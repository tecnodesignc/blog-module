<?php

namespace Modules\Blog\Repositories\Cache;

use Modules\Blog\Repositories\PostRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CachePostDecorator extends BaseCacheDecorator implements PostRepository
{
    public function __construct(PostRepository $post)
    {
        parent::__construct();
        $this->entityName = 'blog.posts';
        $this->repository = $post;
    }

    public function WhereCategory(int $id): object
    {
        return $this->remember(function () use ($id) {
            return $this->repository->whereCategory($id);
        });
    }
}
