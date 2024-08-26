<?php
declare(strict_types=1);

namespace Schivei\TagHelper;

use Illuminate\Filesystem\Filesystem;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
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
        $element = $tagHelper->getTargetElement();

        $elements = explode('|', $element);

        $attribute = $tagHelper->getTargetAttribute();

        return implode(',', array_map(function ($element) use ($attribute) {
            return !empty($attribute) ? "{$element}[$attribute]" : $element;
        }, $elements));
    }

    /**
     * Parse the HTML content of the view.
     *
     * @param string $viewContents
     * @param Helper $tagHelper
     * @return string
     *
     * @throws ChildNotFoundException|CircularException|StrictException|NotLoadedException
     */
    protected function parseHtml(string $viewContents, Helper $tagHelper) : string
    {
        $doc = new Dom();

        $doc->loadStr($viewContents, [
            'removeScripts' => false,
            'removeStyles' => false,
            'removeSmartyScripts' => false,
            'strict' => false,
            'preserveLineBreaks' => true,
            'removeDoubleSpace' => false,
        ]);

        $elements = $doc->find($this->getTagSelector($tagHelper));

        $content = $viewContents;

        if ($elements->count() > 0) {
            foreach ($elements as /** @type HtmlNode $element */ $element) {
                $el = HtmlElement::create($doc, $element);

                $tagHelper->process($el);
            }

            $content = (string)$doc;
        }

        return $content;
    }
}
