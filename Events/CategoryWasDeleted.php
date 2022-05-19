<?php

namespace Modules\Blog\Events;

use Modules\Media\Contracts\DeletingMedia;

class CategoryWasDeleted implements DeletingMedia
{
    /**
     * @var string
     */
    private string $categoryClass;
    /**
     * @var int
     */
    private int $categoryId;

    public function __construct($categoryId, $categoryClass)
    {
        $this->categoryClass = $categoryClass;
        $this->categoryId = $categoryId;
    }

    /**
     * Get the entity ID
     * @return int
     */
    public function getEntityId():int
    {
        return $this->categoryId;
    }

    /**
     * Get the class name the imageables
     * @return string
     */
    public function getClassName():string
    {
        return $this->categoryClass;
    }
}
