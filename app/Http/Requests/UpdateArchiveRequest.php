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
            'index_number' => ['required', 'string', 'max:50', Rule::unique('archives')->ignore($this->archive)],
            'description' => ['required', 'string'],
            'lampiran_surat' => ['nullable', 'string'],
            'kurun_waktu_start' => ['required', 'date'],
            'tingkat_perkembangan' => ['required', 'string'],
            'skkad' => ['required', 'string', 'in:SANGAT RAHASIA,TERBATAS,RAHASIA,BIASA/TERBUKA'],
            'jumlah_berkas' => ['required', 'integer', 'min:1'],
            'ket' => ['nullable', 'string'],
            // Manual input fields for non-JRA categories (LAINNYA only)
            'is_manual_input' => ['boolean'],
            'manual_retention_aktif' => ['nullable', 'required_if:is_manual_input,1', 'integer', 'min:0'],
            'manual_retention_inaktif' => ['nullable', 'required_if:is_manual_input,1', 'integer', 'min:0'],
            'manual_nasib_akhir' => ['nullable', 'required_if:is_manual_input,1', 'string', 'in:Musnah,Permanen,Dinilai Kembali'],
        ];
    }
}
