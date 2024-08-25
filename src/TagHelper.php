<?php
declare(strict_types=1);

namespace Schivei\TagHelper;

use Schivei\TagHelper\Exceptions\InvalidHelperGiven;

/**
 * Class TagHelper
 * @package Schivei\TagHelper
 */
class TagHelper
{
    /**
     * Registered tag helpers
     *
     * @var string[]
     */
    public array $registeredTagHelpers = [];

    /**
     * Get all registered tag helpers
     *
     * @return Helper[]
     */
    public function getRegisteredTagHelpers(): array
    {
        return array_map(function ($helper) {
            return app($helper);
        }, $this->registeredTagHelpers);
    }

    /**
     * Register a tag helper
     *
     * @param string $helper
     * @return void
     * @throws InvalidHelperGiven
     */
    public function helper(string $helper): void
    {
        if (!is_subclass_of($helper, Helper::class)) {
            throw InvalidHelperGiven::withHelper($helper);
        }

        $this->registeredTagHelpers[] = $helper;
    }
}
