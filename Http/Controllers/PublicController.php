<?php

namespace Modules\Icommerce\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Icommerce\Entities\Category;
use Modules\Icommerce\Entities\Currency;
use Modules\Icommerce\Repositories\CategoryRepository;
use Modules\Icommerce\Repositories\ManufacturerRepository;
use Modules\Icommerce\Transformers\CartTransformer;
use Modules\Icommerce\Transformers\CategoryTransformer;
use Modules\Icurrency\Repositories\CurrencyRepository;
use Modules\Icommerce\Repositories\PaymentMethodRepository;
use Modules\Icommerce\Repositories\ProductRepository;
use Modules\Icommerce\Repositories\ShippingMethodRepository;
use Modules\Iprofile\Repositories\UserApiRepository;
use Modules\Icommerce\Transformers\PaymentMethodTransformer;
use Modules\Icommerce\Transformers\ShippingMethodTransformer;
use Modules\Icommerce\Transformers\ProductTransformer;
use Route;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

class PublicController extends BaseApiController
{
  protected $auth;
  private $product;
  private $category;
  private $manufacturer;
  private $currency;
  private $payments;
  private $shippings;
  
  public function __construct(
    ProductRepository $product,
    CategoryRepository $category,
    ManufacturerRepository $manufacturer,
    CurrencyRepository $currency,
    PaymentMethodRepository $payments,
    ShippingMethodRepository $shippings
  )
  {
    parent::__construct();
    $this->product = $product;
    $this->category = $category;
    $this->manufacturer = $manufacturer;
    $this->currency = $currency;
    $this->payments = $payments;
    $this->shippings = $shippings;
  }

  // view products by category
  public function index(Request $request)
  {
    $argv = explode("/",$request->path());
    $slug = end($argv);

    $tpl = 'icommerce::frontend.index';
  
    $category = null;
  
    $categoryBreadcrumb = [];
    
    if($slug && $slug != trans('icommerce::routes.store.index.index')){
      
      $category = $this->category->findBySlug($slug);
    
      if(isset($category->id)){
        $categoryBreadcrumb = CategoryTransformer::collection(Category::ancestorsAndSelf($category->id));
       
        $ptpl = "icommerce.category.{$category->parent_id}%.index";
        if ($category->parent_id != 0 && view()->exists($ptpl)) {
          $tpl = $ptpl;
        }
  
        $ctpl = "icommerce.category.{$category->id}.index";
        if (view()->exists($ctpl)) $tpl = $ctpl;
  
        $ctpl = "icommerce.category.{$category->id}%.index";
        if (view()->exists($ctpl)) $tpl = $ctpl;

        $gallery = $this->getGalleryCategory($category);
  
      }else{
        return response()->view('errors.404', [], 404);
      }
      
    }

    //$dataRequest = $request->all();

    return view($tpl, compact('category','categoryBreadcrumb','gallery'));
  }

  // view products by category
  public function indexManufacturer(Request $request)
  {
    $argv = explode("/",$request->path());
    $slug = end($argv);
  
    $tpl = 'icommerce::frontend.index';
    $ttpl = 'icommerce.index';
    
    if (view()->exists($ttpl)) $tpl = $ttpl;
  
    $manufacturer = null;
  
    $categoryBreadcrumb = [];
    
    if($slug && $slug != trans('icommerce::routes.store.index')){
  
      $manufacturer = $this->manufacturer->findBySlug($slug);
    
      if(isset($manufacturer->id)){
        
      
        $ctpl = "icommerce.manufacturer.{$manufacturer->id}.index";
        if (view()->exists($ctpl)) $tpl = $ctpl;
  
        $ctpl = "icommerce.manufacturer.{$manufacturer->id}%.index";
        if (view()->exists($ctpl)) $tpl = $ctpl;
  
      }else{
        return response()->view('errors.404', [], 404);
      }
      
    }

    //$dataRequest = $request->all();

    return view($tpl, compact('manufacturer','categoryBreadcrumb'));
  }

  // view products by category
  public function indexCategoryManufacturer(Request $request, $categorySlug, $manufacturerSlug)
  {
    $argv = explode("/",$request->path());
  
    $tpl = 'icommerce::frontend.index';
    $ttpl = 'icommerce.index';
    
    if (view()->exists($ttpl)) $tpl = $ttpl;
  
    $manufacturer = null;
    $category = null;
  
    $categoryBreadcrumb = [];
    
    if($categorySlug && $manufacturerSlug){
  
      $manufacturer = $this->manufacturer->findBySlug($manufacturerSlug);
      
      $category = $this->category->findBySlug($categorySlug);
  
     
      if(isset($category->id) && isset($manufacturer->id)){
      
        $categoryBreadcrumb = CategoryTransformer::collection(Category::ancestorsAndSelf($category->id));
        
        $ctpl = "icommerce.manufacturer.{$manufacturer->id}.index";
        if (view()->exists($ctpl)) $tpl = $ctpl;
  
        $ctpl = "icommerce.manufacturer.{$manufacturer->id}%.index";
        if (view()->exists($ctpl)) $tpl = $ctpl;
  
      }else{
        return response()->view('errors.404', [], 404);
      }
      
    }

    //$dataRequest = $request->all();

    return view($tpl, compact('category','manufacturer','categoryBreadcrumb'));
  }
  
  /**
   * Show product
   * @param Request $request
   * @return mixed
   */
  public function show(Request $request)
  {
    $argv = explode("/",$request->path());
    $slug = end($argv);
   
    $tpl = 'icommerce::frontend.show';
    $ttpl = 'icommerce.show';
    if (view()->exists($ttpl)) $tpl = $ttpl;
    $params = json_decode(json_encode(
      [
        "include" => explode(",","translations,category,categories,tags,addedBy"),
        "filter" => [
          "field" => "slug"
        ]
      ]
    ));
    
    $product = $this->product->getItem($slug,$params);
    
    if($product){
      $category= $product->category;
      $categoryBreadcrumb = CategoryTransformer::collection(Category::ancestorsAndSelf($category->id));
      
      return view($tpl, compact('product','category','categoryBreadcrumb'));
      
    }else{
      return response()->view('errors.404', [], 404);
    }
    
  }
  
  public function checkout()
  {
    $tpl = 'icommerce::frontend.checkout.index';
    $ttpl = 'icommerce.checkout.index';
    if (view()->exists($ttpl)) $tpl = $ttpl;
  
    $cart = request()->session()->get('cart');
    if(isset($cart->id)) {
      $cart = app('Modules\Icommerce\Repositories\CartRepository')->getItem($cart->id);
    }
    $currency = Currency::where("default_currency",1)->first();
    
    return view($tpl,["cart" => new CartTransformer($cart),"currency" => $currency]);
  }
  
  public function wishlist()
  {
    $tpl = 'icommerce::frontend.wishlist.index';
    $ttpl = 'icommerce.wishlist.index';
    
    if (view()->exists($ttpl)) $tpl = $ttpl;
    return view($tpl);
  }
  
  // view products by category
  public function search(Request $request)
  {
    
    $tpl = 'icommerce::frontend.search';
    $ttpl = 'icommerce.search';
    
    if (view()->exists($ttpl)) $tpl = $ttpl;
    $category=$request->input('category')??null;
    $params=$this->_paramsRequest($request,$category);
    
    $products = $this->product->getItemsBy($params);
    
    $products=ProductTransformer::collection($products);
    
    $paginate=(object)[
      "total" => $products->total(),
      "lastPage" => $products->lastPage(),
      "perPage" => $products->perPage(),
      "currentPage" => $products->currentPage()
    ];
    
    return view($tpl, compact('products','paginate', 'category'));
    
  }
  // view products by category
  public function test(Request $request)
  {
    

    
  }
  
  private function _paramsRequest(&$params)
  {
    //$params->take = $params->take ?? setting("")
    //Return params
    $params = (object)[
      "page" => is_numeric($request->input('page')) ? $request->input('page') : 1,
      "take" => is_numeric($request->input('take')) ? $request->input('take') :
        ($request->input('page') ? 12 : null),
      "include" =>[],
      "filter" => json_decode(json_encode(['categories'=>$category,'manufacturers'=>$manufacturer,'priceRange'=>['from'=>$minPrice,'to'=>$maxPrice],'search'=>$search,'order'=>$order,'status'=>1])),
    ];

    return $params;//Response
  }

  /**
  * Get Images to gallery top Category
  *
  */
  protected function getGalleryCategory($category){

    $gallery = [];

    $typeGallery = setting('icommerce::carouselIndexCategory',null,'carousel-category-active');
    if(isset($category->id)){

      if($category->parent_id!=null && $typeGallery=="carousel-category-parent"){
        $category = Category::whereAncestorOf($category)->whereNull("parent_id")->first();
      }

      $mediaFiles = $category->mediaFiles();
      if(isset($mediaFiles->carouselindeximage)){
        $gallery = $mediaFiles->carouselindeximage;
      } 

    }

    return $gallery;

  }
  
}
