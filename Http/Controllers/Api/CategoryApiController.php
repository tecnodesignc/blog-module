<?php

namespace Modules\Blog\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Blog\Http\Requests\CreateCategoryRequest;
use Modules\Blog\Repositories\CategoryRepository;
use Modules\Blog\Transformers\CategoryTransformer;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Route;

class CategoryApiController extends BaseApiController
{
    /**
     *
     * @var CategoryRepository
     */
    private CategoryRepository $category;

    public function __construct(CategoryRepository $category)
    {
        parent::__construct();
        $this->category = $category;
    }

    /**
     * Get listing of the resource
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $params = $this->getParamsRequest($request);
            $categories = $this->category->getItemsBy($params);
            $response = ["data" => CategoryTransformer::collection($categories)];
            $params->page ? $response["meta"] = ["page" => $this->pageTransformer($categories)] : false;
        } catch (\Exception $e) {
            Log::Error($e);
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }

    /**
     * Get a resource item
     *
     * @param $criteria
     * @param Request $request
     * @return JsonResponse
     */
      public function show($criteria,Request $request): JsonResponse
      {
        try {
          $params = $this->getParamsRequest($request);
          $category = $this->category->getItem($criteria, $params);
          if(!$category) throw new Exception( trans('core::core.exceptions.item no found', ['item' => trans('blog::categories.title.categories')]),404);
          $response = ["data" => new CategoryTransformer($category)];
        } catch (\Exception $e) {
          $status = $this->getStatusError($e->getCode());
          $response = ["errors" => $e->getMessage()];
        }
        return response()->json($response, $status ?? 200);
      }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        \DB::beginTransaction();
        try {
            $data = $request->input('attributes') ?? [];
            $this->validateRequestApi(new CreateCategoryRequest($data));
            $this->category->create($data);
            $response = ["message" => trans('core::core.messages.resource created', ['name' => trans('blog::categories.title.categories')])];
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            Log::Error($e);
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }

    /**
     * Update the specified resource in storage..
     *
     * @param $criteria
     * @param Request $request
     * @return JsonResponse
     */
    public function update($criteria, Request $request): JsonResponse
    {
        \DB::beginTransaction();
        try {
            $data = $request->input('attributes') ?? [];//Get data
            $this->validateRequestApi(new CreateCategoryRequest($data));
            $params = $this->getParamsRequest($request);
            $category = $this->category->getItem($criteria, $params);
            if(!$category) throw new Exception(trans('core::core.exceptions.item no found', ['item' => trans('blog::categories.title.categories')]),404);
            $this->category->update($category, $data);
            $response = ["message" => trans('core::core.messages.resource updated', ['name' => trans('blog::categories.title.categories')])];
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            Log::Error($e);
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $criteria
     * @param Request $request
     * @return JsonResponse
     */
    public function delete($criteria, Request $request): JsonResponse
    {
        \DB::beginTransaction();
        try {
            $params = $this->getParamsRequest($request);
            $category = $this->category->getItem($criteria, $params);
            if(!$category) throw new Exception(trans('core::core.exceptions.item no found', ['item' => trans('blog::categories.title.categories')]),404);
            $this->category->destroy($category);
            $response = ["message" => trans('core::core.messages.resource deleted', ['name' => trans('blog::categories.title.categories')])];
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            Log::Error($e);
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }

}
