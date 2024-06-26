<?php

namespace Modules\Icommerce\Repositories\Eloquent;

use Modules\Icommerce\Repositories\OrderRepository;
use Modules\Core\Icrud\Repositories\Eloquent\EloquentCrudRepository;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class EloquentOrderRepository extends EloquentCrudRepository implements OrderRepository
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
  protected $with = [
    'index' => ['customer', 'addedBy', 'paymentCountry', 'shippingCountry', 'shippingDepartment', 'paymentDepartment'],
    'show' => ['customer', 'addedBy', 'orderItems', 'orderHistory', 'transactions', 'coupons',
      'paymentCountry', 'shippingCountry', 'shippingDepartment', 'paymentDepartment']
  ];


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

    //add filter by search
    if (isset($filter->search)) {
      //find search in columns
      $query->where('id', 'like', '%' . $filter->search . '%')
        ->orWhere('invoice_nro', 'like', '%' . $filter->search . '%')
        ->orWhere('status_id', 'like', '%' . $filter->search . '%')
        ->orWhere('first_name', 'like', '%' . $filter->search . '%')
        ->orWhere('last_name', 'like', '%' . $filter->search . '%')
        ->orWhere('email', 'like', '%' . $filter->search . '%')
        ->orWhere('payment_first_name', 'like', '%' . $filter->search . '%')
        ->orWhere('payment_last_name', 'like', '%' . $filter->search . '%')
        ->orWhere('shipping_first_name', 'like', '%' . $filter->search . '%')
        ->orWhere('shipping_last_name', 'like', '%' . $filter->search . '%')
        ->orWhere('updated_at', 'like', '%' . $filter->search . '%')
        ->orWhere('created_at', 'like', '%' . $filter->search . '%');
    }

    if (isset($filter->status)) {
      $query->where('status_id', $filter->status);
    }

      if (isset($filter->warehouseId)) {
        $query->where('warehouse_id', $filter->warehouseId);
      }

    if (isset($filter->customer)) {

      // if has permission
      $indexPermission = $params->permissions['icommerce.orders.index'] ?? false; // index orders
      $showOthersPermission = $params->permissions['icommerce.orders.show-others'] ?? false; // show orders of others

      $user = $params->user;
      if ($showOthersPermission || ($filter->customer == $user->id && $indexPermission)) {
        $query->where('customer_id', $filter->customer);
      }
    }

    if (!isset($params->filter->order)) {
      $query->orderBy("created_at", "desc");//Add order to query
    }

    // if has permission show-others
    $showOthersPermission = $params->permissions['icommerce.orders.show-others'] ?? false; // show orders of others

    if (!$showOthersPermission && !isset($filter->field)) {
      $query->where('customer_id', $params->user->id)->where('parent_id', null);
    }


    $entitiesWithCentralData = json_decode(setting("icommerce::tenantWithCentralData", null, "[]"));
    $tenantWithCentralData = in_array("orders", $entitiesWithCentralData);


    if ($tenantWithCentralData && isset(tenant()->id)) {
      $model = $this->model;

      $query->withoutTenancy();
      $query->where(function ($query) use ($model) {
        $query->where($model->qualifyColumn(BelongsToTenant::$tenantIdColumn), tenant()->getTenantKey())
          ->orWhere(function ($query) use ($model) {
            $authUser = \Auth::user();
            $query->whereNull($model->qualifyColumn(BelongsToTenant::$tenantIdColumn))
              ->where("customer_id", $authUser->id ?? null);
          });
      });
    }

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
}
