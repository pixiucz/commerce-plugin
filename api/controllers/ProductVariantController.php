<?php namespace Pixiu\Commerce\api\Controllers;

use Pixiu\Commerce\api\Classes\VariantsQueryBuilder;

class ProductVariantController
{
    private $queryBuilder;

    public function __construct(VariantsQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function index()
    {
        $products = $this->queryBuilder->getVariants();
        return response(['products' => $products], 200);
    }

    public function show($slug)
    {
        $product = $this->queryBuilder->getVariantBySlug($slug);
        return response(['product' => $product], 200);
    }
}