<div class="tab-pane fade {{$tabSelected == $shippingMethods['pickup'] ? 'show active' : ''}}" id="pointModalWarehouseLocator" role="tabpanel" aria-labelledby="pointTabModalWarehouseLocator">

    @if(!$chooseOtherWarehouse)

        <div id="warehouseSelected">

        
            <div class="list-address" wire:init="loadWarehouseShowInfor">
                <div class="item-address">
                    <div class="form-check d-flex align-items-center position-static">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2" checked>
                        <label class="form-check-label active" for="exampleRadios2">
                            <p class="mb-0">{{$warehouse->title}} | {{$warehouse->address}}</p>
                        </label>
                        <div class="marked"></div>
                    </div>
                </div>
            </div>

            <!-- End Listado de tiendas marcadas -->
            <div class="form-row justify-content-center mt-4">
                <div class="form-group col-md-6">
                    <button wire:click="$set('chooseOtherWarehouse', true)" type="button" class="btn outline btn-primary btn-block">
                        {{trans('icommerce::warehouses.button.choosed other warehouse')}}
                    </button>
                </div>
                <div class="form-group col-md-6">
                    @include('icommerce::frontend.livewire.warehouse-locator.layouts.tabs.btn-confirm')
                </div>
            </div>
        
        </div>

    @else

        <div id="otherWarehouse">

            <p class="text-small">
                <strong>{{trans('icommerce::warehouses.title.information')}}:</strong>
                {{trans('icommerce::warehouses.messages.select province and city')}}
            </p>

            <div class="form-point">

                <!-- Selects Province and City -->
                @include('icommerce::frontend.livewire.warehouse-locator.layouts.tabs.pickup.selects-location')

                <!-- Warehouse Selected -->
                @include('icommerce::frontend.livewire.warehouse-locator.layouts.tabs.pickup.warehouse-selected')

                <!-- WAREHOUSES MAP -->
                @include('icommerce::frontend.livewire.warehouse-locator.layouts.tabs.pickup.warehouses-map')

            </div>

            
            <div class="form-row justify-content-center mt-4">
                <div class="form-group col-md-4">
                    <button wire:click="$set('chooseOtherWarehouse', false)" type="button" class="btn outline btn-primary btn-block">
                        {{trans('icommerce::warehouses.button.back')}}
                    </button>
                </div>
                <div class="form-group col-md-6">
                    @include('icommerce::frontend.livewire.warehouse-locator.layouts.tabs.btn-confirm')
                </div>
            </div>


        </div>
    @endif
    

</div>