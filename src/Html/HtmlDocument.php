<?php

namespace Schivei\TagHelper\Html;

use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\StrictException;

class HtmlDocument
{
    protected string $content;
    /**
     * @var HtmlElement[][] $allElements
     */
    protected array $allElements = [];
    /**
     * @var HtmlElement[][][] $allAttributes
     */
    protected array $allAttributes = [];

    private HtmlElement $_root;

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     */
    public function __construct(string $content)
    {
        $this->content = $content;

        $this->_parse();
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     */
    private function _parse(): void
    {
        $this->_root = new HtmlElement(0, $this, $root, $this->content, 'root');
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     */
    public static function parse(string $content): HtmlDocument
    {
        return new HtmlDocument($content);
    }

    public function __toString()
    {
        $this->updateDocument();

        $beautify = new Beautifier(array(
            'indent_inner_html' => true,
            'indent_char' => " ",
            'indent_size' => 2,
            'wrap_line_length' => 32786,
            'unformatted' => ['code', 'pre'],
            'preserve_newlines' => false,
            'max_preserve_newlines' => 32786,
            'indent_scripts' => 'normal', // keep|separate|normal
        ));

        return $beautify->beautify($this->content);
    }

    public function updateDocument(): void
    {
        $this->content = $this->_root->getInnerHtml();
    }

    /**
     * @return HtmlElement[]
     */
    public function find(array $elements, ?string $attribute): array
    {
        if (!empty($attribute)) {
            if (!array_key_exists($attribute, $this->allAttributes)) {
                return [];
            }

            $components = $this->allAttributes[$attribute];

            if ($elements[0] === '*') {
                return array_merge(...array_values($components));
            }

            return array_merge(...array_values(array_filter($components, function ($key) use ($elements) {
                return in_array($key, $elements);
            }, ARRAY_FILTER_USE_KEY)));
        }

        if ($elements[0] === '*') {
            return $this->allElements;
        }

        return array_merge(...array_values(array_filter($this->allElements, function ($key) use ($elements) {
            return in_array($key, $elements);
        }, ARRAY_FILTER_USE_KEY)));
    }

    public function register(HtmlElement &$element, ?string $attribute = null): void
    {
        if (!array_key_exists($element->getTag(), $this->allElements)) {
            $this->allElements[$element->getTag()] = [];
        }

        $this->allElements[$element->getTag()][] = &$element;

        if (!empty($attribute)) {
            if (!array_key_exists($attribute, $this->allAttributes)) {
                $this->allAttributes[$attribute] = [];
            }

            if (!array_key_exists($element->getTag(), $this->allAttributes[$attribute])) {
                $this->allAttributes[$attribute][$element->getTag()] = [];
            }

            $this->allAttributes[$attribute][$element->getTag()][] = &$element;
        }
    }

    public function removeAttribute(string $key, HtmlElement &$element): void
    {
        if (!array_key_exists($key, $this->allAttributes)) {
            return;
        }

        if (!array_key_exists($element->getTag(), $this->allAttributes[$key])) {
            return;
        }

        unset($this->allAttributes[$key][$element->getTag()]);

        $this->allAttributes[$key] = array_filter($this->allAttributes[$key]);

        if (empty($this->allAttributes[$key])) {
            unset($this->allAttributes[$key]);
        }

        $this->allAttributes = array_filter($this->allAttributes);

        if (empty($this->allAttributes)) {
            unset($this->allAttributes);
        }

        $this->allAttributes = [];
    }
}
