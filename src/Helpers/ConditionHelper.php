<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class ConditionHelper
 * @package Schivei\TagHelper\Helpers
 */
class ConditionHelper extends Helper
{
    protected string $targetAttribute = 'if';

    /**
     * @throws Exception
     */
    protected function _process(HtmlElement &$element): void
    {
        $condition = $element->getBladeAttribute('if');

        $condition = preg_replace('/^([\'"])?(.*)\1$/', '$2', $condition);

        $element->prependOuterHtml('@if(' . $condition . ')');
        $element->appendOuterHtml('@endif');
    }
}
