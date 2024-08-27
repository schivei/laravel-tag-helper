<?php
declare(strict_types=1);

namespace Schivei\TagHelper;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Schivei\TagHelper\Html\HtmlDocument;
use Schivei\TagHelper\Html\HtmlElement;

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
     * @param HtmlDocument $doc
     * @param Helper $tagHelper
     * @return HtmlElement[]
     */
    protected function getElements(HtmlDocument $doc, Helper $tagHelper): array
    {
        $element = $tagHelper->getTargetElement();

        $elements = explode('|', $element);

        $attribute = $tagHelper->getTargetAttribute();

        return $doc->find($elements, $attribute);
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

        $elements = $this->getElements($doc, $tagHelper);

        if (empty($elements)) {
            return $viewContents;
        }

        if (isset($elements[0]) && is_array($elements[0])) {
            $elements = array_merge(...$elements);
        }

        foreach ($elements as $element) {
            $tagHelper->process($element);
        }

        return (string)$doc;
    }
}
