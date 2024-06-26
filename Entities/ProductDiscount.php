<?php

namespace Modules\Icommerce\Entities;

use Astrotomic\Translatable\Translatable;
use Modules\Core\Icrud\Entities\CrudModel;
use Modules\Core\Support\Traits\AuditTrait;
use Modules\Iprofile\Entities\Department;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ProductDiscount extends CrudModel
{
  use BelongsToTenant;

  protected $table = 'icommerce__product_discounts';
  public $transformer = 'Modules\Icommerce\Transformers\ProductDiscountTransformer';
  public $repository = 'Modules\Icommerce\Repositories\ProductDiscountRepository';
  public $requestValidation = [
    'create' => 'Modules\Icommerce\Http\Requests\CreateProductDiscountRequest',
    'update' => 'Modules\Icommerce\Http\Requests\UpdateProductDiscountRequest',
  ];
  //Instance external/internal events to dispatch with extraData
  public $dispatchesEventsWithBindings = [
    //eg. ['path' => 'path/module/event', 'extraData' => [/*...optional*/]]
    'created' => [],
    'creating' => [],
    'updated' => [],
    'updating' => [],
    'deleting' => [],
    'deleted' => []
  ];

    protected $fillable = [
        'product_id',
        'product_option_id',
        'product_option_value_id',
        'quantity',
        'quantity_sold',
        'priority',
        'discount',
        'criteria',
        'date_start',
        'date_end',
        'department_id',
        'exclude_departments',
        'include_departments',
    ];

    protected $casts = [
        'exclude_departments' => 'array',
        'include_departments' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productOptionValue()
    {
        return $this->belongsTo(ProductOptionValue::class);
    }

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function getFinishedAttribute()
    {
        $now = date('Y-m-d');

        $endDate = date('Y-m-d', strtotime($this->date_end));

        return $this->quantity_sold >= $this->quantity
          || ($now > $endDate);
    }

    public function getRunningAttribute()
    {
        $now = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime($this->date_start));
        $endDate = date('Y-m-d', strtotime($this->date_end));

        return $this->quantity_sold < $this->quantity
          && (($now >= $startDate) && ($now <= $endDate));
    }

    public function getPriceAttribute()
    {
        $basePrice = $this->product->price;
        $valueDiscount = $this->calcDiscount($basePrice);

        return floatval($basePrice) - floatval($valueDiscount);
    }

  public function getTotalDiscountAttribute()
  {

    $basePrice = $this->product->price;
    return $this->calcDiscount($basePrice);

  }

    public function setExcludeDepartmentsAttribute($value)
    {
        $this->attributes['exclude_departments'] = json_encode($value);
    }

    public function getExcludeDepartmentsAttribute($value)
    {
        return json_decode($value);
    }

    public function setIncludeDepartmentsAttribute($value)
    {
        $this->attributes['include_departments'] = json_encode($value);
    }

    public function getIncludeDepartmentsAttribute($value)
    {
        return json_decode($value);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function calcDiscount($value)
    {
        if ($this->criteria == 'fixed') {
            return $this->discount;
        }

        if ($this->criteria == 'percentage') {
            return floatval(($value * $this->discount) / 100);
        }
    }
}
