<?php

namespace Schivei\TagHelper\Html;

final class HtmlText extends HtmlNode
{
    private string $_text;

    public function __construct(int $index, HtmlDocument &$root, HtmlElement &$parent, string $content, string $tag)
    {
        parent::__construct($index, $root, $parent, $content, $tag);

        $this->_text = $content;
    }

    public function getText(): string
    {
        return $this->_text;
    }

    public function setText(string $text): void
    {
        $this->_text = $text;
    }
}
