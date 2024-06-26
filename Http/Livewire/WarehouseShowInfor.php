<?php

namespace Modules\Icommerce\Http\Livewire;

use Livewire\Component;


class WarehouseShowInfor extends Component
{
  public $layout;
  public $view;
  public $log;
  public $readyToLoad = false;

  public $varName;
  public $varAtt;

  /**
   * MOUNT
   */
  public function mount(
    $layout = 'warehouse-show-infor-layout-1', 
    $varName,
    $varAtt  
  ){
      $this->log = "Icommerce::Livewire|WarehouseShowInfor|";
      $this->layout = $layout;
      $this->view = "icommerce::frontend.livewire.warehouse-show-infor.layouts.$this->layout.index";

      $this->varName = $varName;
      $this->varAtt = $varAtt;
     
  }

  /**
   * WIRE INIT
   */
  public function loadWarehouseShowInfor()
  {
      $this->readyToLoad = true;
  }

  /**
   *  Proccess to get Information | Case: Active Cache
   */
  public function getInfor()
  {
    
    \Log::info($this->log.'getInfor');

    $warehouseSession = session($this->varName);
    $warehouseAtt = $this->varAtt;

    //Validation | Bug Cache
    /*
    Cuando se activaba el cache, a pesar de que por el log se observa que la variable de sesion existe
    en este componente a veces aparecia vacia
    */
    if(!is_null($warehouseSession))
      return $warehouseSession->{$warehouseAtt};
    else
      return "Cargando";

  }


  //|--------------------------------------------------------------------------
  //| Render
  //|--------------------------------------------------------------------------
  /**
   * @return mixed
   */
  public function render()
  { 

    return view($this->view,[
      'infor' => $this->readyToLoad ? $this->getInfor() : "Cargando..."
    ]);

  }

}
