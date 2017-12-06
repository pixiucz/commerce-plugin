<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Traits\LocalizedValidation;

/**
 * OrderLog Model
 */
class OrderNote extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use LocalizedValidation;

    public $rules = [];

    public function __construct()
    {
        parent::__construct();

        $this->localizeValidation();
        $this->rules = [
            'title' => 'required'
        ];
    }
    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_order_notes';

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
