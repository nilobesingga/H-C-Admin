<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait Searchable
{
    /**
     * Scope for searching within model fields and relationships.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $search
     * @param array $fields
     * @param array $relationships
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeSearch($query, $search, $fields = [], $relationships = [])
    {
        if ($search) {
            $query->where(function ($q) use ($search, $fields, $relationships) {
                // Search in main model fields
                foreach ($fields as $field => $type) {
                    if ($type === 'string') {
                        $q->where($field, 'LIKE', '%' . $search . '%');
                    } elseif ($type === 'integer' && is_numeric($search)) {
                        $q->orWhere($field, '=', $search);
                    }
                }

                // Search in related models
                foreach ($relationships as $relation => $relationFields) {
                    $q->orWhereHas($relation, function ($subQuery) use ($search, $relationFields) {
                        foreach ($relationFields as $field => $type) {
                            if ($type === 'string') {
                                $subQuery->orWhere($field, 'LIKE', $search);
                            } elseif ($type === 'integer' && is_numeric($search)) {
                                $subQuery->orWhere($field, '=', $search);
                            }
                        }
                    });
                }
            });
        }
        return $query;
    }
}
