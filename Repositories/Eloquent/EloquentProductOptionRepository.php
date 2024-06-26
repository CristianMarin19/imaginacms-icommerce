<?php

namespace Modules\Icommerce\Repositories\Eloquent;

use Modules\Icommerce\Repositories\ProductOptionRepository;
use Modules\Core\Icrud\Repositories\Eloquent\EloquentCrudRepository;

class EloquentProductOptionRepository extends EloquentCrudRepository implements ProductOptionRepository
{
  /**
   * Filter names to replace
   * @var array
   */
  protected $replaceFilters = [];

  /**
   * Relation names to replace
   * @var array
   */
  protected $replaceSyncModelRelations = [];

  /**
   * Attribute to customize relations by default
   * @var array
   */
  protected $with = ['all' => ['option','productOptionValues']];

  /**
   * Filter query
   *
   * @param $query
   * @param $filter
   * @param $params
   * @return mixed
   */
  public function filterQuery($query, $filter, $params)
  {

    /**
     * Note: Add filter name to replaceFilters attribute before replace it
     *
     * Example filter Query
     * if (isset($filter->status)) $query->where('status', $filter->status);
     *
     */

    // add filter by search
    if (isset($filter->search)) {
      //find search in columns
      $query->where(function ($query) use ($filter) {
        $query->whereHas('translations', function ($query) use ($filter) {
          $query->where('locale', $filter->locale)
            ->where('description', 'like', '%' . $filter->search . '%');
        })->orWhere('id', 'like', '%' . $filter->search . '%')
          ->orWhere('price', 'like', '%' . $filter->search . '%')
          ->orWhere('quantity', 'like', '%' . $filter->search . '%')
          ->orWhere('updated_at', 'like', '%' . $filter->search . '%')
          ->orWhere('created_at', 'like', '%' . $filter->search . '%');
      });
    }

    /*== By parent Option Value ==*/
    if (isset($filter->parentOptionValue))
      $query->where('parent_option_value_id', $filter->parentOptionValue);


    //Response
    return $query;
  }

  /**
   * Method to sync Model Relations
   *
   * @param $model ,$data
   * @return $model
   */
  public function syncModelRelations($model, $data)
  {
    //Get model relations data from attribute of model
    $modelRelationsData = ($model->modelRelations ?? []);

    /**
     * Note: Add relation name to replaceSyncModelRelations attribute before replace it
     *
     * Example to sync relations
     * if (array_key_exists(<relationName>, $data)){
     *    $model->setRelation(<relationName>, $model-><relationName>()->sync($data[<relationName>]));
     * }
     *
     */

    //Response
    return $model;
  }

  public function updateBy($criteria, $data, $params = false)
  {

    /*== REQUEST ==*/
    $model = $this->model->where($field ?? 'id', $criteria)->first();

    //If parent id change, set null all product parent option Values
    if ($model->parent_id != $data['parent_id']) {
      \DB::table('icommerce__product_option_value')->where('product_option_id', $criteria)
        ->update(['parent_option_value_id' => null]);
    }

    return parent::updateBy($criteria, $data, $params); // TODO: Change the autogenerated stub
  }
}
