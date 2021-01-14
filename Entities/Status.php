<?php


namespace Modules\Blog\Entities;

/**
 * Class Status
 * @package Modules\Blog\Entities
 */

class Status
{
    const DRAFT = 0;
    const PENDING = 1;
    const PUBLISHED = 2;
    const UNPUBLISHED = 3;
    /**
     * @var array
     */
    private $statuses = [];

    public function __construct()
    {
        $this->statuses = [
            self::DRAFT => trans('blog::common.status.draft'),
            self::PENDING => trans('blog::common.status.pending review'),
            self::PUBLISHED => trans('blog::common.status.published'),
            self::UNPUBLISHED => trans('blog::common.status.unpublished'),
        ];
    }

    /**
     * Get the available statuses
     * @return array
     */
    public function lists()
    {
        return $this->statuses;
    }

    /**
     * Get the post status
     * @param int $statusId
     * @return string
     */
    public function get($statusId)
    {
        if (isset($this->statuses[$statusId])) {
            return $this->statuses[$statusId];
        }

        return $this->statuses[self::DRAFT];
    }
}
