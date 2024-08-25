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
class ConcatHelper extends Helper
{
    protected ?string $targetAttribute = 'concat';

    protected string $targetElement = '*';

    public function process(HtmlElement $element) : void
    {
        $concat = $element->getAttribute('concat') ?? "";

        if (str_starts_with($concat, '$')) {
            $concat = "{{ $concat }}";

            $content = $element->getInnerHtml() . $concat;

            $element->setInnerHtml($content);

            return;
        }

        $content = $element->getInnerText() . $concat;

        $element->setInnerText($content);
    }
}
