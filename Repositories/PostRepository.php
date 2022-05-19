<?php

namespace Modules\Blog\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Blog\Entities\Post;
use Modules\Core\Repositories\BaseRepository;

interface PostRepository extends BaseRepository
{
    /**
     * Get the next post of the given post
     * @param integer $id
     * @return LengthAwarePaginator
     */

    public function WhereCategory(int $id): LengthAwarePaginator;

    /**
     * Return the latest x blog posts
     * @param int $amount
     * @return Collection
     */
    public function latest(int $amount = 5): Collection;

    /**
     * Get the previous post of the given post
     * @param Post $post
     * @return Model|Collection|Builder|array|null
     */
    public function getPreviousOf(Post $post): Model|Collection|Builder|array|null;

    /**
     * Get the next post of the given post
     * @param Post $post
     * @return Model|Collection|Builder|array|null
     */
    public function getNextOf(Post $post):Model|Collection|Builder|array|null;
}
