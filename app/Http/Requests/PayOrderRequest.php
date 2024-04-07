<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_name' => 'required|string|max:80', 
            'customer_email' => 'required|email|max:120',
            'customer_mobile' => 'required|digits:10', 
            'product_quantity' => 'required|integer|between:1,10', 
        ];
    }
}
