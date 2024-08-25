<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class FormMethodHelper
 * @package Schivei\TagHelper\Helpers
 */
class FormMethodHelper extends Helper
{
    protected ?string $targetAttribute = 'method';

    protected string $targetElement = 'form';

    public function process(HtmlElement $element) : void
    {
        $method = strtolower($element->getAttribute('method'));

        if ($method !== 'get' && $method !== 'post') {
            $element->setAttribute('method', 'post');
            $element->appendHtml('<input type="hidden" name="_method" value="'.strtoupper($method).'" />');
        }
    }
}
