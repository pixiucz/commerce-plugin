<?php namespace Pixiu\Commerce\Models;

use Model;

/**
 * Brand Model
 */
class Brand extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_brands';

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
    public $hasMany = [
        'products' => ['Pixiu\Commerce\Models\Product']
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'logo' => 'System\Models\File'
    ];
    public $attachMany = [];
}
