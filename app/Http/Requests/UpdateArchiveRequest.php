<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArchiveRequest extends FormRequest
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
            'classification_id' => ['required', 'exists:classifications,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'uraian' => ['required', 'string'],
            'kurun_waktu_start' => ['required', 'date'],
            'tingkat_perkembangan' => ['required', 'string', 'in:Asli,Salinan,Tembusan'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'ket' => ['nullable', 'string'],
        ];
    }
}
