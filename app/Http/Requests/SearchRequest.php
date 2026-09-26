<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 空でも通す。空のときはコントローラーでトップへ戻す
            'q' => ['nullable', 'string', 'max:255'],
        ];
    }
}
