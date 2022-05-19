<?php

namespace Modules\Blog\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Blog\Http\Requests\CreateCategoryRequest;
use Modules\Blog\Repositories\CategoryRepository;
use Modules\Blog\Repositories\PostRepository;
use Modules\Blog\Transformers\CategoryTransformer;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Transformers\UserProfileTransformer;
use Route;

//Base API

class CategoryApiController extends BaseApiController
{

    private PostRepository $post;
    /**
     *
     * @var CategoryRepository
     */
    private CategoryRepository $category;

    public function __construct(PostRepository $post, CategoryRepository $category)
    {
        parent::__construct();
        $this->post = $post;
        $this->category = $category;
    }

    /**
     * Get Data from Categories
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        try {

            //Get Parameters from URL.
            $params = $this->getParamsRequest($request);
            //Request to Repository
            $categories = $this->category->getItemsBy($params);

            //Response
            $response = ["data" => CategoryTransformer::collection($categories)];

            //If request pagination add meta-page
            $params->page ? $response["meta"] = ["page" => $this->pageTransformer($categories)] : false;
        } catch (\Exception $e) {
            Log::Error($e);
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }

        //Return response
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }

    /**
     * GET A ITEM
     *
     * @param $criteria
     * @param Request $request
     * @return JsonResponse
     */
      public function show($criteria,Request $request): JsonResponse
      {
        try {
          //Get Parameters from URL.
          $params = $this->getParamsRequest($request);

          //Request to Repository
          $category = $this->category->getItem($criteria, $params);

          //Break if no found item
          if(!$category) throw new Exception('Item not found',404);

          //Response
          $response = ["data" => new CategoryTransformer($category)];

        } catch (\Exception $e) {
          $status = $this->getStatusError($e->getCode());
          $response = ["errors" => $e->getMessage()];
        }

        //Return response
        return response()->json($response, $status ?? 200);
      }


    /**
     * Create a Category
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        \DB::beginTransaction();
        try {
            $data = $request->input('attributes') ?? [];//Get data
            //Validate Request
            $this->validateRequestApi(new CreateCategoryRequest($data));

            //Create item
            $category = $this->category->create($data);

            //Response
            $response = ["data" => new CategoryTransformer($category)];
            \DB::commit(); //Commit to Data Base
        } catch (\Exception $e) {
            \DB::rollback();//Rollback to Data Base
            Log::Error($e);
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        //Return response
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }

    /**
     * Update a Category
     *
     * @param $criteria
     * @param Request $request
     * @return JsonResponse
     */
    public function update($criteria, Request $request): JsonResponse
    {
        \DB::beginTransaction(); //DB Transaction
        try {
            //Get data
            $data = $request->input('attributes') ?? [];//Get data

            //Validate Request
            $this->validateRequestApi(new CreateCategoryRequest($data));

            //Get Parameters from URL.
            $params = $this->getParamsRequest($request);


            //Request to Repository
            $category = $this->category->getItem($criteria, $params);

            //Request to Repository
            $this->category->update($category, $data);

            //Response
            $response = ["data" => trans('blog::common.messages.resource updated')];
            \DB::commit();//Commit to DataBase
        } catch (\Exception $e) {
            \DB::rollback();//Rollback to Data Base
            Log::Error($e);
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }

        //Return response
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }

    /**
     * Delete a Category
     *
     * @param $criteria
     * @param Request $request
     * @return JsonResponse
     */
    public function delete($criteria, Request $request)
    {
        \DB::beginTransaction();
        try {
            //Get params
            $params = $this->getParamsRequest($request);

            //Request to Repository
            $category = $this->category->getItem($criteria, $params);
            //call Method delete
            $this->category->destroy($category);

            //Response
            $response = ["data" => trans('blog::common.messages.resource deleted')];
            \DB::commit();//Commit to Data Base
        } catch (\Exception $e) {
            \DB::rollback();//Rollback to Data Base
            Log::Error($e);
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }

        //Return response
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }

}
