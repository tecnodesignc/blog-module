<?php

namespace Modules\Blog\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface PostRepository extends BaseRepository
{
    /**
     * Get the next post of the given post
     * @param integer $id
     * @return object
     */

    public function WhereCategory(int $id): object;
}
