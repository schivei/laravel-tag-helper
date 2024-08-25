<?php
declare(strict_types=1);

namespace Schivei\TagHelper\Tests;

use Schivei\TagHelper\TagHelper;
use Illuminate\Support\Facades\Blade;
use Spatie\Snapshots\MatchesSnapshots;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as Orchestra;
use Schivei\TagHelper\TagHelperServiceProvider;

abstract class TestCase extends Orchestra
{
    use MatchesSnapshots;

    protected function setUp() : void
    {
        parent::setUp();

        Artisan::call('view:clear');
    }

    protected function getPackageProviders($app) : array
    {
        return [
            TagHelperServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app) : array
    {
        return [
            'TagHelper' => TagHelper::class,
        ];
    }

    protected function assertMatchesViewSnapshot(string $viewName, array $data = []) : void
    {
        $fullViewName = "views.{$viewName}";

        $this->assertMatchesSnapshot(
            view($fullViewName, $data)->render()
        );

        $this->assertMatchesSnapshot(
            '<div>'.Blade::compileString($this->getViewContents($viewName)).'</div>'
        );
    }

    protected function getViewContents(string $viewName): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $testFile = last($backtrace)['file'];

        $baseDirectory = pathinfo($testFile, PATHINFO_DIRNAME);

        $viewFileName = "{$baseDirectory}/stubs/views/{$viewName}.blade.php";

        return file_get_contents($viewFileName);
    }
}
