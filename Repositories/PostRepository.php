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

    /**
     * Return the latest x blog posts
     * @param int $amount
     * @return object
     */
    public function latest($amount = 5);

    /**
     * Get the previous post of the given post
     * @param object $post
     * @return object
     */
    public function getPreviousOf(object $post);

    /**
     * Get the next post of the given post
     * @param object $post
     * @return object
     */
    public function getNextOf(object $post);
}
