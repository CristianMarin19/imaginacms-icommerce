<div class="product-layout product-layout-3">
  @php($discount = $product->discount ?? null)
  @include('icommerce::frontend.components.product.meta')
  
 
  
  @if(isset($productListLayout) && $productListLayout=='one')
    <div class="row product-list-layout-one">
      
      <div class="col-12 col-sm-6">
        @include('icommerce::frontend.components.product.ribbon')
        @include('icommerce::frontend.components.product.product-list-item.layouts.product-list-item-layout-3.image')
      </div>
      <div class="col-12 col-sm-6">
        @include('icommerce::frontend.components.product.product-list-item.layouts.product-list-item-layout-3.infor')
      </div>
    </div>
  @else
    @include('icommerce::frontend.components.product.ribbon')
    @include('icommerce::frontend.components.product.product-list-item.layouts.product-list-item-layout-3.image')
    @include('icommerce::frontend.components.product.product-list-item.layouts.product-list-item-layout-3.infor')
  @endif

</div>