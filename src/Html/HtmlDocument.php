<?php

namespace Schivei\TagHelper\Html;

use Exception;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use Schivei\TagHelper\Helper;

final class HtmlDocument
{
    /**
     * @var array<string, string> $_protectedValues
     */
    private array $_protectedValues;

    /**
     * @var array<string, HtmlElement[]> $_autoRemoveAttributes
     */
    private array $_autoRemoveAttributes;

    private Dom $_dom;

    private string $_content;

    /**
     * @throws Exception
     */
    public function __construct(string $content)
    {
        $this->_content = $content;
        $this->_protectedValues = [];
        $this->_autoRemoveAttributes = [];

        $this->_parse();
    }

    /**
     * @throws Exception
     */
    private function _parse(): void
    {
        $this->_content = $this->protectValue($this->_content);

        $this->_dom = &self::getDom($this->_content);
    }

    /**
     * @throws Exception
     */
    public static function &getDom(?string $content = null): Dom
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

    /**
     * @throws Exception
     */
    public static function parse(string $content): HtmlDocument
    {
        return new HtmlDocument($content);
    }

    private function _getKey(): string
    {
        return uniqid('___PROTECTED_VALUE_', true) . '___';
    }

    public function protectValue(string $value, bool $skipBlade = false): string
    {
        if (!$skipBlade) {
            $regexBladeAttributes = '/
                (?<attr>(?<preceding_char>[:])(?<attr_name>\w+))(?<equal>\s*=)
            /isx';

            preg_match_all($regexBladeAttributes, $value, $matches);

            foreach ($matches['attr_name'] as $index => $attrName) {
                $newName = "blade-attr-$attrName";

                $value = str_replace($matches['attr'][$index] . $matches['equal'][$index], $newName . $matches['equal'][$index], $value);
            }
        }

        $replacers = [
            '/\n/' => '___NEW_LINE___',
            '/\t/' => '___SPACE___',
            '/\s\s/' => '___SPACE___',
        ];

        $value = preg_replace(array_keys($replacers), array_values($replacers), $value);

        $regexProtectedValues = '/
            (?<precedingChar>[=\s])?
            (?<protectedValues>
                (?<comment><!--.*?-->) |
                (?<php><\?(=|php)?.*?\?>) |
                (?<multi_bracket>\{[\{]+[^\}]*[\}]+[\}]) |
                (?<blade_directive>@[\w]+(\(.*?\))?) |
            )
        /isx';

        preg_match_all($regexProtectedValues, $value, $matches);

        foreach ($matches['protectedValues'] as $index => $protectedValue) {
            if (empty($protectedValue)) {
                continue;
            }

            $key = $this->_getKey();

            $value = str_replace($protectedValue, $key, $value);
            $this->_protectedValues[$key] = $protectedValue;
        }

        return $value;
    }

    /**
     * @throws Exception
     */
    public function toString(): string
    {
        $root = $this->_dom->root;

        foreach ($this->_autoRemoveAttributes as $attribute => $elements) {
            foreach ($elements as $element) {
                $element->removeAttribute($attribute);
            }
        }

        $this->_content = $root->innerHtml();

        $replacers = [
            '___NEW_LINE___' => "\n",
            '___SPACE___' => '  ',
        ];

        $this->_content = str_replace(array_keys($replacers), array_values($replacers), $this->_content);

        foreach ($this->_protectedValues as $key => $protectedValue) {
            $this->_content = str_replace($key, $protectedValue, $this->_content);
        }

        return $this->_content;
    }

    /**
     * @return HtmlElement[]
     * @throws Exception
     */
    public function find(Helper $helper): array
    {
        /** @var HtmlElement[] $foundElements */
        $foundElements = [];

        $elements = explode('|', $helper->getTargetElement());
        $attribute = $helper->getTargetAttribute();
        $attributes = [$attribute, "blade-attr-$attribute"];

        foreach ($elements as $element) {
            foreach ($attributes as $attr) {
                $selector = $element . "[$attr]";

                $found = $this->_dom->find($selector);

                if ($found->count() > 0) {
                    /** @var HtmlNode $node */
                    foreach ($found as &$node) {
                        $el = new HtmlElement($this, $node);
                        $foundElements[] = $el;

                        if ($helper->getAutoRemoveAttribute()) {
                            $this->_autoRemoveAttributes[$attribute][] = &$el;
                        }
                    }
                }
            }
        }

        return $foundElements;
    }

    public function __destruct()
    {
        foreach (get_object_vars($this) as $property => $value) {
            unset($this->$property);
        }
    }
}
