<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Tests;

use Schivei\TagHelper\Facades\TagHelper;
use Schivei\TagHelper\Exceptions\InvalidHelperGiven;
use Schivei\TagHelper\Tests\Compilation\Tags\RegularTag;

class RegistrationTest extends TestCase
{
    /** @test */
    public function it_can_register_tag_helpers()
    {
        $initialCount = count(TagHelper::getRegisteredTagHelpers());

        TagHelper::helper(RegularTag::class);

        $this->assertCount($initialCount + 1, TagHelper::getRegisteredTagHelpers());
    }

    /** @test */
    public function it_throws_an_exception_for_invalid_helpers()
    {
        $this->expectException(InvalidHelperGiven::class);

        TagHelper::helper(TagHelper::class);
    }
}
