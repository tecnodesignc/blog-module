<?php

namespace Modules\Blog\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Blog\Entities\Status;
use Modules\Blog\Repositories\PostRepository;

class PostPresenter extends Presenter
{
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var PostRepository
     */
    private $post;

    public function __construct($entity)
    {
        parent::__construct($entity);
        $this->post = app('Modules\Blog\Repositories\PostRepository');
        $this->status = app('Modules\Blog\Entities\Status');
    }

    /**
     * Get the previous post of the current post
     * @return object
     */
    public function previous()
    {
        return $this->post->getPreviousOf($this->entity);
    }

    /**
     * Get the next post of the current post
     * @return object
     */
    public function next(): object
    {
        return $this->post->getNextOf($this->entity);
    }

    /**
     * Get the post status
     * @return string
     */
    public function status(): string
    {
        return $this->status->get($this->entity->status);
    }

    /**
     * Getting the label class for the appropriate status
     * @return string
     */
    public function statusLabelClass(): string
    {
        switch ($this->entity->status) {
            case Status::DRAFT:
                return 'red';
                break;
            case Status::PENDING:
                return 'orange';
                break;
            case Status::PUBLISHED:
                return 'green';
                break;
            case Status::UNPUBLISHED:
                return 'purple';
                break;
            default:
                return 'primary';
                break;
        }
    }
}
