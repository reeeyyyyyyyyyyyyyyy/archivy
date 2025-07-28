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

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih',
            'category_id.exists' => 'Kategori yang dipilih tidak valid',
            'code.required' => 'Kode klasifikasi wajib diisi',
            'code.string' => 'Kode klasifikasi harus berupa teks',
            'code.max' => 'Kode klasifikasi maksimal 50 karakter',
            'code.unique' => 'Kode klasifikasi sudah ada, silakan gunakan kode lain',
            'nama_klasifikasi.required' => 'Nama klasifikasi wajib diisi',
            'nama_klasifikasi.string' => 'Nama klasifikasi harus berupa teks',
            'nama_klasifikasi.max' => 'Nama klasifikasi maksimal 255 karakter',
            'retention_aktif.required' => 'Retensi aktif wajib diisi',
            'retention_aktif.integer' => 'Retensi aktif harus berupa angka',
            'retention_aktif.min' => 'Retensi aktif minimal 0 tahun',
            'retention_inaktif.required' => 'Retensi inaktif wajib diisi',
            'retention_inaktif.integer' => 'Retensi inaktif harus berupa angka',
            'retention_inaktif.min' => 'Retensi inaktif minimal 0 tahun',
            'nasib_akhir.required' => 'Nasib akhir wajib dipilih',
            'nasib_akhir.in' => 'Nasib akhir harus Musnah atau Permanen',
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
            'category_id' => 'kategori',
            'code' => 'kode klasifikasi',
            'nama_klasifikasi' => 'nama klasifikasi',
            'retention_aktif' => 'retensi aktif',
            'retention_inaktif' => 'retensi inaktif',
            'nasib_akhir' => 'nasib akhir',
        ];
    }
}
