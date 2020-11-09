<?php

namespace Modules\Icommerce\Repositories\Eloquent;

use Modules\Icommerce\Repositories\ManufacturerRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentManufacturerRepository extends EloquentBaseRepository implements ManufacturerRepository
{
  public function getItemsBy($params = false)
  {
    // INITIALIZE QUERY
    $query = $this->model->query();
    
    // RELATIONSHIPS
    //$defaultInclude = ['translations'];
    //$query->with(array_merge($defaultInclude, $params->include));

    /*== RELATIONSHIPS ==*/
    if (in_array('*', $params->include)) {//If Request all relationships
      $query->with(['translations']);
    } else {//Especific relationships
      $includeDefault = ['translations'];//Default relationships
      if (isset($params->include))//merge relations with default relationships
        $includeDefault = array_merge($includeDefault, $params->include);
      $query->with($includeDefault);//Add Relationships to query
    }
    
    // FILTERS
    if ($params->filter) {
      $filter = $params->filter;
      
      //get language translation
      $lang = \App::getLocale();
      
      //add filter by search
      if (isset($filter->search)) {
        
        //find search in columns
        $query->where(function ($query) use ($filter, $lang) {
          $query->whereHas('translations', function ($query) use ($filter, $lang) {
            $query->where('locale', $lang)
              ->where('name', 'like', '%' . $filter->search . '%');
          })->orWhere('id', 'like', '%' . $filter->search . '%');
        });
      }
        if (isset($filter->store)) {
            $query->where('store_id', $filter->store);
        }
      //add filter by status
      if (!empty($filter->status)) {
        $query->where('status', $filter->status);
      }
    }
  
    /*== FIELDS ==*/
    if (isset($params->fields) && count($params->fields))
      $query->select($params->fields);
  
    /*== REQUEST ==*/
    if (isset($params->page) && $params->page) {
      return $query->paginate($params->take);
    } else {
      $params->take ? $query->take($params->take) : false;//Take
      return $query->get();
    }
  }
  
  public function getItem($criteria, $params)
  {
    // INITIALIZE QUERY
    $query = $this->model->query();
    
    $query->where('id', $criteria);
    
    // RELATIONSHIPS
    $includeDefault = ['translations'];
    $query->with(array_merge($includeDefault, $params->include));
    
    // FIELDS
    if ($params->fields) {
      $query->select($params->fields);
    }
    return $query->first();
    
  }
  
  
  public function create($data)
  {
    
    $manufacturer = $this->model->create($data);
    
    
    return $manufacturer;
  }
  
  
  public function updateBy($criteria, $data, $params){
    
    // INITIALIZE QUERY
    $query = $this->model->query();
    
    // FILTER
    if (isset($params->filter)) {
      $filter = $params->filter;
      
      if (isset($filter->field))//Where field
        $query->where($filter->field, $criteria);
      else//where id
        $query->where('id', $criteria);
    }
  
    // REQUEST
    $model = $query->first();
  
    if($model) {
      $model->update($data);
    }
    return $model;
    
  }
  
  public function deleteBy($criteria, $params)
  {
    // INITIALIZE QUERY
    $query = $this->model->query();
    
    // FILTER
    if (isset($params->filter)) {
      $filter = $params->filter;
      
      if (isset($filter->field)) //Where field
        $query->where($filter->field, $criteria);
      else //where id
        $query->where('id', $criteria);
    }
  
    // REQUEST
    $model = $query->first();
  
    if($model) {
      $model->delete();
    }
  }
}
