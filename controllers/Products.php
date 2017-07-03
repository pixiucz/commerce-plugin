<?php namespace Pixiu\Commerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Pixiu\Commerce\Models\Product;
use function Stringy\create;
use System\Models\File;
use Pixiu\Commerce\Models\AttributeGroup;
use Pixiu\Commerce\Models\ProductVariant;
use Pixiu\Commerce\Models\Attribute;
use Pixiu\Commerce\Classes\Utils;

/**
 * Products Back-end Controller
 */
class Products extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct($id = null)
    {
        parent::__construct();

        $this->vars['test'] = $id;

        BackendMenu::setContext('Pixiu.Commerce', 'commerce', 'products');
    }

    public function makeProductVariantsListWidget()
    {
        $config = $this->makeConfig('$/pixiu/commerce/models/productvariant/columns.yaml');
        $config->model = new \Pixiu\Commerce\Models\ProductVariant;
        $productVariantsList = $this->makeWidget('Backend\Widgets\Lists', $config);
        $this->vars['productVariantsList'] = $productVariantsList;
    }

    public function formAfterSave($model) {
        // Gets all variants + all attribute groups
        if (!$variants = post('variant')){
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
                 *  we can only update it's EAN number
                 *  (so far only mutable attribute)
                 */
                if (array_has($variant, 'id')){
                    $this->updateProductVariant($variant['id'], $variant);
                } else {

                    /*
                     *  Otherwise we have to create new variant and
                     *  resolve all it's attributes
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


    // TODO: Are these concern of controller or model?

    /**
     * @param array $options (array of attribute group names)
     * @returns array["attribute_group_name" => attribute_group_obj]
     */
    public function resolveAttributeGroups($options)
    {
        $attrGroupWithIds = [];
        foreach ($options as $option) {
            if (!$attrGroup = AttributeGroup::where('name', $option)->first()) {
                $attrGroup = new AttributeGroup();
                $attrGroup->name = strtolower($option);
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
        $productVariant->ean = $variant['ean'];
        $productVariant->in_stock = $variant['in_stock'];
        $productVariant->product()->associate($model);
        $productVariant->save();

        foreach(json_decode($variant['attributes']) as $attribute){
            if (!$newAttribute = Attribute::where('value', $attribute->value)->where('attribute_group_id', $attributeGroups[$attribute->attributegroup->name]->id)->first()){
                $newAttribute = new Attribute();
                $newAttribute->attributegroup()->associate($attributeGroups[$attribute->attributegroup->name]);
            }
            $newAttribute->value = $attribute->value;
            $newAttribute->save();
            $newAttribute->productvariant()->attach($productVariant,  ['group_id' => $newAttribute->attribute_group_id]);
        }
    }

    /**
     * @param int $variantId
     * @param array $variantData
     */
    public function updateProductVariant(int $variantId, array $variantData)
    {
        $productVariant = ProductVariant::find($variantId);
        $productVariant->ean = $variantData['ean'];
        $productVariant->save();
    }

    public function prepareVariantsForPartials(int $id) : array
    {
        $productVariants = ProductVariant::with('attributes')->with('images')->where('product_id', $id)->get()->toArray();
        $returnArray = [];

        foreach ($productVariants as $productVariant) {
            $images = [];
            foreach ($productVariant['images'] as $image) {
                array_push($images, [
                    'url' => Utils::createAbsolutePathToImages($image['disk_name']),
                    'id' => $image['id']
                ]);
            }

            array_push($returnArray, [
                'id' => $productVariant['id'],
                'attributes' => $productVariant['attributes'],
                'images' => $images
            ]);
        }
        return $returnArray;
    }

    public function prepareVariantImagesForPartias(int $id) : array
    {

        $productImages = File::where('attachment_type', 'Pixiu\Commerce\Models\Product')
            ->where('attachment_id', $id)
            ->get()
            ->toArray();

        $returnArray = [];
        foreach ($productImages as $productImage){
            array_push($returnArray, [
                'url' => Utils::createAbsolutePathToImages($productImage['disk_name']),
                'id' => $productImage['id']
            ]);
        }
        return $returnArray;
    }


    /*
     * On button press returns partial containing
     * image-variant pairing dialog and sends
     * all product images and product variants
     * as variables.
     */
    public function onLoadCreatePictureSelector($id = null)
    {
        if ($id === null){
            $this->vars['message'] = "Product must be saved first.";
            return $this->makePartial('error_dialog');
        }
        $this->vars['productImages'] = $this->prepareVariantImagesForPartias($id);
        $this->vars['productVariants'] = $this->prepareVariantsForPartials($id);

        if ((empty($this->vars['productImages'])) || (empty($this->vars['productVariants']))){
            $this->vars['message'] = "Check if there are variants generated and pictures uploaded. Otherwise we can't perform any pairing.";
            return $this->makePartial('error_dialog');
        }

        return $this->makepartial('picture_selector');
    }

    public function onPicturesPair($id = null)
    {
        $images = post('images');
        $variants = post('variants');
        if (($images === null) || ($variants === null)){
            return;
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
    }

    public function onDeletePictureFromVariant()
    {
        $variant = ProductVariant::find(post('variantId'));
        $variant->images()->detach(post('imageId'));
    }
}
