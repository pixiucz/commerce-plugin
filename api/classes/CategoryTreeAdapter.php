<?php namespace Pixiu\Commerce\api\Classes;

use Pixiu\Commerce\Models\Category;

class CategoryTreeAdapter
{
    public function getTreeStructureFor($id)
    {
        $allCategories = Category::get();
        $selectedCategory = $allCategories->where('id', $id)->first();

        $tree = [];
        while ($selectedCategory !== null) {
            $tree[] = $selectedCategory->only(['id', 'name', 'slug']);
            $selectedCategory =
                isset($selectedCategory->parent_id)
                    ? $allCategories->where('id', $selectedCategory->parent_id)->first()
                    : null;
        }

        return $tree;
    }
}