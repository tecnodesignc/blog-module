<?php

namespace Modules\Blog\Events;

use Illuminate\Database\Eloquent\Model;
use Modules\Blog\Entities\Category;
use Modules\Media\Contracts\StoringMedia;

class CategoryWasUpdated implements StoringMedia
{
    /**
     * @var array
     */
    public array $data;
    /**
     * @var Category
     */
    public Category $category;

    public function __construct(Category $category, array $data)
    {
        $this->data = $data;
        $this->category = $category;
    }

    /**
     * Return the entity
     * @return Model
     */
    public function getEntity():Model
    {
        return $this->category;
    }

    /**
     * Return the ALL data sent
     * @return array
     */
    public function getSubmissionData():array
    {
        return $this->data;
    }
}
