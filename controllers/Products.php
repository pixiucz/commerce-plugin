<?php namespace Pixiu\Commerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Support\Facades\Redirect;
use October\Rain\Support\Facades\Flash;
use Pixiu\Commerce\Models\Product;
use function Stringy\create;
use System\Models\File;
use Pixiu\Commerce\Models\AttributeGroup;
use Pixiu\Commerce\Models\ProductVariant;
use Pixiu\Commerce\Models\Attribute;
use Pixiu\Commerce\Classes\Utils;
use Illuminate\Support\Facades\Lang;

/**
 * Products Back-end Controller
 */
class Products extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct($id = null)
    {
        parent::__construct();

        $this->vars['test'] = $id;

        BackendMenu::setContext('Pixiu.Commerce', 'commerce', 'products');
    }

    public function update($id = null)
    {
        parent::update($id);
        $this->vars['picturesListVariants'] = $this->prepareVariantsForPartials($id);
        $this->vars['picturesListImages'] = $this->prepareVariantImagesForPartias($id);
    }

    public function makeProductVariantsListWidget()
    {
        $config = $this->makeConfig('$/pixiu/commerce/models/productvariant/columns.yaml');
        $config->model = new \Pixiu\Commerce\Models\ProductVariant;
        $productVariantsList = $this->makeWidget('Backend\Widgets\Lists', $config);
        $this->vars['productVariantsList'] = $productVariantsList;
    }

    public function formAfterSave($model) {
        /*
         *  If no variant exist, create 'empty' variant
         */
        if (!$variants = post('variant')){
            $this->handleEmptyVariant($model);
            return;
        };

        $options = post('options');

        /*
         *  Gets rid of all variants that are no-longer in form
         */
        ProductVariant::where('product_id', $model->id)->whereNotIn('id', array_pluck($variants, 'id'))->delete();

        foreach ($variants as $variant) {
            if (array_has($variant, 'exists')){

                /*
                 *  If Product variant already exists,
                 *  we can only update it's EAN number and Price
                 */
                if (array_has($variant, 'id')){
                    $this->updateProductVariant($variant['id'], $model, $variant);
                } else {

                    /*
                     *  Otherwise we have to create new variant and
                     *  resolve all it's attributes and their groups
                     */
                    $this->createProductVariant(
                        $variant,
                        $model,
                        $this->resolveAttributeGroups(array_pluck($options, 'name'))
                    );
                }
            }
        }
    }

    /**
     * @param array $options (array of attribute group names)
     * @returns array["attribute_group_name" => attribute_group_obj]
     */
    public function resolveAttributeGroups($options)
    {
        $attrGroupWithIds = [];
        foreach ($options as $option) {
            $attrGroup = AttributeGroup::whereRaw('lower(name) = ?', [strtolower($option)])->first();
            if ($attrGroup === null) {
                $attrGroup = new AttributeGroup();
                $attrGroup->name = $option;
                $attrGroup->save();
            }
            $attrGroupWithIds = array_add($attrGroupWithIds, $option, $attrGroup);
        }

        return $attrGroupWithIds;
    }

    /**
     * @param array $variant
     * @param Product $model
     * @return ProductVariant
     */
    public function createProductVariant(array $variant, Product $model, array $attributeGroups)
    {
        $productVariant = new ProductVariant();
        $productVariant->price = $variant['price'] === "" ? $model->retail_price : $variant['price'];
        $productVariant->ean = $variant['ean'];
        $productVariant->in_stock = $variant['in_stock'];
        $productVariant->product()->associate($model);
        $productVariant->slug = "temp " . random_int(1, 10);
        $productVariant->save();

        $slug = $this->getBasicSlug($model);

        foreach(json_decode($variant['attributes']) as $attribute){
            $newAttribute = Attribute::whereRaw('lower(value) = ?', [strtolower($attribute->value)])->where('attribute_group_id', $attributeGroups[$attribute->attributegroup->name]->id)->first();
            if ($newAttribute === null){
                $newAttribute = new Attribute();
                $newAttribute->attributegroup()->associate($attributeGroups[$attribute->attributegroup->name]);
            }
            $newAttribute->value = $attribute->value;
            $slug .= ' ' . $attribute->value;
            $newAttribute->save();
            $newAttribute->productvariant()->attach($productVariant,  ['group_id' => $newAttribute->attribute_group_id]);
        }

        $productVariant->slug = str_slug($slug, '-');
        $productVariant->save();

    }

    /**
     * @param int $variantId
     * @param array $variantData
     */
    public function updateProductVariant(int $variantId, Product $model, array $variantData)
    {
        $productVariant = ProductVariant::find($variantId);
        $productVariant->price = $variantData['price'] === "" ? $model->price : $variantData['price'];
        $productVariant->ean = $variantData['ean'];
        $productVariant->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Partials handling
    |--------------------------------------------------------------------------
    |
    |
    */

    /*
     *  Returns array with prepared variants
     */
    public function prepareVariantsForPartials(int $id) : array
    {
        $productVariants = ProductVariant::with('attributes')->with('images')->where('product_id', $id)->get()->toArray();
        $returnArray = [];

        foreach ($productVariants as $productVariant) {
            $images = [];
            foreach ($productVariant['images'] as $image) {
                array_push($images, [
                    'url' => Utils::getAbsolutePathToImages($image['disk_name']),
                    'id' => $image['id']
                ]);
            }

            array_push($returnArray, [
                'id' => $productVariant['id'],
                'attributes' => $productVariant['attributes'],
                'images' => $images,
                'primary_picture_id' => $productVariant['primary_picture_id']
            ]);
        }
        return $returnArray;
    }

    /*
     *  Returns array with preparet iamges for each variant
     */
    public function prepareVariantImagesForPartias(int $id) : array
    {

        $productImages = File::where('attachment_type', 'Pixiu\Commerce\Models\Product')
            ->where('attachment_id', $id)
            ->get()
            ->toArray();

        $returnArray = [];
        foreach ($productImages as $productImage){
            array_push($returnArray, [
                'url' => Utils::getAbsolutePathToImages($productImage['disk_name']),
                'id' => $productImage['id']
            ]);
        }
        return $returnArray;
    }

    /*
     *  Partial with dialog allowing user to
     *  attach any uploaded image to any variant
     */
    public function onPicturesToVariantsPartial($id = null)
    {
        $this->vars['productImages'] = $this->prepareVariantImagesForPartias($id);
        $this->vars['productVariants'] = $this->prepareVariantsForPartials($id);

        if ((empty($this->vars['productImages'])) || (empty($this->vars['productVariants']))){
            $this->vars['message'] = Lang::get('pixiu.commerce::lang.other.noimages_or_novariants');
            return $this->makePartial('error_dialog');
        }

        return $this->makepartial('picture_selector');
    }

    /*
     *  Partial that allows user to select one
     *  picture from uploaded pictures as primary picture
     */
    public function onPrimaryPictureForVariantsPartial($id = null)
    {
        $this->vars['productImages'] = $this->prepareVariantImagesForPartias($id);
        $this->vars['productVariants'] = $this->prepareVariantsForPartials($id);
        return $this->makePartial('primary_picture');
    }

    public function onAttachPicturesToVariants($id = null)
    {
        $images = post('images');
        $variants = post('variants');
        if (($images === null) || ($variants === null)){
            return back();
        }

        foreach ($variants as $variant) {
            $productVariant = ProductVariant::with('images')->findOrFail($variant);
            foreach ($images as $image){
                if (!$productVariant->images->contains($image)){
                    $productImage = File::findOrFail($image);
                    $productVariant->images()->attach($productImage);
                }
            }
        }

        Flash::success(Lang::get('pixiu.commerce::lang.other.pictures_added'));
        return back();
    }

    public function onSavePrimaryPictures($id = null)
    {
        $primaryImages = post('primaryImages');

        if ($primaryImages === null){
            return Flash::warning('No pictures selected');
        }

        foreach ($primaryImages as $variantId => $imageId) {
            $variant = ProductVariant::findOrFail($variantId);
            $image = File::findOrFail($imageId);
            $variant->primary_picture = $image;
            $variant->save();
        }

        Flash::success(Lang::get('pixiu.commerce::lang.other.saved_primary_pictures'));
        return back();
    }

    public function onDeletePictureFromVariant()
    {
        $variant = ProductVariant::find(post('variantId'));
        $variant->images()->detach(post('imageId'));
    }

    /**
     * @param $model
     */
    private function handleEmptyVariant($model): void
    {
        if (!$productVariant = $model->productvariants->first()) {
            $productVariant = new ProductVariant();
            $productVariant->in_stock = post('Product._in_stock');
            $productVariant->product()->associate($model);
        };
        $productVariant->ean = post('Product._ean');
        $productVariant->price = post('Product.retail_price');

        $slug = $this->getBasicSlug($model);
        $productVariant->slug = str_slug($slug, '-');

        if ($stockChange = post('Product._change_stock')){
            $productVariant->in_stock += $stockChange;
        }

        $productVariant->save();

        $model->images()->get()->each(function($item, $key) use ($productVariant) {
            if (!$productVariant->images->contains($item)){
                $productVariant->images()->attach($item);
            }
        });
    }

    /**
     * @param $model
     * @return string
     */
    private function getBasicSlug($model): string
    {
        $slug = "";
        if (count($model->brand)) {
            $slug = $model->brand->name . ' ';
        }
        $slug .= $model->name;
        return $slug;
    }
}
