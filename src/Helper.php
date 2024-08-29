<?php
declare(strict_types=1);

namespace Schivei\TagHelper;

use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class Helper
 * @package Schivei\TagHelper
 */
abstract class Helper
{
    /**
     * The target element for this helper
     *
     * @var string The target element for this helper
     */
    protected string $targetElement = '*';

    /**
     * The target attribute for this helper
     *
     * @var string The target attribute for this helper
     */
    protected string $targetAttribute;

    /**
     * Whether to automatically remove the target attribute after processing
     *
     * @var bool
     */
    protected bool $autoRemoveAttribute = false;

    /**
     * Whether the target attribute is a boolean attribute
     *
     * @var bool
     */
    protected bool $canBeEmpty = false;

    /**
     * Get the target element for this helper
     *
     * @return string The target element for this helper
     */
    final public function getTargetElement(): string
    {
        return $this->targetElement;
    }

    /**
     * Get the target attribute for this helper
     *
     * @return string The target attribute for this helper
     */
    final public function getTargetAttribute(): string
    {
        return $this->targetAttribute;
    }

    /**
     * Get whether to automatically remove the target attribute after processing
     *
     * @return bool
     */
    final public function getAutoRemoveAttribute(): bool
    {
        return $this->autoRemoveAttribute;
    }

    /**
     * Process the element
     *
     * @param HtmlElement $element The element to process
     * @return void
     */
    abstract public function process(HtmlElement &$element): void;

    public function canBeEmpty(): bool
    {
        return $this->canBeEmpty;
    }
}
