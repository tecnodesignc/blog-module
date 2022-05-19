<?php

namespace Modules\Blog\Repositories\Cache;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Blog\Entities\Post;
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

    /**
     * Get the next post of the given post
     * @param integer $id
     * @return LengthAwarePaginator
     */
    public function WhereCategory(int $id): LengthAwarePaginator
    {
        return $this->remember(function () use ($id) {
            return $this->repository->whereCategory($id);
        });
    }

    public function latest(int $amount = 5): Collection
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

    public function getPreviousOf(Post $post): Model|Collection|Builder|array|null
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

    /**
     * Get the next post of the given post
     * @param Post $post
     * @return Model|Collection|Builder|array|null
     */
    public function getNextOf(Post $post):Model|Collection|Builder|array|null
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
