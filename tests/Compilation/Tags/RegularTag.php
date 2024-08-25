<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Tests\Compilation\Tags;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class RegularTag extends Helper
{
    protected string $targetElement = 'div';

    protected ?string $targetAttribute = 'custom-helper';

    public function process(HtmlElement $element) : void
    {
        $element->appendInnerText('Processed');
        $element->removeAttribute('custom-helper');
    }
}
