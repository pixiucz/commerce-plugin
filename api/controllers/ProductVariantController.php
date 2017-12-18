<?php namespace Pixiu\Commerce\api\Controllers;

use Pixiu\Commerce\api\Classes\VariantsQueryBuilder;

class ProductVariantController
{
    private $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new VariantsQueryBuilder();
    }

    public function index()
    {
        return response($this->queryBuilder->getVariants(), 200);
    }

    public function show($slug)
    {
        $product = $this->queryBuilder->getVariantBySlug($slug);
        return response(['product' => $product], 200);
    }
}