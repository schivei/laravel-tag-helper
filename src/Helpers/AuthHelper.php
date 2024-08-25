<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class AuthHelper
 * @package Schivei\TagHelper\Helpers
 */
class AuthHelper extends Helper
{
    protected ?string $targetAttribute = 'auth';

    public function process(HtmlElement $element) : void
    {
        $auth = $element->getAttributeForBlade('auth');

        if (empty($auth) || "'auth'" === $auth || "':auth'" === $auth) {
            $auth = null;
        }

        $element->removeAttribute('auth');

        $outerHtml = '@auth('.$auth.') ';
        $outerHtml .= $element->getOuterHtml();
        $outerHtml .= ' @endauth';

        $element->setOuterHtml($outerHtml);
    }
}
