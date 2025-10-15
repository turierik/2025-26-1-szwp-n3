<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreOrUpdateRequest extends FormRequest
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
            'title' => 'required|string',
            'content' => 'required|string|min:10',
            'author_id' => 'required|integer|exists:users,id',
            'categories' => 'array',
            'categories.*' => 'integer|distinct|exists:categories,id'
        ];
    }

    public function messages(){
        return [
            'content.min' => 'A tartalom legalább 10 karakter kell legyen!'
        ];
    }
}
