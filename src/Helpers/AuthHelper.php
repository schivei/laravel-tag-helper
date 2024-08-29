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
    protected bool $autoRemoveAttribute = true;
    protected bool $canBeEmpty = true;

    /**
     * @throws Exception
     */
    public function process(HtmlElement &$element): void
    {
        $auth = $element->getBladeAttribute('auth');

        $element->prependOuterHtml('@auth(' . $auth . ')');
        $element->appendOuterHtml('@endauth');
    }
}
