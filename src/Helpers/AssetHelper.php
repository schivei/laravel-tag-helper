<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class AssetHelper
 *
 * @package Schivei\TagHelper\Helpers
 */
class AssetHelper extends Helper
{
    protected ?string $targetAttribute = 'asset';

    protected string $targetElement = 'a|area|base|link|audio|embed|iframe|img|input|script|source|track|video';

    public function process(HtmlElement $element) : void
    {
        $asset = $element->getAttribute('asset');

        $element->removeAttribute('asset');

        $elementName = $element->getTag();

        $attr = "src";

        if (in_array($elementName, ['a', 'area', 'base', 'link'])) {
            $attr = "href";
        }

        $element->setAttribute($attr, asset($asset));
    }
}
