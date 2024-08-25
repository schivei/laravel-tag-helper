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
     * @var string|null The target attribute for this helper
     */
    protected ?string $targetAttribute = null;

    /**
     * Get the target element for this helper
     *
     * @return string The target element for this helper
     */
    public function getTargetElement(): string
    {
        return $this->targetElement;
    }

    /**
     * Get the target attribute for this helper
     *
     * @return string|null The target attribute for this helper
     */
    public function getTargetAttribute(): ?string
    {
        return $this->targetAttribute;
    }

    /**
     * Process the element
     *
     * @param HtmlElement $element The element to process
     * @return void
     */
    abstract public function process(HtmlElement $element) : void;
}
