<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class ConditionHelper
 * @package Schivei\TagHelper\Helpers
 */
class ConditionHelper extends Helper
{
    protected ?string $targetAttribute = 'if';

    public function process(HtmlElement $element) : void
    {
        $condition = $element->getAttribute('if');

        $toContent = $element->hasAttribute('to-content');

        $element->removeAttribute('if');

        if ($toContent) {
            $element->removeAttribute('to-content');

            $element->prependHtml('@if('.$condition.')');
            $element->appendHtml('@endif');

            return;
        }

        $outerHtml = '@if('.$condition.') ';
        $outerHtml .= $element;
        $outerHtml .= ' @endif';

        $element->setOuterHtml($outerHtml);
    }
}
