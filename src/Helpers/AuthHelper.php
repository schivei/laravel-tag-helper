<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class AuthHelper
 * @package Schivei\TagHelper\Helpers
 */
class AuthHelper extends Helper
{
    protected string $targetAttribute = 'auth';
    protected bool $canBeEmpty = true;

    /**
     * @throws Exception
     */
    protected function _process(HtmlElement &$element): void
    {
        $auth = $element->getBladeAttribute('auth');

        $element->prependOuterHtml('@auth(' . $auth . ')');
        $element->appendOuterHtml('@endauth');
    }
}
