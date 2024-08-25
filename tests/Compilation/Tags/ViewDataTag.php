<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Tests\Compilation\Tags;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class ViewDataTag extends Helper
{
    protected string $targetElement = 'div';

    protected ?string $targetAttribute = 'view-data';

    public function process(HtmlElement $element) : void
    {
        $element->appendInnerText('{{'.$element->getAttribute('view-data').'}}');
        $element->removeAttribute('view-data');
    }
}
