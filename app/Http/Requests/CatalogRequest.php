<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogRequest extends FormRequest
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
        $rules = [
            'cat_name'    => 'required|string|unique:catalogs,cat_name',
            'cat_keyword' => 'nullable|unique:catalogs,cat_keyword|max:4',
            'parent_id'   => 'nullable|integer|exists:catalogs,id'
        ];
        if (in_array($this->method(), ['PUT'])) {
            $rules['cat_name'] = [
                'nullable',
                'string',
                'unique:catalogs,cat_name,' . $this->catalog->id
            ];
            $rules['cat_keyword'] = [
                'nullable',
                'string',
                'max:4',
                'unique:catalogs,cat_keyword,' . $this->catalog->id
            ];
        }

        return $rules;
    }
}
