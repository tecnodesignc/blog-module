<?php

namespace Modules\Blog\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\Status;
use Modules\Blog\Events\PostWasCreated;
use Modules\Blog\Events\PostWasDeleted;
use Modules\Blog\Events\PostWasUpdated;
use Modules\Blog\Repositories\PostRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

class EloquentPostRepository extends EloquentBaseRepository implements PostRepository
{


    public function findBySlug($slug): Model|Collection|Builder|array|null
    {
        if (method_exists($this->model, 'translations')) {
            return $this->model->whereHas('translations', function (Builder $q) use ($slug) {
                $q->where('slug', $slug);
            })->with('translations', 'category', 'categories', 'tags', 'user')->whereStatus(Status::PUBLISHED)->firstOrFail();
        }

        return $this->model->where('slug', $slug)->with('category', 'categories', 'tags', 'user')->whereStatus(Status::PUBLISHED)->firstOrFail();;
    }

    /**
     * @param integer $id
     * @return LengthAwarePaginator
     */
    public function whereCategory(int $id):LengthAwarePaginator
    {
        $query = $this->model->with('categories', 'category', 'tags', 'user', 'translations');
        $query->whereHas('categories', function ($q) use ($id) {
            $q->where('category_id', $id);
        })->whereStatus(Status::PUBLISHED)->where('created_at', '<', date('Y-m-d H:i:s'))->orderBy('created_at', 'DESC');

        return $query->paginate(setting('blog::posts-per-page'));
    }

    /**
     * Find post by id
     * @param int $id
     * @return Model|Collection|Builder|array|null
     */
    public function find(int $id): Model|Collection|Builder|array|null
    {
        return $this->model->with('translations', 'category', 'categories', 'tags', 'user')->find($id);
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->with( 'translations')->orderBy('created_at', 'DESC')->get();
    }

    /**
     * Create a resource
     * @param  $data
     * @return Model|Collection|Builder|array|null
     */
    public function create($data): Model|Collection|Builder|array|null
    {
        $post = $this->model->create($data);
        $post->categories()->sync(array_merge(Arr::get($data, 'categories', []), [$post->category_id]));
        event(new PostWasCreated($post, $data));
        $post->setTags(Arr::get($data, 'tags', []));
        return $post;
    }

    /**
     * Update a resource
     * @param  $model
     * @param array $data
     * @return Model|Collection|Builder|array|null
     */
    public function update($model, array $data): Model|Collection|Builder|array|null
    {
        $model->update($data);

        $model->categories()->sync(array_merge(Arr::get($data, 'categories', []), [$model->category_id]));

        event(new PostWasUpdated($model, $data));
        $model->setTags(Arr::get($data, 'tags', []));

        return $model;
    }

    /**
     * Destroy a resource
     * @param  $model
     * @return bool
     */
    public function destroy($model): bool
    {
        $model->untag();
        event(new PostWasDeleted($model->id, get_class($model)));

        return $model->delete();
    }

    /**
     * Get resources by an array of attributes
     * @param bool|object $params
     * @return LengthAwarePaginator|Collection
     */
    public function getItemsBy($params = false): Collection|LengthAwarePaginator
    {
        /*== initialize query ==*/
        $query = $this->model->query();

        /*== RELATIONSHIPS ==*/
        if (in_array('*', $params->include)) {//If Request all relationships
            $query->with(['translations']);
        } else {//Especific relationships
            $includeDefault = ['translations'];//Default relationships
            if (isset($params->include))//merge relations with default relationships
                $includeDefault = array_merge($includeDefault, $params->include);
            $query->with($includeDefault);//Add Relationships to query
        }

        /*== FILTERS ==*/
        if (isset($params->filter)) {
            $filter = $params->filter;//Short filter
            if (isset($filter->categories) && !empty($filter->categories)) {

                $categories = is_array($filter->categories) ? $filter->categories : [$filter->categories];
                $query->whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('category_id', $categories);
                });
            }

            if (isset($filter->users) && !empty($filter->users)) {
                $users = is_array($filter->users) ? $filter->users : [$filter->users];
                $query->whereIn('user_id', $users);
            }

            if (isset($filter->include) && !empty($filter->include)) {
                $include = is_array($filter->include) ? $filter->include : [$filter->include];
                $query->whereIn('id', $include);
            }
            if (isset($filter->exclude) && !empty($filter->exclude)) {
                $exclude = is_array($filter->exclude) ? $filter->exclude : [$filter->exclude];
                $query->whereNotIn('id', $exclude);
            }

            if (isset($filter->exclude_categories) && !empty($filter->exclude_categories)) {

                $exclude_categories = is_array($filter->exclude_categories) ? $filter->exclude_categories : [$filter->exclude_categories];
                $query->whereHas('categories', function ($q) use ($exclude_categories) {
                    $q->whereNotIn('category_id', $exclude_categories);
                });
            }

            if (isset($filter->exclude_users) && !empty($filter->exclude_users)) {
                $exclude_users = is_array($filter->exclude_users) ? $filter->exclude_users : [$filter->exclude_users];
                $query->whereNotIn('user_id', $exclude_users);
            }

            if (isset($filter->tag) && !empty($filter->tag)) {

                $query->whereTag($filter->tag);
            }


            if (isset($filter->search) && !empty($filter->search)) { //si hay que filtrar por rango de precio
                $criterion = $filter->search;
                $searchValues = preg_split('/\s+/', $criterion, -1, PREG_SPLIT_NO_EMPTY);
                $query->whereHas('translations', function (Builder $q) use ($searchValues) {
                    $q->where(function ($s) use ($searchValues){

                        foreach ($searchValues as $value){
                            if(strlen($value)>3){
                                $s->orWhere('title', 'like', "%{$value}%");
                            }
                        }
                    });

                });
            }

            //Filter by date
            if (isset($filter->date) && !empty($filter->date)) {
                $date = $filter->date;//Short filter date
                $date->field = $date->field ?? 'created_at';
                if (isset($date->from))//From a date
                    $query->whereDate($date->field, '>=', $date->from);
                if (isset($date->to))//to a date
                    $query->whereDate($date->field, '<=', $date->to);
            }
            if (is_module_enabled('Marketplace')) {
                if (isset($filter->store) && !empty($filter->store)) {
                    $query->where('store_id', $filter->store);
                }
            }

            //Order by
            if (isset($filter->order) && !empty($filter->order)) {
                $orderByField = $filter->order->field ?? 'created_at';//Default field
                $orderWay = $filter->order->way ?? 'desc';//Default way
                $query->orderBy($orderByField, $orderWay);//Add order to query
            }

            if (isset($filter->status) && !empty($filter->status)) {
                $query->whereStatus($filter->status);
            }

        }

        /*== FIELDS ==*/
        if (isset($params->fields) && count($params->fields))
            $query->select($params->fields);
        /*== REQUEST ==*/

        if (isset($params->page) && $params->page) {
            return $query->paginate($params->take);
        } else {
            if (isset($params->skip) && !empty($params->skip)) {
                $query->skip($params->skip);
            };

            $params->take ? $query->take($params->take) : false;//Take

            return $query->get();
        }
    }

    /**
     * Get resources by an array of attributes
     * @param string $criteria
     * @param bool|object $params
     * @return Model|Collection|Builder|array|null
     */
    public function getItem(string $criteria, $params = false): Model|Collection|Builder|array|null
    {
        //Initialize query
        $query = $this->model->query();

        /*== RELATIONSHIPS ==*/
        if (in_array('*', $params->include)) {//If Request all relationships
            $query->with(['translations']);
        } else {//Especific relationships
            $includeDefault = [];//Default relationships
            if (isset($params->include))//merge relations with default relationships
                $includeDefault = array_merge($includeDefault, $params->include);
            $query->with($includeDefault);//Add Relationships to query
        }

        /*== FILTER ==*/
        if (isset($params->filter)) {
            $filter = $params->filter;

            if (isset($filter->field))//Filter by specific field
                $field = $filter->field;

            // find translatable attributes
            $translatedAttributes = $this->model->translatedAttributes;

            // filter by translatable attributes
            if (isset($field) && in_array($field, $translatedAttributes))//Filter by slug
                $query->whereHas('translations', function ($query) use ($criteria, $filter, $field) {
                    $query->where('locale', $filter->locale)
                        ->where($field, $criteria);
                });
            else
                // find by specific attribute or by id
                $query->where($field ?? 'id', $criteria);
        }

        /*== FIELDS ==*/
        if (isset($params->fields) && count($params->fields))
            $query->select($params->fields);

        /*== REQUEST ==*/
        return $query->first();

    }

    /**
     * Return the latest x blog posts
     * @param int $amount
     * @return Collection
     */
    public function latest(int $amount = 5): Collection
    {
        return $this->model->whereStatus(Status::PUBLISHED)->orderBy('created_at', 'desc')->take($amount)->get();

    }

    /**
     * Get the previous post of the given post
     * @param Post $post
     * @return Model|Collection|Builder|array|null
     */
    public function getPreviousOf(Post $post): Model|Collection|Builder|array|null
    {
        return $this->model->where('created_at', '<', $post->created_at)
            ->whereStatus(Status::PUBLISHED)->orderBy('created_at', 'desc')->first();
    }

    /**
     * Get the next post of the given post
     * @param Post $post
     * @return Model|Collection|Builder|array|null
     */
    public function getNextOf(Post $post): Model|Collection|Builder|array|null
    {
        return $this->model->where('created_at', '>', $post->created_at)
            ->whereStatus(Status::PUBLISHED)->first();
    }


}
