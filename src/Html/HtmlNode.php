<?php

namespace Schivei\TagHelper\Html;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

abstract class HtmlNode
{
    private const SELF_CLOSING_TAGS = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'
    ];

    protected bool $_closed = false;
    protected int $_index;
    protected HtmlDocument $_root;
    protected HtmlElement $_parent;
    protected ?string $_tag;
    protected HtmlAttributes $_attributes;
    /** @var HtmlNode[] */
    protected array $_children = [];

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     * @throws NotLoadedException
     */
    public function __construct(int $index, HtmlDocument &$root, HtmlElement &$parent, string $content, string $tag)
    {
        $this->_index = $index;
        $this->_root = $root;
        $this->_parent = $parent;
        $this->_tag = $tag;

        if ($this instanceof HtmlElement) {
            $this->_attributes = new HtmlAttributes($root, $this);
            $this->_children = $this->getChildren($content);
        } else {
            $this->_attributes = new HtmlAttributes($root);
            $this->_children = [];
        }
    }

    /**
     * @abstract
     * @param string|null $content
     * @return array|HtmlNode[]
     */
    public function getChildren(?string $content = null): array
    {
        if ($this->_closed) {
            return [];
        }

        return $this->_children;
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     */
    protected static function _getDom(?string $content = null): Dom
    {
        $dom = new Dom();

        $dom->loadStr($content, [
            'strict' => false,
            'removeScripts' => false,
            'removeStyles' => false,
            'removeComments' => false,
            'removeSmartyScripts' => false,
        ]);

        return $dom;
    }

    public function getTag(): ?string
    {
        if ($this->_closed) {
            return null;
        }

        return $this->_tag;
    }

    public function setTag(string $tag): void
    {
        if ($this->_closed) {
            return;
        }

        if (!($this instanceof HtmlElement)) {
            return;
        }

        $this->_tag = $tag;
    }

    public function getAttributes(): array
    {
        if ($this->_closed) {
            return [];
        }

        return $this->_attributes->toArray();
    }

    public function hasAttribute(string $attribute): bool
    {
        if ($this->_closed) {
            return false;
        }

        return $this->_attributes->exists($attribute);
    }

    public function getAttributeForBlade(string $attribute, mixed $default = null): mixed
    {
        if ($this->_closed) {
            return null;
        }

        $result = $this->getAttribute(":$attribute", $default);

        $result = !empty($result) ? $result : $this->getAttribute($attribute, $default);

        return (!empty($result) ? $result : $default) ?? "'$attribute'";
    }

    public function getAttribute(string $attribute, mixed $default = null): mixed
    {
        if ($this->_closed) {
            return null;
        }

        $value = $this->_attributes->get($attribute);

        return !empty($value) ? $value : $default;
    }

    public function setAttribute(string $attribute, mixed $value): void
    {
        if ($this->_closed) {
            return;
        }

        $this->_attributes->set($attribute, $value);
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     */
    public function setBefore(string $html): void
    {
        if ($this->_closed) {
            return;
        }

        $children = $this->_parent->getChildren($html);

        array_splice($this->_parent->_children, $this->_index, 0, $children);
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     */
    public function setAfter(string $html): void
    {
        if ($this->_closed) {
            return;
        }

        $children = $this->_parent->getChildren($html);

        array_splice($this->_parent->_children, $this->_index + 1, 0, $children);
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     */
    public function setOuterHtml(string $html): void
    {
        if ($this->_closed) {
            return;
        }

        $children = $this->_parent->getChildren($html);

        array_splice($this->_parent->_children, $this->_index, 1, $children);

        $this->_close();
    }

    private function _close(): void
    {
        unset($this->_parent);
        $this->_closed = true;
    }

    public function getInnerHtml(): string
    {
        if ($this->_closed) {
            return '';
        }

        if ($this instanceof HtmlElement) {
            return implode(" ", array_map(function ($child) {
                return $child->getOuterHtml();
            }, $this->_children));
        }

        /** @var HtmlText $this */
        return $this->getInnerText();
    }

    public function getOuterHtml(): string
    {
        if ($this->_closed) {
            return '';
        }

        return $this->toString();
    }

    public function toString(): string
    {
        $attributes = $this->_attributes->toString();

        $eol = PHP_EOL;

        if ($this instanceof HtmlElement) {
            $innerHtml = $this->getInnerHtml();

            if ($this->isSelfClosing()) {
                return "<$this->_tag $attributes />";
            }

            return "<$this->_tag $attributes>$innerHtml$eol</$this->_tag>";
        }

        return $this->getInnerHtml();
    }

    public function isSelfClosing(): bool
    {
        return in_array($this->_tag, self::SELF_CLOSING_TAGS);
    }

    public function getInnerText(): string
    {
        if ($this->_closed) {
            return '';
        }

        if ($this instanceof HtmlElement) {
            return implode(" ", array_map(function ($child) {
                return $child->getInnerText();
            }, $this->_children));
        }

        /** @var HtmlText $this */
        return $this->getText();
    }

    public function setInnerHtml(string $html): void
    {
        if ($this->_closed) {
            return;
        }

        $this->clear();

        $children = $this->getChildren($html);

        foreach ($children as $child) {
            $this->append($child);
        }
    }

    public function clear(): void
    {
        if ($this->_closed) {
            return;
        }

        foreach ($this->_children as $child) {
            $child->remove();
        }
    }

    public function remove(?HtmlNode $node = null): void
    {
        if ($this->_closed) {
            return;
        }

        if (!isset($node)) {
            $this->_parent->remove($this);
            return;
        }

        $index = array_search($node, $this->_children, true);

        if ($index === false) {
            return;
        }

        unset($this->_children[$index]);

        $this->_children = array_values($this->_children);

        $node->_close();
    }

    public function append(HtmlNode $node): void
    {
        if ($this instanceof HtmlText) {
            return;
        }

        if ($this->isSelfClosing()) {
            return;
        }

        if (isset($node->_parent)) {
            $node->remove();
        }

        $this->_children[] = $node;

        /** @var HtmlElement $this */
        $node->_parent = $this;
        $node->_closed = false;
    }

    public function appendHtml(string $html): void
    {
        if ($this->_closed) {
            return;
        }

        $children = $this->getChildren($html);

        foreach ($children as $child) {
            $this->append($child);
        }
    }

    public function prependHtml(string $html): void
    {
        if ($this->_closed) {
            return;
        }

        $children = $this->getChildren($html);

        foreach ($children as $child) {
            $this->prepend($child);
        }
    }

    public function prepend(HtmlNode $node): void
    {
        if ($this instanceof HtmlText) {
            return;
        }

        if ($this->isSelfClosing()) {
            return;
        }

        if (isset($node->_parent)) {
            $node->remove();
        }

        array_unshift($this->_children, $node);

        /** @var HtmlElement $this */
        $node->_parent = $this;
        $node->_closed = false;
    }

    public function setInnerText(string $text): void
    {
        if ($this->_closed) {
            return;
        }

        if ($this instanceof HtmlText) {
            $this->setText($text);
            return;
        }

        $this->clear();

        $children = $this->getChildren($text);

        foreach ($children as $child) {
            $this->append($child);
        }
    }

    /**
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws StrictException
     */
    public function appendText(string $text): void
    {
        if ($this->_closed) {
            return;
        }

        if ($this instanceof HtmlElement) {
            $this->append(new HtmlText(0, $this->_root, $this, $text, 'text'));
        }
    }

    /**
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws StrictException
     */
    public function prependText(string $text): void
    {
        if ($this->_closed) {
            return;
        }

        if ($this instanceof HtmlElement) {
            $this->prepend(new HtmlText(0, $this->_root, $this, $text, 'text'));
        }
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
