<?php namespace Pixiu\Commerce\api\Controllers;

use Pixiu\Commerce\api\Classes\Paginator;
use Pixiu\Commerce\Classes\Utils;
use Pixiu\Commerce\Models\Category;
use Pixiu\Commerce\api\Classes\VariantsQueryBuilder;



class CategoryController
{
    public function index($slug = null)
    {
        if (isset($id)) {
            $category = Category::select('id', 'name', 'slug')
                ->where('slug', $slug)
                ->first();
            return response([
                'category' => $category
            ], 201);
        }

        // TODO: Should return tree instead of list
        $categories = Category::select('id', 'name', 'slug')->get();
        return response(['categories' => $categories], 201);
    }

    public function productVariants($categorySlug)
    {
//        $paginator = new Paginator($limit, $offset, $orderBy, $orderDir);

        $id = Category::where('slug', $categorySlug)->first()->id;

        $response = (new VariantsQueryBuilder())->getVariantsByCategory($id);
        return response($response, 201);
    }
}