<?php

namespace Modules\Icommerce\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ProductTransformer extends Resource
{
    public function toArray($request)
    {

        /*evalua segun si el pruducto es nuevo o no (15 días)*/
        $date_available = date_create($this->date_available);
        $now = date_create(date("Y/m/d"));
        $new_product = date_diff($date_available, $now )>15 ? true:false ; //diferencia de dias

        /*valida la imagen del producto*/
        if (isset($this->options->mainimage) && !empty($this->options->mainimage)) {
            $image = url($this->options->mainimage);
        } else {
            $image = url('modules/icommerce/img/product/default.jpg');
        }

        /*valida la imagen del producto*/
        if (isset($this->options->mainfile) && !empty($this->options->mainfile)) {
            $pdf = url($this->options->mainfile);
        } else {
            $pdf = false;
        }

        /*obtiene el descuento que este activo por producto*/
        $price_discount = $this->product_discounts()->where('date_start' ,'<=', date('Y-m-d'))->where('date_end' ,'>=', date('Y-m-d'))->first()->price ?? null;

        if ($price_discount) {
            $discount = '-' . intVal((($this->price - $price_discount) / $this->price) * 100) . '%';
        } else {
            $discount = false;
        }

        /*datos*/

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => $this->url,
            'description' => $this->description,
            'summary' => $this->summary,
            'price' => formatMoney($this->price),
            'unformatted_price' => $this->price,
            'unformatted_price_discount' => $price_discount,
            'price_discount' => formatMoney($price_discount),
            'discount' => $discount,
            'new' => $new_product,
            'rating' => $this->rating,
            'mainimage' => $image,
            'product_discounts' => empty($this->product_discounts) ? 0 : $this->product_discounts,
            'weight' => $this->weight,
            'quantity' => $this->quantity,
            'gallery' => count($this->gallery) >= 1 ? $this->gallery : false,
            'sku' => $this->sku,
            'pdf' => $pdf,
            'quantity_cart' => 0,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'freeshipping' => $this->freeshipping,
            'category' => new CategoryTransformer($this->category),
        ];
    }
}