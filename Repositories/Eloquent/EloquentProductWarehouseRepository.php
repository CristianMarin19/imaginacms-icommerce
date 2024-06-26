<?php

namespace Modules\Icommerce\Repositories\Eloquent;

use Modules\Icommerce\Repositories\ProductWarehouseRepository;
use Modules\Core\Icrud\Repositories\Eloquent\EloquentCrudRepository;
use Modules\Icommerce\Events\ProductWarehouseWasCreated;
use Modules\Icommerce\Events\ProductWarehouseWasUpdated;
use Modules\Icommerce\Events\ProductWarehouseWasDeleted;

class EloquentProductWarehouseRepository extends EloquentCrudRepository implements ProductWarehouseRepository
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
   * Attribute to define default relations
   * all apply to index and show
   * index apply in the getItemsBy
   * show apply in the getItem
   * @var array
   */
  protected $with = [/*all => [] ,index => [],show => []*/];

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

  public function create($data)
  {
    $model = parent::create($data);
    event(new ProductWarehouseWasCreated($model));
    return $model;
  }

  public function updateBy($criteria, $data, $params = false)
  {
    $model = parent::updateBy($criteria, $data, $params = false);
    event(new ProductWarehouseWasUpdated($model));
    return $model;
  }

  public function deleteBy($criteria, $params = false)
  {
    $model = parent::deleteBy($criteria, $params = false);
    event(new ProductWarehouseWasDeleted($model));
    return $model;
  }

}
