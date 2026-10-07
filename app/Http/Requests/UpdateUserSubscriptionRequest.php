<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 自分の契約かどうかはルートの can で確認済みなので許可する
        return true;
    }

    public function rules(): array
    {
        return [
            // 1円以上の整数。DB の INTEGER に収まる上限も設ける
            'price' => ['bail', 'required', 'integer', 'min:1', 'max:2147483647'],
        ];
    }

    public function attributes(): array
    {
        return [
            'price' => '月額料金',
        ];
    }

    public function messages(): array
    {
        return [
            'price.required' => ':attributeを入力してください',
            'price.integer' => ':attributeは整数で入力してください',
            'price.min' => ':attributeは1円以上で入力してください',
            'price.max' => ':attributeは2147483647円以下で入力してください',
        ];
    }
}
