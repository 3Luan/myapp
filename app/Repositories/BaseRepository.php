<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Getter
     * 
     */
    abstract public function getModel();

    /**
     * Setter
     * 
     */
    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    /**
     * Get all
     * @return mixed
     */
    public function getAll()
    {
        return $this->model->orderByDesc('id');
    }

    /**
     * Get one
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $result = $this->model->find($id);
        return $result;
    }

    /**
     * Create
     * @param array $attributes
     * @return mixed
     */
    public function create($attributes = [])
    {
        return $this->model->create($attributes);
    }

    /**
     * Update
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function update($id, $attributes = [])
    {
        $result = $this->find($id);
        if ($result) {
            $result->update($attributes);
            return $result;
        }

        return false;
    }

    /**
     * Delete
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();

            return true;
        }

        return false;
    }

    /**
     * Update model detail that have change
     * @param $modelDetail
     * @param array $dataUpdate
     * @return mixed
     */
    public function updateChange($modelDetail, $dataUpdate = [], $fieldNoUpdate = [])
    {
        $fieldAble = array_diff($modelDetail->getFillable(), $fieldNoUpdate);
        $dataSave = [];
        foreach ($fieldAble as $key) {
            //can update field to empty
            if (request()->has($key) && $modelDetail[$key] != $dataUpdate[$key]) {
                $dataSave[$key] = $dataUpdate[$key];
            }
        }
        if (count($dataSave) > 0) {
            $modelDetail = $modelDetail->update($dataSave);
            return $modelDetail;
        }
        return false;
    }

    public function paginateQuery(Builder $query, array $params, string $modelType)
    {
        $search = $params['search'] ?? null;
        $currentPage = $params['currentPage'] ?? 1;
        $limit = $params['limit'] ?? 10;
        $orderElement = $params['order_element'] ?? 'id';
        $orderType = $params['order_type'] ?? 'desc';

        // Search & sort
        $config = [
            'product' => [
                'searchable' => ['name', 'description'],
                'sortable'   => ['id', 'name', 'price', 'created_at'],
            ],
            'order' => [
                'searchable' => ['id', 'status'],
                'sortable'   => ['id', 'status', 'created_at'],
            ],
            'user' => [
                'searchable' => ['email', 'name'],
                'sortable'   => ['id', 'email', 'created_at'],
            ],
            'role' => [
                'searchable' => ['id', 'name'],
                'sortable'   => ['id', 'name', 'created_at'],
            ],
            'cart' => [
                'searchable' => ['id', 'name', 'price'],
                'sortable'   => ['id', 'name', 'price'],
            ],
            'notification' => [
                'searchable' => ['id'],
                'sortable'   => ['id', 'created_at'],
            ],
        ];

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search, $config, $modelType) {
                foreach ($config[$modelType]['searchable'] as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        // Sort
        if (in_array($orderElement, $config[$modelType]['sortable'])) {
            $query->orderBy($orderElement, $orderType);
        }

        return $query->paginate($limit, ['*'], 'page', $currentPage);
    }
}
