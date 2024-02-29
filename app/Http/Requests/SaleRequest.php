<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|integer',
            'date' => 'required|date',
            'category_id' => 'required|array|min:1',
            'category_id.*' => 'required|integer',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|integer',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'discount_percentage' => 'nullable|array|min:1',
            'discount_percentage.*' => 'nullable|numeric',
            'discount_amount' => 'nullable|array|min:1',
            'discount_amount.*' => 'nullable|numeric',
            'flat_discount_percentage' => 'nullable|numeric',
            'flat_discount_amount' => 'nullable|numeric',
            'subtotal_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric',
        ];
    }
}
