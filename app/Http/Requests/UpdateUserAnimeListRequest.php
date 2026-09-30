<?php

namespace App\Http\Requests;

use App\Enums\ListStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserAnimeListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // want・watched以外の値はエラーにする
            'status' => ['required', Rule::enum(ListStatus::class)],
        ];
    }
}
