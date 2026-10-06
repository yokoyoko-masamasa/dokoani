<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 実在するサービスで、自分がまだ契約していないものだけ通す
            'streaming_service_id' => [
                'bail',
                'required',
                'integer',
                'exists:streaming_services,id',
                Rule::unique('user_subscriptions', 'streaming_service_id')->where('user_id', $this->user()->id),
            ],
            // 1円以上の整数。DB の INTEGER に収まる上限も設ける
            'price' => ['bail', 'required', 'integer', 'min:1', 'max:2147483647'],
        ];
    }

    public function attributes(): array
    {
        return [
            'streaming_service_id' => 'サービス',
            'price' => '月額料金',
        ];
    }

    public function messages(): array
    {
        return [
            'streaming_service_id.required' => ':attributeを選択してください',
            'streaming_service_id.integer' => ':attributeを選択してください',
            'streaming_service_id.exists' => '選択した:attributeは追加できません',
            'streaming_service_id.unique' => 'この:attributeはすでに登録されています',
            'price.required' => ':attributeを入力してください',
            'price.integer' => ':attributeは整数で入力してください',
            'price.min' => ':attributeは1円以上で入力してください',
            'price.max' => ':attributeは2147483647円以下で入力してください',
        ];
    }
}
