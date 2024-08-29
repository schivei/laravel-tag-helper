<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Helpers;

use Exception;
use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

/**
 * Class LinkHelper
 * @package Schivei\TagHelper\Helpers
 */
class LinkHelper extends Helper
{
    protected string $targetElement = 'a';
    protected string $targetAttribute = 'route';
    protected bool $autoRemoveAttribute = true;

    /**
     * @throws Exception
     */
    public function process(HtmlElement &$element): void
    {
        $route = $element->getBladeAttribute('route');

        $params = $element->getBladeAttribute('route-parameters', '[]');

        $params = preg_replace('/^([\'"])?(.*)\1$/', '$2', $params);

        $element->setAttribute('href', "{{ route($route, $params) }}");

        $element->removeAttribute('route-parameters');
    }
}
