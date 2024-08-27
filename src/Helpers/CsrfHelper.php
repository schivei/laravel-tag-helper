<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class CsrfHelper
 * @package Schivei\TagHelper\Helpers
 */
class CsrfHelper extends Helper
{
    protected ?string $targetAttribute = 'csrf';

    protected string $targetElement = 'form';

    public function process(HtmlElement $element) : void
    {
        $element->removeAttribute('csrf');

        $element->appendText('@csrf');
    }
}
