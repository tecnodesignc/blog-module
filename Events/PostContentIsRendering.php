<?php

namespace Modules\Blog\Events;

/**
 *
 */
class PostContentIsRendering
{
    /**
     * @var string The body of the page to render
     */
    private string $content;
    /**
     * @var mixed
     */
    private mixed $original;

    public function __construct($content)
    {
        $this->content = $content;
        $this->original = $content;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getOriginal(): mixed
    {
        return $this->original;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
