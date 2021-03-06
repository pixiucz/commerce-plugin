<?php namespace Pixiu\Commerce\Models;

use Model;

/**
 * OrderLog Model
 */
class OrderLog extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_order_logs';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'title', 'style'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'order' => 'Pixiu\Commerce\Models\Order'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
