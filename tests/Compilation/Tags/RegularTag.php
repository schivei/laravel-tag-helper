<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Tests\Compilation\Tags;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class RegularTag extends Helper
{
    protected string $targetElement = 'div';
    protected string $targetAttribute = 'custom-helper';
    protected bool $autoRemoveAttribute = false;

    /**
     * @throws Exception
     */
    protected function _process(HtmlElement &$element): void
    {
        $outer = $element->getOuterHtml();

        if (empty($outer)) {
            return;
        }

        $inner = $element->getInnerHtml();

        if (empty($inner)) {
            return;
        }

        $element->setInnerHtml("Processed");

        $element->appendText('Processed');

        $element->removeAttribute('custom-helper');
    }
}
