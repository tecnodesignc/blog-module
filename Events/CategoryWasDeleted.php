<?php

namespace Modules\Blog\Events;

use Modules\Media\Contracts\DeletingMedia;

class CategoryWasDeleted implements DeletingMedia
{
    /**
     * @var string
     */
    private $categoryClass;
    /**
     * @var int
     */
    private $categoryId;

    public function __construct($categoryId, $categoryClass)
    {
        $this->categoryClass = $categoryClass;
        $this->categoryId = $categoryId;
    }

    /**
     * Get the entity ID
     * @return int
     */
    public function getEntityId()
    {
        return $this->categoryId;
    }

    /**
     * Get the class name the imageables
     * @return string
     */
    public function getClassName()
    {
        return $this->categoryClass;
    }
}
