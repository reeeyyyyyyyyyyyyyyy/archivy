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
            // Manual input fields for hybrid cases
            'is_manual_input' => ['boolean'],
            'manual_retention_aktif' => ['nullable', 'integer', 'min:0'],
            'manual_retention_inaktif' => ['nullable', 'integer', 'min:0'],
            'manual_nasib_akhir' => ['nullable', 'string', 'in:Musnah,Permanen,Dinilai Kembali'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
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
            'skkad.required' => 'SKKAD wajib dipilih',
            'skkad.in' => 'SKKAD harus salah satu dari: SANGAT RAHASIA, TERBATAS, RAHASIA, BIASA/TERBUKA',
            'jumlah_berkas.required' => 'Jumlah berkas wajib diisi',
            'jumlah_berkas.integer' => 'Jumlah berkas harus berupa angka',
            'jumlah_berkas.min' => 'Jumlah berkas minimal 1',
            'ket.string' => 'Keterangan harus berupa teks',
            // Manual input validation messages
            'manual_retention_aktif.integer' => 'Retensi aktif manual harus berupa angka',
            'manual_retention_aktif.min' => 'Retensi aktif manual minimal 0',
            'manual_retention_inaktif.integer' => 'Retensi inaktif manual harus berupa angka',
            'manual_retention_inaktif.min' => 'Retensi inaktif manual minimal 0',
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
            'skkad' => 'SKKAD',
            'jumlah_berkas' => 'jumlah berkas',
            'ket' => 'keterangan',
            'is_manual_input' => 'input manual',
            'manual_retention_aktif' => 'retensi aktif manual',
            'manual_retention_inaktif' => 'retensi inaktif manual',
            'manual_nasib_akhir' => 'nasib akhir manual',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $classificationId = $this->input('classification_id');

            if ($classificationId) {
                $classification = \App\Models\Classification::with('category')->find($classificationId);

                if ($classification) {
                    // Check if any field requires manual input
                    $requiresManualAktif = $classification->retention_aktif === 0;
                    $requiresManualInaktif = $classification->retention_inaktif === 0;
                    $requiresManualNasib = $classification->nasib_akhir === 'Manual';

                    // LAINNYA category - all fields manual
                    if ($classification->category && $classification->category->nama_kategori === 'LAINNYA') {
                        $requiresManualAktif = true;
                        $requiresManualInaktif = true;
                        $requiresManualNasib = true;
                    }

                    // Validate required manual fields
                    if ($requiresManualAktif && $this->input('manual_retention_aktif') === null) {
                        $validator->errors()->add('manual_retention_aktif', 'Retensi aktif manual wajib diisi untuk klasifikasi ini.');
                    }

                    if ($requiresManualInaktif && $this->input('manual_retention_inaktif') === null) {
                        $validator->errors()->add('manual_retention_inaktif', 'Retensi inaktif manual wajib diisi untuk klasifikasi ini.');
                    }

                    if ($requiresManualNasib && empty($this->input('manual_nasib_akhir'))) {
                        $validator->errors()->add('manual_nasib_akhir', 'Nasib akhir manual wajib dipilih untuk klasifikasi ini.');
                    }
                }
            }
        });
    }
}
