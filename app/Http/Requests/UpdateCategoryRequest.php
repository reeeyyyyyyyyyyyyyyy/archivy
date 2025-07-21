<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
            // 'classification_id' => 'required|exists:classifications,id', // REMOVED as per user request
            'parent_id' => 'nullable|exists:categories,id',
            // 'code' rule was previously removed based on user feedback
            'name' => 'required|string|max:255',
            'retention_active' => 'required|integer|min:0',
            'retention_inactive' => 'required|integer|min:0',
            'nasib_akhir' => 'required|in:Musnah,Permanen,Dinilai Kembali',
        ];
    }
}