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
            'lampiran_surat' => ['nullable', 'string'],
            'kurun_waktu_start' => ['required', 'date'],
            'tingkat_perkembangan' => ['required', 'string'],
            'skkd' => ['required', 'string', 'in:SANGAT RAHASIA,TERBATAS,RAHASIA,BIASA/TERBUKA'],
            'jumlah_berkas' => ['required', 'integer', 'min:1'],
            'ket' => ['nullable', 'string'],
            // Manual input fields for non-JRA categories (LAINNYA only)
            'is_manual_input' => ['boolean'],
            'manual_retention_aktif' => ['nullable', 'required_if:is_manual_input,1', 'integer', 'min:0'],
            'manual_retention_inaktif' => ['nullable', 'required_if:is_manual_input,1', 'integer', 'min:0'],
            'manual_nasib_akhir' => ['nullable', 'required_if:is_manual_input,1', 'string', 'in:Musnah,Permanen,Dinilai Kembali'],
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
            'lampiran_surat.string' => 'Lampiran surat harus berupa teks',
            'kurun_waktu_start.required' => 'Tanggal arsip wajib diisi',
            'kurun_waktu_start.date' => 'Format tanggal arsip tidak valid',
            'tingkat_perkembangan.required' => 'Tingkat perkembangan wajib diisi',
            'tingkat_perkembangan.string' => 'Tingkat perkembangan harus berupa teks',
            'skkd.required' => 'SKKD wajib dipilih',
            'skkd.in' => 'SKKD harus salah satu dari: SANGAT RAHASIA, TERBATAS, RAHASIA, BIASA/TERBUKA',
            'jumlah_berkas.required' => 'Jumlah berkas wajib diisi',
            'jumlah_berkas.integer' => 'Jumlah berkas harus berupa angka',
            'jumlah_berkas.min' => 'Jumlah berkas minimal 1',
            'ket.string' => 'Keterangan harus berupa teks',
            // Manual input validation messages (LAINNYA category only)
            'manual_retention_aktif.required_if' => 'Retensi aktif manual wajib diisi untuk kategori LAINNYA',
            'manual_retention_aktif.integer' => 'Retensi aktif manual harus berupa angka',
            'manual_retention_aktif.min' => 'Retensi aktif manual minimal 0',
            'manual_retention_inaktif.required_if' => 'Retensi inaktif manual wajib diisi untuk kategori LAINNYA',
            'manual_retention_inaktif.integer' => 'Retensi inaktif manual harus berupa angka',
            'manual_retention_inaktif.min' => 'Retensi inaktif manual minimal 0',
            'manual_nasib_akhir.required_if' => 'Nasib akhir manual wajib dipilih untuk kategori LAINNYA',
            'manual_nasib_akhir.in' => 'Nasib akhir manual harus salah satu dari: Musnah, Permanen, Dinilai Kembali',
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
            'lampiran_surat' => 'lampiran surat',
            'kurun_waktu_start' => 'tanggal arsip',
            'tingkat_perkembangan' => 'tingkat perkembangan',
            'skkd' => 'skkd',
            'jumlah_berkas' => 'jumlah berkas',
            'ket' => 'keterangan',
            'is_manual_input' => 'input manual',
            'manual_retention_aktif' => 'retensi aktif manual',
            'manual_retention_inaktif' => 'retensi inaktif manual',
            'manual_nasib_akhir' => 'nasib akhir manual',
        ];
    }
}
