<?php

namespace Modules\Blog\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Blog\Entities\Category;
use Modules\Blog\Events\CategoryWasCreated;
use Modules\Blog\Events\CategoryWasDeleted;
use Modules\Blog\Events\CategoryWasUpdated;
use Modules\Blog\Repositories\CategoryRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EloquentCategoryRepository extends EloquentBaseRepository implements CategoryRepository
{

    /**
     * @param $id
     * @return Model|Collection|Builder|array|null
     */
    public function find($id): Model|Collection|Builder|array|null
    {
        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations','parent', 'children')->find($id);
        }
        return $this->model->with('parent', 'children')->find($id);
    }

    /**
     * Find a resource by the given slug
     *
     * @param string $slug
     * @return Model|Collection|Builder|array|null
     */
    public function findBySlug(string $slug): Model|Collection|Builder|array|null
    {
        if (method_exists($this->model, 'translations')) {
            return $this->model->whereHas('translations', function (Builder $q) use ($slug) {
                $q->where('slug', $slug);
            })->with('translations', 'parent', 'children', 'posts')->firstOrFail();
        }

        return $this->model->where('slug', $slug)->with('translations', 'parent', 'children', 'posts')->first();;
    }

    /**
     * Get resources by an array of attributes
     * @param bool|object $params
     * @return LengthAwarePaginator|Collection
     */
    public function getItemsBy($params = false):LengthAwarePaginator|Collection
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
            if (isset($filter->parent)) {
                $query->where('parent_id', $filter->parent);
            }

            if (isset($filter->search)) { //si hay que filtrar por rango de precio
                $criterion = $filter->search;
                $param = explode(' ', $criterion);
                $criterion = $filter->search;
                //find search in columns
                $query->where(function ($query) use ($filter, $criterion) {
                    $query->whereHas('translations', function (Builder $q) use ($criterion) {
                        $q->where('title', 'like', "%{$criterion}%");
                    });
                })->orWhere('id', 'like', '%' . $filter->search . '%');
            }

            //Filter by date
            if (isset($filter->date)) {
                $date = $filter->date;//Short filter date
                $date->field = $date->field ?? 'created_at';
                if (isset($date->from))//From a date
                    $query->whereDate($date->field, '>=', $date->from);
                if (isset($date->to))//to a date
                    $query->whereDate($date->field, '<=', $date->to);
            }
            if(is_module_enabled('Marketplace')){
                if (isset($filter->store)) {
                    $query->where('store_id',$filter->store);
                }
            }
            //Order by
            if (isset($filter->order)) {
                $orderByField = $filter->order->field ?? 'created_at';//Default field
                $orderWay = $filter->order->way ?? 'desc';//Default way
                $query->orderBy($orderByField, $orderWay);//Add order to query
            }
        }

        /*== FIELDS ==*/
        if (isset($params->fields) && count($params->fields))
            $query->select($params->fields);

        /*== REQUEST ==*/
        if (isset($params->page) && $params->page) {
            return $query->paginate($params->take);
        } else {
            $params->take ? $query->take($params->take) : false;//Take
            return $query->get();
        }
    }

    /**
     * Standard Api Method
     * @param $criteria
     * @param bool $params
     * @return Model|Collection|Builder|array|null
     */
    public function getItem($criteria, $params = false): Model|Collection|Builder|array|null
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
     * Standard Api Method
     * @param $data
     * @return Model|Collection|Builder|array|null
     */
    public function create($data):Model|Collection|Builder|array|null
    {

        $category = $this->model->create($data);

        event(new CategoryWasCreated($category, $data));

        return $this->find($category->id);
    }

    /**
     * Update a resource
     * @param $model
     * @param array $data
     * @return Model|Collection|Builder|array|null
     */
    public function update($model, array $data):Model|Collection|Builder|array|null
    {
        $model->update($data);

        event(new CategoryWasUpdated($model, $data));

        return $model;
    }
    /**
     * Destroy a resource
     * @param  $model
     * @return bool
     */

    public function destroy($model): bool
    {
        event(new CategoryWasDeleted($model->id, get_class($model)));

        return $model->delete();
    }


}
