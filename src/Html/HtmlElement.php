<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Html;

use Exception;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use Schivei\TagHelper\Helper;

/**
 * Class HtmlElement
 * @package Schivei\TagHelper\Html
 */
final class HtmlElement
{
    private HtmlDocument $_doc;

    private HtmlNode $_node;

    private ?Helper $_helper = null;

    public function __construct(HtmlDocument &$doc, HtmlNode &$node)
    {
        $this->_doc = &$doc;
        $this->_node = &$node;
    }

    public function setHelper(?Helper &$helper = null): void
    {
        $this->_helper = &$helper;
    }

    public function getTag(): string
    {
        return $this->_node->getTag()->name();
    }

    /**
     * @throws Exception
     */
    public function getOuterHtml(): string
    {
        return $this->_node->outerHtml();
    }

    /**
     * @throws Exception
     */
    public function getInnerHtml(): string
    {
        return $this->_node->innerHtml();
    }

    /**
     * @throws Exception
     */
    public function setInnerHtml(string $html): void
    {
        $html = $this->_doc->protectValue($html);

        $this->clear();

        $newInner = HtmlDocument::getDom($html)->root->getChildren();

        foreach ($newInner as $child) {
            $this->_node->addChild($child);
        }
    }

    /**
     * @throws Exception
     */
    public function prependInnerHtml(string $html): void
    {
        $indent = $this->getInnerSpace();

        $html = $this->_doc->protectValue("$html\n$indent");

        $newInner = HtmlDocument::getDom($html)->root->getChildren();

        foreach ($newInner as $child) {
            $nodeChild = $this->_node->firstChild();

            $this->_node->insertBefore($child, $nodeChild->id());
        }
    }

    /**
     * @throws Exception
     */
    public function appendInnerHtml(string $html): void
    {
        $indent = $this->getInnerSpace();

        $html = $this->_doc->protectValue("\n$indent$html");

        $newInner = HtmlDocument::getDom($html)->root->getChildren();

        foreach ($newInner as $child) {
            $this->_node->addChild($child);
        }
    }

    /**
     * @throws Exception
     */
    public function prependOuterHtml(string $html): void
    {
        $indent = $this->getOuterSpace();

        $html = $this->_doc->protectValue("$html\n$indent");

        $newInner = HtmlDocument::getDom($html)->root->getChildren();

        $parent = $this->_node->getParent();

        foreach ($newInner as $child) {
            $parent->insertBefore($child, $this->_node->id());
        }
    }

    /**
     * @throws Exception
     */
    public function appendOuterHtml(string $html): void
    {
        $indent = $this->getOuterSpace();

        $html = $this->_doc->protectValue("\n$indent$html");

        $newInner = HtmlDocument::getDom($html)->root->getChildren();

        $parent = $this->_node->getParent();

        foreach ($newInner as $child) {
            $parent->insertAfter($child, $this->_node->id());
        }
    }

    /**
     * @throws Exception
     */
    public function hasAttribute(string $name): bool
    {
        return $this->_node->hasAttribute($name);
    }

    /**
     * @throws Exception
     */
    public function getAttribute(string $name, ?string $default = null): ?string
    {
        if ($this->hasAttribute($name)) {
            return $this->_node->getAttribute($name) ?? $default;
        }

        return $default;
    }

    /**
     * @throws Exception
     */
    public function getBladeAttribute(string $name, ?string $default = null): ?string
    {
        $bladeAttr = "blade-attr-$name";

        if ($this->hasAttribute($bladeAttr)) {
            $attr = $this->getAttribute($bladeAttr) ?? $default;
        } else {
            $value = $this->getAttribute($name) ?? $default;

            $helperAttribute = $this->_helper?->getTargetAttribute() ?? "";
            $canBeEmpty = $this->_helper?->canBeEmpty() ?? false;

            if (empty($value) && empty($default) && $name === $helperAttribute && $canBeEmpty) {
                return $default;
            }

            $attr = !empty($default) && $default === $value ? $value : "'$value'";
        }

        $replaces = [
            '/^(=)(.*)$/' => '$2',
            '/^(")([^"]*)(")$/' => "$2",
        ];

        return preg_replace(array_keys($replaces), array_values($replaces), $attr);
    }

    /**
     * @throws Exception
     */
    public function setAttribute(string $name, string $value): void
    {
        $value = $this->_doc->protectValue($value);

        $this->_node->setAttribute($name, $value);
    }

    /**
     * @throws Exception
     */
    public function removeAttribute(string $name): void
    {
        if ($this->hasAttribute($name)) {
            $this->_node->removeAttribute($name);
        }

        if ($this->hasAttribute("blade-attr-$name")) {
            $this->_node->removeAttribute("blade-attr-$name");
        }
    }

    /**
     * @throws Exception
     */
    public function clear(): void
    {
        $children = $this->_node->getChildren();

        foreach ($children as $child) {
            $child->delete();
        }
    }

    /**
     * @throws Exception
     */
    private function getInnerSpace(): string
    {
        static $found;

        if (!empty($found)) {
            return $found;
        }

        $found = "";

        $children = $this->_node->getChildren();

        foreach ($children as $child) {
            if (empty($found)) {
                if ($child instanceof TextNode) {
                    $text = $child->text();

                    if (!str_contains($text, "___SPACE___")) {
                        continue;
                    }

                    $found .= $text;
                }
            }
        }

        return $found;
    }

    /**
     * @throws Exception
     */
    public function getOuterSpace(): string
    {
        static $found;

        if (!empty($found)) {
            return $found;
        }

        $found = "";

        $parent = $this->_node->getParent();

        $children = $parent->getChildren();

        foreach ($children as $child) {
            if ($child->id() === $this->_node->id()) {
                break;
            }

            if ($child instanceof TextNode) {
                $found .= $child->text();
            }
        }

        return $found;
    }

    /**
     * @throws Exception
     */
    public function appendText(string $text): void
    {
        $indent = $this->getInnerSpace();

        $text = $this->_doc->protectValue("\n$indent$text");

        $txt = new TextNode($text);

        $this->_node->addChild($txt);
    }

    /**
     * @throws Exception
     */
    public function prependText(string $text): void
    {
        $indent = $this->getInnerSpace();

        $text = $this->_doc->protectValue("$text\n$indent");

        $txt = new TextNode($text);

        $child = $this->_node->firstChild();

        $this->_node->insertBefore($txt, $child->id());
    }

    public function __destruct()
    {
        foreach (get_object_vars($this) as $property => $value) {
            unset($this->$property);
        }
    }

    public function removeHelper(): void
    {
        unset($this->_helper);
    }
}
