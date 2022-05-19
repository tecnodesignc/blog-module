<?php

namespace Modules\Blog\Events;

use Illuminate\Database\Eloquent\Model;
use Modules\Blog\Entities\Post;
use Modules\Media\Contracts\StoringMedia;

class PostWasUpdated implements StoringMedia
{
    /**
     * @var array
     */
    public array $data;
    /**
     * @var Post
     */
    public Post $post;

    public function __construct(Post $post, array $data)
    {
        $this->data = $data;
        $this->post = $post;
    }

    /**
     * Return the entity
     * @return Model
     */
    public function getEntity():Model
    {
        return $this->post;
    }

    /**
     * Return the ALL data sent
     * @return array
     */
    public function getSubmissionData(): array
    {
        return $this->data;
    }
}
