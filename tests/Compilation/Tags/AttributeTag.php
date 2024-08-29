<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Tests\Compilation\Tags;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class AttributeTag extends Helper
{
    protected string $targetAttribute = 'custom-helper';

    /**
     * @throws Exception
     */
    protected function _process(HtmlElement &$element): void
    {
        $element->setInnerHtml("Processed 0");

        $element->appendText('Processed 1');

        $element->prependInnerHtml('Processed 2');

        $element->prependOuterHtml('Processed 3');

        $element->appendOuterHtml('Processed 4');

        $element->appendInnerHtml('Processed 5');

        $element->prependText('Processed 6');
    }
}
