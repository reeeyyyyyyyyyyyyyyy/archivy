<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassificationRequest extends FormRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:50', 'unique:classifications,code'],
            'nama_klasifikasi' => ['required', 'string', 'max:255'],
            'retention_aktif' => ['required', 'integer', 'min:0'],
            'retention_inaktif' => ['required', 'integer', 'min:0'],
            'nasib_akhir' => ['required', 'string', 'in:Musnah,Permanen'],
        ];
    }
}
