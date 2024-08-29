<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Tests\Compilation\Tags;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class ViewDataTag extends Helper
{
    protected string $targetElement = 'div';

    protected string $targetAttribute = 'view-data';
    protected bool $autoRemoveAttribute = false;

    /**
     * @throws Exception
     */
    protected function _process(HtmlElement &$element): void
    {
        $element->appendText('{{' . $element->getAttribute('view-data') . '}}');
        $element->removeAttribute('view-data');
    }
}
