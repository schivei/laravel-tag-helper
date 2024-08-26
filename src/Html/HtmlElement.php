<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Html;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use PHPHtmlParser\Exceptions\UnknownChildTypeException;

/**
 * Class HtmlElement
 * @package Schivei\TagHelper\Html
 */
class HtmlElement
{
    protected HtmlNode $domNode;

    protected Dom $doc;

    public static function create(Dom &$doc, HtmlNode &$node): self
    {
        return new static($doc, $node);
    }

    public function __construct(Dom &$doc, HtmlNode &$domNode)
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

    public function setAttribute(string $attribute, $value): void
    {
        $attr = $this->domNode->setAttribute($attribute, $value);
    }

    public function removeAttribute(string $attribute) : void
    {
        $this->domNode->removeAttribute($attribute);
    }

    /**
     * @throws UnknownChildTypeException
     * @throws ChildNotFoundException
     */
    public function getOuterHtml() : string
    {
        return $this->domNode->outerHtml();
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     * @throws NotLoadedException
     */
    public function setBefore(string $html): void
    {
        $doc = new Dom();
        $doc->loadStr("<root_dom>$html</root_dom>");

        $elements = $doc->find('root_dom')[0]->getChildren();

        foreach ($elements as $element) {
            $this->domNode->parent->insertBefore($element, $this->domNode->id());
        }
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     * @throws NotLoadedException
     */
    public function setAfter(string $html): void
    {
        $doc = new Dom();
        $doc->loadStr("<root_dom>$html</root_dom>");

        $elements = $doc->find('root_dom')[0]->getChildren();

        foreach ($elements as $element) {
            $this->domNode->parent->insertAfter($element, $this->domNode->id());
        }
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     * @throws NotLoadedException
     */
    public function setOuterHtml(string $html) : void
    {
        $doc = new Dom();
        $doc->loadStr("<root_dom>$html</root_dom>");

        $elements = $doc->find('root_dom')[0]->getChildren();

        $id = $this->domNode->id();
        foreach ($elements as $element) {
            $this->domNode->parent->insertAfter($element, $id);
            $id = $element->id();

            if (!isset($el) && $element instanceof HtmlNode) {
                $el = $element;
            }
        }

        $this->domNode->delete();

        if (isset($el)) {
            $this->domNode = &$el;
        } else {
            unset($this->domNode);
        }
    }

    /**
     * @throws UnknownChildTypeException
     * @throws ChildNotFoundException
     */
    public function getInnerHtml() : string
    {
        return $this->domNode->innerHtml();
    }

    /**
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws StrictException
     */
    public function setInnerHtml(string $html) : void
    {
        $doc = new Dom();
        $doc->loadStr("<root_dom>$html</root_dom>");

        $elements = $doc->find('root_dom')[0]->getChildren();

        $this->clearChildren();

        foreach ($elements as /** @type HtmlNode $element */ $element) {
            $this->domNode->addChild($element);
        }
    }

    /**
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws StrictException
     */
    public function appendHtml(string $html) : void
    {
        $doc = new Dom();
        $doc->loadStr("<root_dom>$html</root_dom>");

        $elements = $doc->find('root_dom')[0]->getChildren();

        foreach ($elements as /** @type HtmlNode $element */ $element) {
            $this->domNode->addChild($element);
        }
    }

    /**
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws StrictException
     */
    public function prependHtml(string $html) : void
    {
        $doc = new Dom();
        $doc->loadStr("<root_dom>$html</root_dom>");

        $elements = $doc->find('root_dom')[0]->getChildren();

        $firstChild = $this->domNode->firstChild();

        foreach ($elements as /** @type HtmlNode $element */ $element) {
            $this->domNode->insertBefore($element, $firstChild->id());
        }
    }

    public function getInnerText(): string
    {
        return $this->domNode->text();
    }

    /**
     * @throws LogicalException
     * @throws CircularException
     * @throws ChildNotFoundException
     */
    public function setInnerText(string $text) : void
    {
        $txt = new TextNode($text, false);

        $this->clearChildren();

        $this->domNode->addChild($txt);
    }

    /**
     * @throws CircularException
     */
    public function clearChildren(): void
    {
        foreach ($this->domNode->getChildren() as /** @type HtmlNode $child */ $child) {
            $this->domNode->removeChild($child->id());
        }
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws LogicalException
     */
    public function prependInnerText(string $prepend) : void
    {
        $this->setInnerText($prepend.$this->getInnerText());
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws LogicalException
     */
    public function appendInnerText(string $append) : void
    {
        $this->setInnerText($this->getInnerText().$append);
    }

    public function getTag() : string
    {
        return $this->domNode->getTag()->name();
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     */
    public function setTag(string $tag) : void
    {
        $node = new HtmlNode($tag);

        foreach ($this->domNode->getAttributes() as $key => $value) {
            $node->setAttribute($key, $value);
        }

        $this->domNode->parent->insertAfter($node, $this->domNode->id());
        $this->domNode->delete();

        $this->domNode = &$node;
    }

    public function __toString() : string
    {
        return $this->getOuterHtml();
    }
}
