<a class="nav-link {{$classCart}}" type="button"
     id="dropdownCart" data-toggle="dropdown" aria-haspopup="true"
     aria-expanded="false" role="button" aria-label="cart">
    <div class="cart d-inline-block">
      <span class="quantity">
        @if(!isset($cart->quantity))
          <i class="fa fa-spinner fa-pulse fa-2x fa-fw text-white"></i>
        @else
          {{  $cart->quantity  }}
        @endif
      </span>
      <i class="{{$icon,$iconquote}}"></i>
    </div>
</a>