<?php namespace Pixiu\Commerce\api\Controllers;

use Pixiu\Commerce\Classes\Utils;
use Pixiu\Commerce\Models\Category;
use Pixiu\Commerce\api\Classes\VariantsQueryBuilder;



class CategoryController
{
    public function index($id = null)
    {
        if (isset($id)) {
            $category = Category::select('id', 'name', 'slug')->findOrFail($id);
            return response([
                'category' => $category
            ], 201);
        }

        // TODO: Should return tree instead of list
        $categories = Category::select('id', 'name', 'slug')->get();
        return response(['categories' => $categories], 201);
    }

    public function productVariants($categoryId)
    {
        $productVariants = (new VariantsQueryBuilder())->getVariantsByCategory($categoryId);
        return response($productVariants, 201);
    }
}