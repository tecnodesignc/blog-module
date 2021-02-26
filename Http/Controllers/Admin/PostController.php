<?php

namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\Status;
use Modules\Blog\Http\Requests\CreatePostRequest;
use Modules\Blog\Http\Requests\UpdatePostRequest;
use Modules\Blog\Repositories\PostRepository;
use Modules\Blog\Repositories\CategoryRepository;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

class PostController extends AdminBaseController
{
    /**
     * @var PostRepository
     */
    private $post;

    /**
     * @var CategoryRepository
     */
    private $category;

    /**
     * @var Status
     */
    private $status;
    /**
     * @var RoleRepository
     */
    private $role;

    /**
     * @var UserRepository
     */
    private $user;

    public function __construct(PostRepository $post, CategoryRepository $category, Status $status, RoleRepository $role, UserRepository $user)
    {
        parent::__construct();

        $this->post = $post;
        $this->category = $category;
        $this->status = $status;
        $this->role = $role;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|Response
     */
    public function index(Request $request)
    {
        if ($request->input('q')) {
            $param = $request->input('q');
            $posts = $this->post->search($param);
        } else {
            $posts = $this->post->paginate(20);
        }
        return view('blog::admin.posts.index', compact('posts'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $users = $this->user->all();
        $status = $this->status->lists();
        $categories = $this->category->all();
        return view('blog::admin.posts.create', compact('categories', 'status', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePostRequest $request
     * @return Response
     */
    public function store(CreatePostRequest $request)
    {
        \DB::beginTransaction();
        try {
            $this->post->create($request->all());

            \DB::commit();//Commit to Data Base

            return redirect()->route('admin.blog.post.index')
                ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('blog::posts.title.posts')]));
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('blog::posts.title.posts')]))->withInput($request->all());

        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Post $post
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|Response
     */
    public function edit(Post $post)
    {
        $users = $this->user->all();
        $status = $this->status->lists();
        $categories = $this->category->all();
        return view('blog::admin.posts.edit', compact('post', 'categories', 'status', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Post $post
     * @param UpdatePostRequest $request
     * @return Response
     */
    public function update(Post $post, UpdatePostRequest $request)
    {
        \DB::beginTransaction();
        try {
            $this->post->update($post, $request->all());
            \DB::commit();//Commit to Data Base

            return redirect()->route('admin.blog.post.index')
                ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('blog::posts.title.posts')]));
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('blog::posts.title.posts')]))->withInput($request->all());

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return Response
     */
    public function destroy(Post $post)
    {
        try {
            $this->post->destroy($post);

            return redirect()->route('admin.blog.post.index')
                ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('blog::posts.title.posts')]));
        } catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('blog::posts.title.posts')]));

        }
    }
}
