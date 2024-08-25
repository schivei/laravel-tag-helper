<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TagHelper
 *
 * @method static void helper(string $helper)
 * @method static array getRegisteredTagHelpers()
 */
class TagHelper extends Facade
{
    protected static function getFacadeAccessor() : string
    {
        return 'tag-helper';
    }
}
