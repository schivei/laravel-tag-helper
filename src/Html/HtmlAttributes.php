<?php declare(strict_types=1);

namespace Schivei\TagHelper\Html;

use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\StrictException;

/**
 * Class HtmlAttributes
 * @package Schivei\TagHelper\Html
 */
final class HtmlAttributes
{
    private HtmlElement $_element;
    private HtmlDocument $_root;
    private array $_attributes;
    private bool $_readonly = false;

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     */
    public function __construct(HtmlDocument &$root, ?HtmlElement &$element = null)
    {
        if (!isset($element)) {
            $this->_attributes = [];
            $this->_readonly = true;
            return;
        }

        $this->_element = &$element;

        $this->_root = &$root;

        $this->_attributes = $element->getAttributes();

        if (empty($this->_attributes)) {
            $this->_attributes = [];

            $root->register($element);
            return;
        }

        foreach ($this->_attributes as $name => $_) {
            $root->register($element, $name);
        }
    }

    /**
     * Get an attribute value.
     *
     * @param string $name
     * @return mixed
     */
    public function get(string $name): mixed
    {
        if (!$this->exists($name)) {
            return null;
        }

        return $this->_attributes[$name];
    }

    /**
     * Check if an attribute exists.
     *
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return key_exists($name, $this->_attributes);
    }

    /**
     * Set an attribute value.
     *
     * @param string $name
     * @param mixed $value
     */
    public function set(string $name, mixed $value): void
    {
        if ($this->_readonly) {
            return;
        }

        if (!isset($value)) {
            $this->remove($name);
            return;
        }

        $exists = $this->exists($name);

        $this->_attributes[$name] = $value;

        if (!$exists) {
            $this->_root->register($this->_element, $name);
        }
    }

    /**
     * Remove an attribute.
     *
     * @param string $name
     */
    public function remove(string $name): void
    {
        if ($this->_readonly) {
            return;
        }

        if (!$this->exists($name)) {
            return;
        }

        unset($this->_attributes[$name]);

        $this->_attributes = $this->toArray();

        $this->_root->removeAttribute($name, $this->_element);
    }

    /**
     * Get all attributes.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter($this->_attributes, function ($value) {
            return isset($value);
        });
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Get all attributes as a string.
     *
     * @return string
     */
    public function toString(): string
    {
        $attributes = $this->toArray();

        return implode(' ', array_map(function ($value, $key) {
            return $value === true ? $key : "$key=\"$value\"";
        }, $attributes, array_keys($attributes)));
    }
}
