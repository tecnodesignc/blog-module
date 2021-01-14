<?php

namespace Modules\Iblog\Http\Controllers;

use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Iblog\Repositories\CategoryRepository;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Ifeeds\Support\SupportFeed;
use Modules\Tag\Repositories\TagRepository;
use Request;
use Route;
use Modules\Page\Http\Controllers\PublicController as PageController;

class PublicController extends BasePublicController
{
    /**
     * @var PostRepository
     */
    private $post;
    private $category;
    private $tag;

    public function __construct(PostRepository $post, CategoryRepository $category, TagRepository $tag)
    {
        parent::__construct();
        $this->post = $post;
        $this->category = $category;
        $this->tag = $tag;
    }

    public function index()
    {
        $slug = Request::path();

        $category = $this->category->findBySlug($slug);
        $posts = $this->post->whereCategory($category->id);
        //Get Custom Template.
        $template = $this->getTemplateForCategory($category);

        return view($template, compact('posts', 'category'));

    }

    public function show($slug)
    {
        $post = $this->post->findBySlug($slug);
        $category = $post->category;
        $tags = $post->tags()->get();

        $template = $this->getTemplateForPost($post);

        return view($template, compact('post', 'category', 'tags'));


    }

    public function tag($slug)
    {

        //Default Template
        $tpl = 'iblog::frontend.tag';
        $ttpl = 'iblog.tag';
        $tag = $this->tag->findBySlug($slug);
        if (view()->exists($ttpl)) $tpl = $ttpl;

        $posts = $this->post->whereTag($slug);
        //Get Custom Template.
        $ctpl = "iblog.tag.{$tag->id}";
        if (view()->exists($ctpl)) $tpl = $ctpl;


        return view($tpl, compact('posts', 'tag'));

    }

    public function feed($format)
    {
        $postPerFeed = config('asgard.iblog.config.postPerFeed');
        $posts = $this->post->whereFilters((object)['status' => 'publicado', 'take' => $postPerFeed]);
        $feed = new SupportFeed($format, $posts);
        $feed_logo = config('asgard.iblog.config.logo');
        return $feed->feed($feed_logo);

    }

    /**
     * Return the template for the given page
     * or the default template if none found
     * @param $post
     * @return string
     */
    private function getTemplateForPost($post)
    {
        return (view()->exists($post->template)) ? $post->template : 'default';
    }

    /**
     * Return the template for the given page
     * or the default template if none found
     * @param $page
     * @return string
     */
    private function getTemplateForCategory($category)
    {
        return (view()->exists($category->template)) ? $category->template : 'default';
    }

}
