<?php

namespace Modules\Icommerce\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\Media\Entities\File;
use Modules\Core\Traits\NamespacedEntity;

class Product extends Model
{
  use Translatable, NamespacedEntity, MediaRelation;

  protected $table = 'icommerce__products';
  public $translatedAttributes = [
    'name',
    'description',
    'summary',
    'slug',
    'meta_title',
    'meta_description'
  ];
  protected $fillable = [
    'added_by_id',
    'options',
    'status',
    'category_id',
    'parent_id',
    'tax_class_id',
    'sku',
    'quantity',
    'stock_status',
    'manufacturer_id',
    'shipping',
    'price',
    'points',
    'date_available',
    'weight',
    'length',
    'width',
    'height',
    'subtract',
    'minimum',
    'reference',
    'rating',
    'freeshipping',
    'order_weight'
  ];
  protected $fakeColumns = ['options'];
  protected $casts = [
    'options' => 'array'
  ];

  public function addedBy()
  {
    $driver = config('asgard.user.config.driver');
    return $this->belongsTo('Modules\\User\\Entities\\{$driver}\\User', 'added_by_id');
  }

  public function getStatus()
  {
    $status = new Status();
    return $status->get($this->status);
  }

  public function stockStatus()
  {
    $stockStatus = new StockStatus();
    return $stockStatus->get($this->stock_status);
  }

  public function priceLists()
  {
    return $this->belongsToMany(PriceList::class, 'icommerce__product_lists')
      ->withPivot('id', 'price')
      ->withTimestamps();
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function taxClass()
  {
    return $this->belongsTo(TaxClass::class);
  }

  public function categories()
  {
    return $this->belongsToMany(Category::class, 'icommerce__product_category')->withTimestamps();
  }

  public function tags()
  {
    return $this->belongsToMany(Tag::class, 'icommerce__product_tag')->withTimestamps();
  }

  public function orderItems()
  {
    return $this->hasMany(OrderItem::class, 'product_id');
  }

  //public function featuredProducts()
  //{
  //return $this->hasMany(OrderItem::class)->select('SUM(quantity) AS total_products')->groupBy('product_id');
  //}

  public function manufacturer()
  {
    return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
  }

  public function discounts()
  {
    return $this->hasMany(ProductDiscount::class);
  }

  public function productOptions()
  {
    return $this->belongsToMany(Option::class, 'icommerce__product_option')
      ->withPivot('id', 'parent_id', 'parent_option_value_id', 'value', 'required')
      ->withTimestamps();
  }

  public function optionValues()
  {
    return $this->belongsToMany(OptionValue::class, 'icommerce__product_option_value')
      ->withPivot(
        'id', 'product_option_id', 'option_id',
        'parent_option_value_id', 'quantity',
        'subtract', 'price', 'weight'
      )->withTimestamps();
  }

  public function relatedProducts()
  {
    return $this->belongsToMany(
      'Modules\Icommerce\Entities\Product',
      'icommerce__related_product',
      'product_id', 'related_id'
    )->withTimestamps();
  }

  public function orders()
  {
    return $this->belongsToMany(Order::class, 'icommerce__order_item')
      ->withPivot('title', 'reference', 'quantity', 'price', 'total', 'tax', 'reward')
      ->withTimestamps()
      ->using(OrderItem::class);
  }

  public function wishlists()
  {
    return $this->hasMany(Wishlist::class);
  }

  public function coupons()
  {
    return $this->belongsToMany(Coupon::class, 'icommerce__coupon_product')->withTimestamps();
  }

  public function parent()
  {
    return $this->belongsTo('Modules\Icommerce\Entities\Product', 'parent_id');
  }

  public function children()
  {
    return $this->hasMany('Modules\Icommerce\Entities\Product', 'parent_id')
      ->orderBy('order_weight', 'desc')
      ->orderBy('created_at', 'desc');
  }

  public function comments()
  {
    return $this->hasMany(Comment::class);
  }

  public function carts()
  {
    return $this->hasMany(CartProduct::class);
  }

  protected function setSlugAttribute($value)
  {

    if (!empty($value)) {
      $this->attributes['slug'] = istr_slug($value, '-');
    } else {
      $this->attributes['slug'] = str_slug($this->title, '-');
    }

  }

  protected function setSummaryAttribute($value)
  {

    if (!empty($value)) {
      $this->attributes['summary'] = $value;
    } else {
      $this->attributes['summary'] = substr(strip_tags($this->description), 0, 150);
    }

  }

  protected function setQuantityAttribute($value)
  {

    if (!empty($value)) {
      $this->attributes['quantity'] = $value;
    } else {
      $this->attributes['quantity'] = 0;
    }

  }

  protected function setPriceAttribute($value)
  {

    if (!empty($value)) {
      $this->attributes['price'] = $value;
    } else {
      $this->attributes['price'] = 0;
    }

  }

  protected function setMinimumAttribute($value)
  {

    if (!empty($value)) {
      $this->attributes['minimum'] = $value;
    } else {
      $this->attributes['minimum'] = 1;
    }

  }

  protected function setSkuAttribute($value)
  {

    if (!empty($value)) {
      $this->attributes['sku'] = $value;
    } else {
      $this->attributes['sku'] = uniqid("s");
    }

  }

  protected function setOptionsAttribute($value)
  {
    $this->attributes['options'] = json_encode($value);
  }

  public function getOptionsAttribute($value)
  {
    return json_decode($value);
  }

  /*public function getUrlAttribute()
  {
    return \URL::route(\LaravelLocalization::getCurrentLocale() . '.icommerceslug.' . $this->slug);
  }*/

  protected function setRatingAttribute($value)
  {

    if (!empty($value)) {
      $this->attributes['rating'] = $value;
    } else {
      $this->attributes['rating'] = 3;
    }

  }

  public function getDiscountAttribute()
  {
    $date = date_create(date("Y/m/d"));

    $query = $this->product_discounts()
      ->select('price')
      ->whereDate('date_start', '<=', $date)
      ->whereDate('date_end', '>=', $date)
      ->first();

    return $query ? $query->price : null;
  }

  public function getMainImageAttribute()
  {
    $thumbnail = $this->files()->where('zone', 'mainimage')->first();
    if(!$thumbnail) return [
      'mimeType' => 'image/jpeg',
      'path' =>url('modules/iblog/img/post/default.jpg')
    ];
    return [
      'mimeType' => $thumbnail->mimetype,
      'path' => $thumbnail->path_string
    ];
  }

  public function getGalleryAttribute()
  {
    $gallery = $this->filesByZone('gallery')->get();
    $response = [];
    foreach ($gallery as $img){
      array_push($response,[
        'mimeType' => $img->mimetype,
        'path' => $img->path_string
      ]);
    }
    return $response;
  }

  /*
  public function getRelatedIdsAttribute($value)
  {

    if (!empty($value)) {
      $ids = json_decode($value);
      $productsRelated = Product::whereIn("id", $ids)->take(20)->get();
      return $productsRelated;
    }

  }*/
}
