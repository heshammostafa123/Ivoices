<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$this->id,
            'password' => 'required_without:id|same:confirm-password',
            'roles_name' => 'required_without:id',
            
        ];
    }
    public function messages()
    {
        return[
                'required'  =>'اسم المستخدم مطلوب',
                'unique'    =>'هذا البريد موجود',
                'same'    =>'كلمة السر غير متطابقه',
                'password.required_without'=>'كلمة السر مطلوبه',
                'roles_name.required_without'=>'يجب اختيار صلاحيه وحده علي الاقل'
        ];
    }
}
