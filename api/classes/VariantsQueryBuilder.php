<?php namespace Pixiu\Commerce\api\Classes;

use Pixiu\Commerce\Models\Category;
use Pixiu\Commerce\Models\Product;
use Pixiu\Commerce\Models\ProductVariant;
use Pixiu\Commerce\api\Classes\CategoryTreeAdapter;

class VariantsQueryBuilder
{

    private $limit;
    private $offset;
    private $orderBy;
    private $orderDir;

    public function __construct()
    {
        $this->prepareConstrains();
    }

    public function getVariants()
    {
        $variants = $this->getBasicQuery()
            ->select('id', 'slug', 'ean', 'product_id', 'primary_picture_id', 'in_stock', 'price', 'specifications', 'created_at')
            ->orderBy($this->orderBy, $this->orderDir)
            ->skip($this->offset)->take($this->limit)
            ->get()->map(function($item) {
                $array = $item->toArray();
                foreach ($array['attributes'] as &$attr) {
                    $attr['name'] = $attr['attributegroup']['name'];
                    unset($attr['attributegroup'], $attr['pivot'], $attr['id']);
                }
                $this->flattenProductArray($array);
                return $array;
            });
        return $variants;
    }

    public function getVariantsByCategory(int $categoryId)
    {
        // FIXME: Overwrite for performance
        $variants = $this->getBasicQuery()
            ->whereHas('product.categories', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->select('id', 'slug', 'ean', 'product_id', 'primary_picture_id', 'in_stock', 'price', 'specifications', 'created_at')
            ->orderBy($this->orderBy, $this->orderDir)
            ->skip($this->offset)->take($this->limit)
            ->get()
            ->map(function($item) {
                $array = $item->toArray();
                foreach ($array['attributes'] as &$attr) {
                    $attr['name'] = $attr['attributegroup']['name'];
                    unset($attr['attributegroup'], $attr['pivot'], $attr['id']);
                }
                $this->flattenProductArray($array);
                return $array;
            });

        $count = ProductVariant::whereHas('product.categories', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        })->get()->count();

        return [
            'products' => $variants,
            'totalCount' => $count
        ];
    }

    public function getVariantBySlug($slug)
    {
        $array = $this->getBasicQuery()
            ->with('product.categories')
            ->select('id', 'slug', 'ean', 'product_id', 'primary_picture_id', 'in_stock', 'price', 'specifications', 'created_at')
            ->where('slug', $slug)
            ->firstOrFail()
            ->toArray();
        foreach ($array['attributes'] as &$attr) {
            $attr['name'] = $attr['attributegroup']['name'];
            unset($attr['attributegroup'], $attr['pivot'], $attr['id']);
        }
        $this->addCategoryAttribute($array);
        $this->flattenProductArray($array);
        $array['otherVariants'] = $this->getVariantsByProductId($array['product_id']);
        return $array;
    }

    public function getVariantsByProductId($productId)
    {
        $variants = $this->getBasicQuery()
            ->where('product_id', $productId)
            ->get()
            ->each(function($item) {
                $this->flattenProductArray($item);
            })
            ->toArray();

        return $variants;
    }

    private function addCategoryAttribute(&$array)
    {
        foreach ($array['product']['categories'] as $category) {
            $array['categories'][] = app('CategoryTreeAdapter')->getTreeStructureFor($category['id']);
        }
        unset($array['product']['categories']);
    }

    private function flattenProductArray(&$array)
    {
        $array['brand_name'] = $array['product']['brand']['brand'];
        $array['product_name'] = $array['product']['name'];
        $array['product_id'] = $array['product']['id'];
        $array['tax_rate'] = $array['product']['tax']['tax_rate'];
        $array['tax_name'] = $array['product']['tax']['tax_name'];
        $array['decomposite_on'] = $array['product']['decomposite_on'];
        $array['short_description'] = array_get($array, 'product.short_description');
        $array['long_description'] = array_get($array, 'product.long_description');

        unset($array['primary_picture_id'],
            $array['product']
        );
    }

    private function getBasicQuery()
    {
        return ProductVariant::
            with(['attributes' => function ($q) {
                $q->select('id', 'value', 'attribute_group_id');
            }, 'attributes.attributegroup' => function ($q) {
                $q->select('name', 'id');
            }])
            ->with(['product' => function ($q) {
                $q->select('id', 'tax_id', 'brand_id', 'name', 'short_description', 'specifications');
            }, 'product.brand' => function ($q) {
                $q->select('id', 'name AS brand', 'description AS brand_description');
            }, 'product.tax' => function ($q) {
                $q->select('id', 'rate AS tax_rate', 'name AS tax_name');
            }, 'product.decomposite_on' => function ($q) {
                $q->select('attribute_group_id');
            }])
            ->with(['primary_picture' => function ($q) {
                $q->select('id', 'disk_name');
            }])
            ->with(['images' => function ($q) {
                $q->select('disk_name');
            }]);
    }

    private function prepareConstrains()
    {
        // FIXME: Use paginator object with these values
        $this->offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $this->limit = isset($_GET['limit']) ? $_GET['limit'] : 25;
        $this->orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'id';
        $this->orderDir = isset($_GET['orderDir']) ? $_GET['orderDir'] : 'desc';
    }

}