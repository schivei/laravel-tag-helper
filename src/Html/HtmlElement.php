<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Html;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use FluentDOM\Loader\Options;

/**
 * Class HtmlElement
 * @package Schivei\TagHelper\Html
 */
class HtmlElement
{
    protected DOMElement $domNode;

    protected DOMDocument $doc;

    public static function create(DOMDocument &$doc, DOMElement &$node) : self
    {
        return new static($doc, $node);
    }

    public function __construct(DOMDocument &$doc, DOMElement &$domNode)
    {
        $this->doc = &$doc;
        $this->domNode = &$domNode;
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->domNode->hasAttribute($attribute);
    }

    public function getAttribute(string $attribute, $default = null) : string
    {
        if ($this->hasAttribute($attribute)) {
            return $this->domNode->getAttribute($attribute) ?? $default ?? '';
        }

        return $default ?? '';
    }

    public function getAttributeForBlade(string $attribute, $default = null) : string
    {
        $result = $this->getAttribute(":$attribute", $default);

        $result = !empty($result) ? $result : $this->getAttribute($attribute, $default);

        return (!empty($result) ? $result : $default) ?? "'$attribute'";
    }

    public function setAttribute(string $attribute, string $value) : void
    {
        $this->domNode->setAttribute($attribute, $value);
    }

    public function removeAttribute(string $attribute) : void
    {
        $this->domNode->removeAttribute($attribute);
    }

    public function getOuterHtml() : string
    {
        return $this->doc->saveHTML($this->domNode);
    }

    public function setOuterHtml(string $html) : void
    {
        $fragment = $this->doc->createDocumentFragment();
        $fragment->appendXML($html);

        $this->domNode->replaceWith($fragment);
    }

    public function getInnerHtml() : string
    {
        $inners = [];

        foreach ($this->domNode->childNodes as $child) {
            $inners[] = $this->doc->saveHTML($child);
        }

        return implode("\n", $inners);
    }

    public function setInnerHtml(string $html) : void
    {
        $fragment = $this->doc->createDocumentFragment();
        $fragment->appendXML($html);

        $this->domNode->nodeValue = '';
        $this->domNode->appendChild($fragment);
    }

    public function appendHtml(string $html) : void
    {
        $fragment = $this->doc->createDocumentFragment();
        $fragment->appendXML($html);

        $this->domNode->appendChild($fragment);
    }

    public function prependHtml(string $html) : void
    {
        $fragment = $this->doc->createDocumentFragment();
        $fragment->appendXML($html);

        $this->domNode->insertBefore($fragment, $this->domNode->firstChild);
    }

    public function getInnerText(): string
    {
        return $this->domNode->textContent;
    }

    public function setInnerText(string $text) : void
    {
        $this->domNode->textContent = $text;
    }

    public function prependInnerText(string $prepend) : void
    {
        $this->setInnerText($prepend.$this->getInnerText());
    }

    public function appendInnerText(string $append) : void
    {
        $this->setInnerText($this->getInnerText().$append);
    }

    public function getTag() : string
    {
        return $this->domNode->nodeName;
    }

    /**
     * @throws DOMException
     */
    public function setTag(string $tag) : void
    {
        $el = $this->doc->createElement($tag);

        foreach ($this->domNode->childNodes as $child) {
            $el->appendChild($child->cloneNode(true));
        }

        foreach ($this->domNode->attributes as $attribute) {
            $el->setAttribute($attribute->nodeName, $attribute->nodeValue);
        }

        $this->domNode->parentNode->replaceChild($el, $this->domNode);
    }

    public function __toString() : string
    {
        return $this->getOuterHtml();
    }
}
