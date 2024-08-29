<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class AssetHelper
 *
 * @package Schivei\TagHelper\Helpers
 */
class AssetHelper extends Helper
{
    protected string $targetAttribute = 'asset';
    protected string $targetElement = 'a|area|base|link|audio|embed|iframe|img|input|script|source|track|video';
    protected bool $autoRemoveAttribute = true;

    /**
     * @throws Exception
     */
    public function process(HtmlElement &$element): void
    {
        $attr = $element->getBladeAttribute('asset');

        $asset = trim("$attr");

        $elementName = $element->getTag();

        $dest = "src";

        if (in_array($elementName, ['a', 'area', 'base', 'link'])) {
            $dest = "href";
        }

        $element->setAttribute($dest, "{{ asset($asset) }}");
    }
}
