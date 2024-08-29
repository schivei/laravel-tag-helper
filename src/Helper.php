<?php
declare(strict_types=1);

namespace Schivei\TagHelper;

use Exception;
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
    protected bool $autoRemoveAttribute = true;

    /**
     * Whether the target attribute is a boolean attribute
     *
     * @var bool
     */
    protected bool $canBeEmpty = false;

    /**
     * Get the target element for this helper
     * @return string The target element for this helper
     *@internal This method should not be overridden
     *
     */
    public final function getTargetElement(): string
    {
        return $this->targetElement;
    }

    /**
     * Get the target attribute for this helper
     * @return string The target attribute for this helper
     *@internal This method should not be overridden
     *
     */
    public final function getTargetAttribute(): string
    {
        return $this->targetAttribute;
    }

    /**
     * Get whether to automatically remove the target attribute after processing
     * @return bool
     * @internal This method should not be overridden
     *
     */
    public final function getAutoRemoveAttribute(): bool
    {
        return $this->autoRemoveAttribute;
    }

    /**
     * Process the element
     *
     * @param HtmlElement $element The element to process
     * @return void
     * @throws Exception
     */
    abstract protected function _process(HtmlElement &$element): void;

    /**
     * Process the element
     * @internal This method should not be overridden
     *
     * @param HtmlElement $element The element to process
     * @return void
     * @throws Exception
     */
    public final function process(HtmlElement &$element): void
    {
        $element->setHelper($this);

        $this->_process($element);

        if ($this->autoRemoveAttribute) {
            $element->removeAttribute($this->targetAttribute);
        }

        $element->removeHelper();
    }

    /**
     * Get whether the target attribute is a boolean attribute
     * @return bool
     * @internal This method should not be overridden
     *
     */
    public final function canBeEmpty(): bool
    {
        return $this->canBeEmpty;
    }
}
