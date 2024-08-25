<?php
declare(strict_types=1);

namespace Schivei\TagHelper;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Exception;
use FluentDOM\Loader\Options;
use Illuminate\Filesystem\Filesystem;
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
     * Get the tag selector for the helper.
     *
     * @param Helper $tagHelper
     * @return string
     */
    protected function getTagSelector(Helper $tagHelper): string
    {
        $elements = explode('|', $tagHelper->getTargetElement());
        return implode('|', array_map(function ($element) use ($tagHelper) {
            $selector = "//$element";
            if (!is_null($tagHelper->getTargetAttribute())) {
                $selector .= "[@{$tagHelper->getTargetAttribute()}]";
            }

            return $selector;
        }, $elements));
    }

    /**
     * Parse the HTML content of the view.
     *
     * @param string $viewContents
     * @param Helper $tagHelper
     * @return string
     */
    protected function parseHtml(string $viewContents, Helper $tagHelper) : string
    {
        $doc = new DOMDocument();

        $doc->loadHTML($viewContents, LIBXML_HTML_NOIMPLIED | LIBXML_NOERROR | LIBXML_NOWARNING);

        $selector = new DOMXPath($doc);

        $elements = $selector->query($this->getTagSelector($tagHelper));

        foreach ($elements as /** @type DOMElement $element */ $element) {
            $el = HtmlElement::create($doc,$element);

            $tagHelper->process($el);
        }

        $content = FluentDOM($doc->documentElement)->outerHtml();

        $content = str_replace(['%7B', '%7D'], ['{', '}'], $content);

        return empty($content) ? $viewContents : $content;
    }
}
