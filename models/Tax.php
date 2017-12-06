<?php namespace Pixiu\Commerce\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use Pixiu\Commerce\Traits\LocalizedValidation;

/**
 * Tax Model
 */
class Tax extends Model
{
    use Validation;
    use LocalizedValidation;

    public $rules = [];

    public function __construct()
    {
        parent::__construct();

        $this->localizeValidation();
        $this->rules = [
            'name' => 'required',
            'rate' => 'required|numeric|min:0'
        ];
    }

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_taxes';

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

    public function getQualifiedNameAttribute()
    {
        return $this->name . ' (' . $this->rate . '%)';
    }
}
