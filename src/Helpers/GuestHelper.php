<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class GuestHelper
 * @package Schivei\TagHelper\Helpers
 */
class GuestHelper extends Helper
{
    protected string $targetAttribute = 'guest';
    protected bool $autoRemoveAttribute = true;
    protected bool $canBeEmpty = true;

    /**
     * @throws Exception
     */
    public function process(HtmlElement &$element): void
    {
        $guest = $element->getBladeAttribute('guest');

        $element->prependOuterHtml('@guest(' . $guest . ')');
        $element->appendOuterHtml('@endguest');
    }
}
