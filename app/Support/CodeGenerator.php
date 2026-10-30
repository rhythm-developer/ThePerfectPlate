<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeGenerator
{
    /**
     * Get the next sequential code for a column, e.g. CUS-0001, ORD-2026-0001.
     *
     * @param  class-string<Model>  $modelClass
     */
    public static function next(string $modelClass, string $column, string $prefix, int $pad = 4): string
    {
        $query = $modelClass::query();

        // Soft-deleted rows still hold their code in the unique index.
        if (in_array(SoftDeletes::class, class_uses_recursive($modelClass))) {
            $query->withTrashed();
        }

        // Order by length first so CUS-10000 sorts after CUS-9999.
        $last = $query->where($column, 'like', $prefix.'%')
            ->orderByRaw("LENGTH({$column}) DESC")
            ->orderByDesc($column)
            ->value($column);

        $number = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $number, $pad, '0', STR_PAD_LEFT);
    }
}
