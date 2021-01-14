<?php

namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Blog\Entities\Category;
use Modules\Blog\Http\Requests\CreateCategoryRequest;
use Modules\Blog\Http\Requests\UpdateCategoryRequest;
use Modules\Blog\Repositories\CategoryRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

class CategoryController extends AdminBaseController
{
    /**
     * @var CategoryRepository
     */
    private $category;

    public function __construct(CategoryRepository $category)
    {
        parent::__construct();

        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $categories = $this->category->all();

        return view('blog::admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = $this->category->all();
        return view('blog::admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCategoryRequest $request
     * @return Response
     */
    public function store(CreateCategoryRequest $request)
    {
        \DB::beginTransaction();
        try {
            $this->category->create($request->all());
            \DB::commit();//Commit to Data Base
            return redirect()->route('admin.blog.category.index')
                ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('blog::categories.title.categories')]));
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('blog::categories.title.categories')]))->withInput($request->all());

        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return Response
     */
    public function edit(Category $category)
    {
        $categories = $this->category->all();
        return view('blog::admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Category $category
     * @param UpdateCategoryRequest $request
     * @return Response
     */
    public function update(Category $category, UpdateCategoryRequest $request)
    {
        try {
        $this->category->update($category, $request->all());

        return redirect()->route('admin.blog.category.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('blog::categories.title.categories')]));
        } catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('blog::categories.title.categories')]))->withInput($request->all());

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return Response
     */
    public function destroy(Category $category)
    {
        try {
        $this->category->destroy($category);

        return redirect()->route('admin.blog.category.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('blog::categories.title.categories')]));
        } catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('blog::categories.title.categories')]));

        }
    }
}
