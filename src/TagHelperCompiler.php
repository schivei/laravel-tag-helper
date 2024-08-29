<?php
declare(strict_types=1);

namespace Schivei\TagHelper;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Schivei\TagHelper\Html\HtmlDocument;

/**
 * Class TagHelperCompiler
 * @package Schivei\TagHelper
 */
class TagHelperCompiler
{
    /**
     * The tag helper instance.
     *
     * @var TagHelper $tagHelper
     */
    protected TagHelper $tagHelper;

    /**
     * The filesystem instance.
     *
     * @var Filesystem $files
     */
    protected Filesystem $files;

    /**
     * Create a new compiler instance.
     *
     * @param TagHelper $tagHelper
     * @param Filesystem $files
     */
    public function __construct(TagHelper $tagHelper, Filesystem $files)
    {
        $this->tagHelper = $tagHelper;
        $this->files = $files;
    }

    /**
     * Compile the view at the given content.
     *
     * @param  string  $viewContent
     * @return string
     */
    public function compile(string $viewContent) : string
    {
        return array_reduce(
            $this->tagHelper->getRegisteredTagHelpers(),
            [$this, 'parseHtml'],
            $viewContent
        );
    }

    /**
     * Parse the HTML content of the view.
     *
     * @param string $viewContents
     * @param Helper $tagHelper
     * @return string
     *
     * @throws Exception
     */
    protected function parseHtml(string $viewContents, Helper $tagHelper) : string
    {
        $doc = HtmlDocument::parse($viewContents);

        $elements = $doc->find($tagHelper);

        if (empty($elements)) {
            return $viewContents;
        }

        foreach ($elements as &$element) {
            $element->setHelper($tagHelper);
            $tagHelper->process($element);
        }

        $content = $doc->toString();

        unset($doc);

        return $content;
    }
}
