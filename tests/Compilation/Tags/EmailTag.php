<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Tests\Compilation\Tags;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class EmailTag extends Helper
{
    protected string $targetElement = 'custom-email';

    /**
     * @throws Exception
     */
    public function process(HtmlElement $element) : void
    {
        $element->setTag('div');
        $element->prependText('This is a custom email tag helper.');
        $element->prependText('This is a custom email tag helper 0.');
    }
}
