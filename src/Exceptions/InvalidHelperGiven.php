<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Exceptions;

/**
 * Class InvalidHelperGiven
 * @package Schivei\TagHelper\Exceptions
 */
class InvalidHelperGiven extends \Exception
{
    public static function withHelper(string $helper) : self
    {
        return new static('Invalid helper class '.$helper);
    }
}
