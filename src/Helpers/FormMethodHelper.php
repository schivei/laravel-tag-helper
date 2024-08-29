<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class FormMethodHelper
 * @package Schivei\TagHelper\Helpers
 */
class FormMethodHelper extends Helper
{
    protected string $targetAttribute = 'method';
    protected string $targetElement = 'form';

    /**
     * @throws Exception
     */
    public function process(HtmlElement &$element): void
    {
        $method = strtolower($element->getAttribute('method'));

        if ($method !== 'get' && $method !== 'post') {
            $method = strtoupper($method);
            $element->setAttribute('method', 'post');
            $element->appendInnerHtml("<input type=\"hidden\" name=\"_method\" value=\"$method\" />");
        }
    }
}
