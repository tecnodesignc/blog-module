<?php

namespace Modules\Blog\Events;

use Modules\Media\Contracts\DeletingMedia;

class PostWasDeleted implements DeletingMedia
{
    /**
     * @var string
     */
    private string $postClass;
    /**
     * @var int
     */
    private int $postId;

    /**
     * @param $postId
     * @param $postClass
     */
    public function __construct($postId, $postClass)
    {
        $this->postClass = $postClass;
        $this->postId = $postId;
    }

    /**
     * Get the entity ID
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->postId;
    }

    /**
     * Get the class name the imageables
     * @return string
     */
    public function getClassName(): string
    {
        return $this->postClass;
    }
}
