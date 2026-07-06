<?php

declare(strict_types=1);

namespace App\Core;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected string $modelClass;

    public function findById(int $id): ?Model
    {
        return $this->modelClass::find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->modelClass::findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->modelClass::create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->fresh();
    }

    public function paginate(array $filters = [], int $perPage = 20, int $page = 1): array
    {
        $query = $this->modelClass::query();
        $query = $this->applyFilters($query, $filters);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ],
        ];
    }

    protected function applyFilters($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['sort']) && !empty($filters['order'])) {
            $query->orderBy($filters['sort'], $filters['order']);
        }
        return $query;
    }
}
