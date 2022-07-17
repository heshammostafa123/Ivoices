<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
            // not required if form has filed name invoice_id
            'invoice_number' =>  'required_without:invoice_id|unique:invoices,id,'.$this->invoice_id,
            'invoice_Date'=>    'required',
            'Due_date'=>    'required',
            'product'=>    'required',
            'Section'=>    'required',
            'Amount_collection'=>    'required',
            'Amount_Commission'=>    'required',
            'Value_VAT'=>    'required',
            'Rate_VAT'=>    'required',
            'Total'=>    'required',
            'pic'=> 'mimes:jpg,jpeg,png,pdf',
        ];
    }
    public function messages()
    {
        return[
            'required'  =>'هذا الحقل مطلوب',
            'invoice_number.required_without'  => 'رقم الفاتوره مطلوب',
        ];
    }
}
