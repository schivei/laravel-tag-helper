<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class LinkHelper
 * @package Schivei\TagHelper\Helpers
 */
class LinkHelper extends Helper
{
    protected string $targetElement = 'a';

    protected ?string $targetAttribute = 'route';

    public function process(HtmlElement $element) : void
    {
        $element->setTag('a');

        $element->setAttribute('href', route($element->getAttributeForBlade('route'), json_decode($element->getAttributeForBlade('route-parameters', '[]'))));

        $element->removeAttribute('route');
        $element->removeAttribute('route-parameters');
    }
}
