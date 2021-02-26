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

    public function latest($amount = 5)
    {
        return $this->cache
            ->tags([$this->entityName, 'global'])
            ->remember(
                "{$this->locale}.{$this->entityName}.latest.{$amount}",
                $this->cacheTime,
                function () use ($amount) {
                    return $this->repository->latest($amount);
                }
            );
    }

    public function getPreviousOf(object $post)
    {
        $postId = $post->id;

        return $this->cache
            ->tags([$this->entityName, 'global'])
            ->remember(
                "{$this->locale}.{$this->entityName}.getPreviousOf.{$postId}",
                $this->cacheTime,
                function () use ($post) {
                    return $this->repository->getPreviousOf($post);
                }
            );
    }

    public function getNextOf(object $post)
    {
        $postId = $post->id;

        return $this->cache
            ->tags([$this->entityName, 'global'])
            ->remember(
                "{$this->locale}.{$this->entityName}.getNextOf.{$postId}",
                $this->cacheTime,
                function () use ($post) {
                    return $this->repository->getNextOf($post);
                }
            );
    }
}
