<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Tests\Compilation;

use Illuminate\Support\Facades\View;
use Schivei\TagHelper\Helpers\AssetHelper;
use Schivei\TagHelper\Tests\TestCase;
use Schivei\TagHelper\Facades\TagHelper;
use Schivei\TagHelper\Tests\Compilation\Tags\EmailTag;
use Schivei\TagHelper\Tests\Compilation\Tags\RegularTag;
use Schivei\TagHelper\Tests\Compilation\Tags\ViewDataTag;
use Schivei\TagHelper\Tests\Compilation\Tags\AttributeTag;

class TagHelperCompilationTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/stubs');
    }

    /** @test */
    public function it_compiles_tags_with_asset_attribute()
    {
        TagHelper::helper(AssetHelper::class);

        $this->assertMatchesViewSnapshot('asset_attribute');
    }

    /** @test */
    public function it_compiles_a_regular_tag_helper()
    {
        TagHelper::helper(RegularTag::class);

        $this->assertMatchesViewSnapshot('regular_tag');
    }

    /** @test */
    public function it_compiles_nested_tags()
    {
        TagHelper::helper(RegularTag::class);

        $this->assertMatchesViewSnapshot('nested_tags');
    }

    /** @test */
    public function it_matches_element_attributes()
    {
        TagHelper::helper(AttributeTag::class);

        $this->assertMatchesViewSnapshot('element_attributes');
    }

    /** @test */
    public function it_can_use_dynamic_data()
    {
        TagHelper::helper(ViewDataTag::class);

        $this->assertMatchesViewSnapshot('dynamic_data');
    }

    /** @test */
    public function it_can_access_view_data()
    {
        TagHelper::helper(ViewDataTag::class);

        $this->assertMatchesViewSnapshot('view_data', ['data' => 'Some Information']);
    }

    /** @test */
    public function it_parses_invalid_html()
    {
        TagHelper::helper(RegularTag::class);

        $this->assertMatchesViewSnapshot('invalid_html');
    }

    /** @test */
    public function tags_can_change_their_own_tag()
    {
        TagHelper::helper(EmailTag::class);

        $this->assertMatchesViewSnapshot('change_tag');
    }

    /** @test */
    public function it_compiles_empty_views()
    {
        $this->assertMatchesViewSnapshot('empty');
    }
}
