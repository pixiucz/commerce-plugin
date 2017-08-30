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
        return response($this->queryBuilder->getVariants(), 201);
    }

    public function show($id)
    {
        return response($this->queryBuilder->getVariantById($id), 201);
    }
}