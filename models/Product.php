<?php namespace Pixiu\Commerce\Models;

use Model;

/**
 * Product Model
 */
class Product extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_commerce_products';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}