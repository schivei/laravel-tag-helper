<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class GuestHelper
 * @package Schivei\TagHelper\Helpers
 */
class GuestHelper extends Helper
{
    protected ?string $targetAttribute = 'guest';

    public function process(HtmlElement $element) : void
    {
        $guest = $element->getAttributeForBlade('guest');

        if (empty($guest) || "'guest'" === $guest || "':guest'" === $guest) {
            $guest = null;
        }

        $element->removeAttribute('guest');

        $outerHtml = '@guest('.$guest.') ';
        $outerHtml .= $element->getOuterHtml();
        $outerHtml .= ' @endguest';

        $element->setOuterHtml($outerHtml);
    }
}
