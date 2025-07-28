<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArchiveRequest extends FormRequest
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
            'index_number' => ['required', 'string', 'max:50', Rule::unique('archives', 'index_number')],
            'description' => ['required', 'string'],
            'kurun_waktu_start' => ['required', 'date'],
            'tingkat_perkembangan' => ['required', 'string', 'in:Asli,Salinan,Tembusan'],
            'jumlah_berkas' => ['required', 'integer', 'min:1'],
            'ket' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'classification_id.required' => 'Klasifikasi wajib dipilih',
            'classification_id.exists' => 'Klasifikasi yang dipilih tidak valid',
            'category_id.required' => 'Kategori wajib dipilih',
            'category_id.exists' => 'Kategori yang dipilih tidak valid',
            'index_number.required' => 'Nomor arsip wajib diisi',
            'index_number.string' => 'Nomor arsip harus berupa teks',
            'index_number.max' => 'Nomor arsip maksimal 50 karakter',
            'index_number.unique' => 'Nomor arsip sudah ada, silakan gunakan nomor lain',
            'description.required' => 'Uraian arsip wajib diisi',
            'description.string' => 'Uraian arsip harus berupa teks',
            'kurun_waktu_start.required' => 'Tanggal arsip wajib diisi',
            'kurun_waktu_start.date' => 'Format tanggal arsip tidak valid',
            'tingkat_perkembangan.required' => 'Tingkat perkembangan wajib dipilih',
            'tingkat_perkembangan.in' => 'Tingkat perkembangan harus Asli, Salinan, atau Tembusan',
            'jumlah_berkas.required' => 'Jumlah berkas wajib diisi',
            'jumlah_berkas.integer' => 'Jumlah berkas harus berupa angka',
            'jumlah_berkas.min' => 'Jumlah berkas minimal 1',
            'ket.string' => 'Keterangan harus berupa teks',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'classification_id' => 'klasifikasi',
            'category_id' => 'kategori',
            'index_number' => 'nomor arsip',
            'description' => 'uraian arsip',
            'kurun_waktu_start' => 'tanggal arsip',
            'tingkat_perkembangan' => 'tingkat perkembangan',
            'jumlah_berkas' => 'jumlah berkas',
            'ket' => 'keterangan',
        ];
    }
}
