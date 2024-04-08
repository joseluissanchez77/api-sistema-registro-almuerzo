<?php

namespace App\Repositories\Base;

use Closure;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class ListBaseRepository
{

    private $model;
    private $parents;
    private $selfFieldsAndParents;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(Model $model, $parents, $selfFieldsAndParents)
    {
        $this->model = $model;
        $this->parents = $parents;
        $this->selfFieldsAndParents = $selfFieldsAndParents;
    }

    /**
     * withOutPaginate
     *
     * @param  mixed $selected
     * @return ListBaseRepository
     */
    public function withOutPaginate($selected): ListBaseRepository
    {
        $this->model = $this->model->select($selected)->where(function ($query) {
            if (array_search('status_id', Schema::getColumnListing($this->model->getTable()))) {
                $query->where('status_id', 1);
            }
        });
        return $this;
    }


    /**
     * withModelRelations
     *
     * @param  mixed $relations
     * @return ListBaseRepository
     */
    public function withModelRelations(array $relations): ListBaseRepository
    {
        if (count($relations) > 0) {
            $this->model = $this->model->with($relations);
        }
        return $this;
    }

    /**
     * searchWithColumnNames
     *
     * @param  mixed $request
     * @return search with name columns model
     */
    public function searchWithColumnNames($request): ListBaseRepository
    {
        $collectQueryString = $this->cleanQueryParams($request);

        if (!empty($collectQueryString))
            $this->model = $this->model->where($collectQueryString);

        return $this;
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ListBaseRepository $this
     */
    public function searchByDateSRI($request): ListBaseRepository
    {
        if ($request->fechaEmision)
            $this->model = $this->model->orWhereBetween('fechaEmision', $request->fechaEmision);

        if ($request->fechaAutorizacionSRI)
            $this->model = $this->model->orWhereBetween('sri_fecha_autorizacion', $request->fechaAutorizacionSRI);

        if ($request->fechaPagoDocumento) {
            $dates = $request->fechaPagoDocumento;
            $this->model = $this->model->whereHas('payments', function ($query) use ($dates) {
                $this->model->orWhereBetween('created_at', [\Carbon\Carbon::parse($dates[0] . "00:00:00"), \Carbon\Carbon::parse($dates[1] . "23:59:59")]);
            });
        }

        return $this;
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ListBaseRepository $this
     */
    public function existPaymentSRI($request): ListBaseRepository
    {
        if ($request->status_payment) {
            if ($request->status_payment === "Pagado") {
                $this->model = $this->model->has('payments');
            } elseif ($request->status_payment === "Por Pagar") {
                $this->model = $this->model->doesntHave('payments');
            }
        }
        return $this;
    }

    /**
     * searchWithConditions
     *
     * @param  mixed $request
     * @return ListBaseRepository
     */
    public function searchWithConditions($request): ListBaseRepository
    {

        if (isset($request->conditions))
            $this->model = $this->model->where($request->conditions);

        return $this;
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $between
     * @return ListBaseRepository $this
     */
    public function searchByDateRange($request): ListBaseRepository
    {
        if ($request->start_date && $request->end_date && $request->between_date) {
            $this->model = $this->model->whereBetween(
                $request->between_date,
                [$request->start_date, $request->end_date]
            );
        }

        return $this;
    }

    public function whereModelRelation($relations, Closure $callback) : ListBaseRepository
    {

        $this->model = $this->model->whereRelation($relations, $callback);
        return $this;
    }

    /**
     * filter
     *
     * @return $this
     */
    public function filter($request, $fields, $relations, $keyName, $table)
    {
        $query = $this->model;
        if ($request->search) {
            $query = $query->when($request, function ($query) use ($request, $fields, $relations, $keyName, $table) {
                if ($this->getParents() == 0) {
                    $query = $query->where(function ($query) use ($request, $fields) {
                        for ($i = 0; $i < count($fields); $i++) {
                            $query->orwhere($fields[$i], 'like',  '%' . strtolower($request->search) . '%');
                        }
                    });
                } else {
                    if (count($relations) > 0) {

                        for ($i = 0; $i < count($this->getParents()); $i++) {

                            $query->select($table . '.*')->join($this->getParent($i), function ($join) use ($i, $keyName, $table, $relations) {

                                $join->on(
                                    $this->getParent($i) . "." . $keyName,
                                    $table . "." . $relations[$i]
                                );
                            });
                        }

                        $selfFieldsAndParents = $this->selfFieldsAndParents;
                        $query = $query->where(function ($query) use ($request, $selfFieldsAndParents) {
                            for ($i = 0; $i < count($selfFieldsAndParents); $i++) {
                                $query->orwhere($selfFieldsAndParents[$i], 'like',  '%' . strtolower($request->search) . '%');
                            }
                        });
                    } else {
                        $query = $query->where(function ($query) use ($request, $fields) {
                            for ($i = 0; $i < count($fields); $i++) {
                                $query->orwhere($fields[$i], 'like',  '%' . strtolower($request->search) . '%');
                            }
                        });
                    }
                }
            });
        }

        $this->model = $query;
        return $this;
    }

    /**
     * paginated
     *
     * @param  mixed $request
     * @return void
     */
    public function paginated($request, $table)
    {
        $sort = $request->sort ? $table . '.' . $request->sort : $table . '.id';
        $type_sort = $request->type_sort ? $request->type_sort : 'desc';

        return $this->model->orderBy($sort, $type_sort)->paginate($request->size ? $request->size : 100);
    }

    /**
     * first
     *
     * @return void
     */
    public function first()
    {
        return $this->model->first();
    }

    /**
     * cleanQueryParams
     *
     * @param  mixed $request
     * @return void
     */
    private function cleanQueryParams($request)
    {
        return collect($request->all())
            ->except(['page', 'size', 'sort', 'type_sort', 'user_profile_id', 'search', 'data', 'conditions', 'orConditions','start_date', 'end_date', 'between_date', 'fechaEmision', 'fechaAutorizacionSRI', 'fechaPagoDocumento', 'status_payment', 'off_description'])->all();
    }


    /**
     * getCollection
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->model->get();
    }



    /**
     * Get all the loaded parents for the instance.
     *
     * @return array|int
     */
    private function getParents()
    {
        if (count($this->parents) > 0)
            return $this->parents;

        return 0;
    }

    /**
     * Get a specified parent.
     *
     * @param  string  $parent
     * @return mixed
     */
    private function getParent($parent)
    {
        return $this->parents[$parent];
    }
}
