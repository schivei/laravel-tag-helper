<?php
declare(strict_types=1);

namespace Schivei\TagHelper;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Schivei\TagHelper\Helpers\AssetHelper;
use Schivei\TagHelper\Helpers\AuthHelper;
use Schivei\TagHelper\Helpers\ConditionHelper;
use Schivei\TagHelper\Helpers\CsrfHelper;
use Schivei\TagHelper\Helpers\FormMethodHelper;
use Schivei\TagHelper\Helpers\GuestHelper;
use Schivei\TagHelper\Helpers\LinkHelper;

/**
 * Class TagHelperServiceProvider
 * @package Schivei\TagHelper
 */
class TagHelperServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->app->singleton(TagHelper::class);

        $this->app->alias(TagHelper::class, 'tag-helper');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() : void
    {
        Blade::prepareStringsForCompilationUsing(function ($value) {
            return $this->app[TagHelperCompiler::class]->compile($value);
        });

        $this->app['tag-helper']->helper(AssetHelper::class);
        $this->app['tag-helper']->helper(LinkHelper::class);
        $this->app['tag-helper']->helper(FormMethodHelper::class);
        $this->app['tag-helper']->helper(CsrfHelper::class);
        $this->app['tag-helper']->helper(ConditionHelper::class);
        $this->app['tag-helper']->helper(AuthHelper::class);
        $this->app['tag-helper']->helper(GuestHelper::class);
    }
}
