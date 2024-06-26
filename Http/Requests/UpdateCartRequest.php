<?php

namespace Modules\Icommerce\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class UpdateCartRequest extends BaseFormRequest
{
  public function rules()
  {
    return [
      'total' => 'numeric',
      'options' => 'max:200'
    ];
  }
  
  public function translationRules()
  {
    return [];
  }
  
  public function authorize()
  {
    return true;
  }
  
  public function messages()
  {
    return [];
  }
  
  public function translationMessages()
  {
    return [];
  }

    public function getValidator(){
        return $this->getValidatorInstance();
    }
}
