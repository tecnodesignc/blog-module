<?php

namespace Modules\Blog\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Blog\Http\Requests\CreatePostRequest;
use Modules\Blog\Repositories\PostRepository;
use Modules\Blog\Transformers\PostTransformer;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Transformers\UserProfileTransformer;
use Route;

class PostApiController extends BaseApiController
{
    /**
     * @var PostRepository
     */
    private PostRepository $post;

    public function __construct(PostRepository $post)
    {
        parent::__construct();
        $this->post = $post;
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
            $posts = $this->post->getItemsBy($params);
            $response = ["data" => PostTransformer::collection($posts)];
            $params->page ? $response["meta"] = ["page" => $this->pageTransformer($posts)] : false;
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
    public function show($criteria, Request $request): JsonResponse
    {
        try {
            $params = $this->getParamsRequest($request);
            $post = $this->post->getItem($criteria, $params);
            if (!$post) throw new Exception(trans('core::core.exceptions.item no found', ['item' => trans('blog::posts.title.posts')]), 404);
            $response = ["data" => new PostTransformer($post)];
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
            $data = $request->input('attributes') ?? [];//Get data
            $this->validateRequestApi(new CreatePostRequest($data));
            $this->post->create($data);
            $response = ["message" => trans('core::core.messages.resource created', ['name' => trans('blog::posts.title.posts')])];
            \DB::commit();
        } catch (\Exception $e) {
            Log::Error($e);
            \DB::rollback();
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
            $this->validateRequestApi(new CreatePostRequest($data));
            $params = $this->getParamsRequest($request);
            $post = $this->post->getItem($criteria, $params);
            if (!$post) throw new Exception(trans('core::core.exceptions.item no found', ['item' => trans('blog::posts.title.posts')]), 404);
            $this->post->update($post, $data);
            $response = ["message" => trans('core::core.messages.resource updated', ['name' => trans('blog::posts.title.posts')])];
            \DB::commit();
        } catch (\Exception $e) {
            Log::Error($e);
            \DB::rollback();
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
            $post = $this->post->getItem($criteria, $params);
            if (!$post) throw new Exception(trans('core::core.exceptions.item no found', ['item' => trans('blog::posts.title.posts')]), 404);
            $this->post->destroy($post);
            $response = ["message" => trans('core::core.messages.resource deleted', ['name' => trans('blog::posts.title.posts')])];
            \DB::commit();
        } catch (\Exception $e) {
            Log::Error($e);
            \DB::rollback();
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }

}
