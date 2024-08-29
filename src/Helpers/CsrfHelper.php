<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class CsrfHelper
 * @package Schivei\TagHelper\Helpers
 */
class CsrfHelper extends Helper
{
    protected string $targetAttribute = 'csrf';
    protected string $targetElement = 'form';

    /**
     * @throws Exception
     */
    protected function _process(HtmlElement &$element): void
    {
        $element->appendInnerHtml('@csrf');
    }
}
