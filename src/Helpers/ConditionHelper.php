<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class ConditionHelper
 * @package Schivei\TagHelper\Helpers
 */
class ConditionHelper extends Helper
{
    protected ?string $targetAttribute = 'if';

    /**
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     * @throws NotLoadedException
     */
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

        $element->setBefore('@if(' . $condition . ')');
        $element->setAfter('@endif');
    }
}
