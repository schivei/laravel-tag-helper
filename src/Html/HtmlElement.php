<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Html;

use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

/**
 * Class HtmlElement
 * @package Schivei\TagHelper\Html
 */
final class HtmlElement extends HtmlNode
{
    private bool $_initialized;
    private string $_content;

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     * @throws NotLoadedException
     */
    public function __construct(int $index, HtmlDocument &$root, ?HtmlElement &$parent, string $content, string $tagName)
    {
        $this->_initialized = false;
        $this->_content = $content;

        if (empty($parent)) {
            $parent = $this;
        }

        parent::__construct($index, $root, $parent, $content, $tagName);

        $this->_initialized = true;
    }

    /**
     * @inheritdoc
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     * @throws NotLoadedException
     * @override
     */
    public function getChildren(?string $content = null): array
    {
        if ($this->_closed) {
            return [];
        }

        if (empty($content) || $this->_initialized) {
            return parent::getChildren();
        }

        $dom = self::_getDom($content);

        if ($dom->root->getTag()->name() !== $this->_tag) {
            $dom = $dom->root;
        }

        /** @var HtmlNode $first */
        $first = null;

        $children = [];

        foreach ($dom->getChildren() as $i => $child) {
            if ($child instanceof TextNode) {
                $children[] = new HtmlText($i, $this->_root, $this, $child->text(), $child->getTag()->name());
                continue;
            }

            $first = $child;
            break;
        }

        if (empty($first)) {
            return $children;
        }

        foreach ($first->getChildren() as $i => $child) {
            if ($child instanceof TextNode) {
                $children[] = new HtmlText($i, $this->_root, $this, $child->text(), $child->getTag()->name());
                continue;
            }

            $children[] = new HtmlElement($i, $this->_root, $this, $child->outerHtml(), $child->getTag()->name());
        }

        return $children;
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     */
    public function getAttributes(): array
    {
        if ($this->_closed) {
            return [];
        }

        if ($this->_initialized) {
            return parent::getAttributes();
        }

        $dom = self::_getDom($this->_content);

        $attributes = $dom->root->firstChild()->getAttributes();

        foreach ($attributes as $key => $value) {
            $attributes[$key] = !isset($value) ? true : $value;
        }

        return $attributes;
    }

    public function removeAttribute(string $key): void
    {
        if ($this->_closed) {
            return;
        }

        $this->_attributes->remove($key);
    }
}
