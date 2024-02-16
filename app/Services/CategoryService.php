<?php
namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public static function get($allLayer = false, $editId = null)
    {
        if (config('settings.category_layer') == 1 && !$allLayer) {
            return null;
        }

        $sql = Category::select('id', 'name');

        if (config('settings.category_layer') >= 2) {
            $sql->with(['children' => function($q) use ($editId) {
                $q->select('id', 'name', 'parent_id');
                if ($editId) {
                    $q->where('id', '!=', $editId);
                }
            }]);
            $sql->whereNull('parent_id');
            if ($editId) {
                $sql->where('id', '!=', $editId);
            }
        }

        return $sql->where('status', 'Active')->get();
    }
}