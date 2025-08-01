<?php

namespace Database\Factories;

use App\Models\Classification;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Archive>
 */
class ArchiveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random classification with its category
        $classification = Classification::with('category')->inRandomOrder()->first();

        // If no classification exists, create one
        if (!$classification) {
            $category = Category::factory()->create();
            $classification = Classification::factory()->create(['category_id' => $category->id]);
        }

        // Generate random date between 2000 and 2024
        $kurunWaktuStart = $this->faker->dateTimeBetween('2000-01-01', '2024-12-31');

        // Calculate transition dates based on classification retention
        $transitionActiveDue = Carbon::parse($kurunWaktuStart)->addYears($classification->retention_aktif);
        $transitionInactiveDue = $transitionActiveDue->copy()->addYears($classification->retention_inaktif);

        // Generate realistic archive descriptions
        $descriptions = [
            'Laporan bulanan kegiatan administrasi dan pelayanan publik',
            'Dokumen pengadaan barang dan jasa untuk kebutuhan kantor',
            'Arsip rapat koordinasi pimpinan dan staf',
            'Laporan keuangan dan anggaran tahunan',
            'Dokumen pengangkatan dan kenaikan pangkat pegawai',
            'Arsip perencanaan pembangunan dan pengembangan',
            'Laporan audit internal dan pengawasan',
            'Dokumen kerjasama dan hubungan luar negeri',
            'Arsip infrastruktur dan sarana prasarana',
            'Laporan bantuan sosial dan kesejahteraan masyarakat',
            'Dokumen pengelolaan lingkungan hidup',
            'Arsip pelayanan perizinan dan sertifikasi',
            'Laporan evaluasi kinerja dan penilaian prestasi',
            'Dokumen perbendaharaan dan keuangan daerah',
            'Arsip konservasi sumber daya alam',
            'Laporan inspeksi dan pengendalian pencemaran',
            'Dokumen rencana pembangunan jangka panjang',
            'Arsip daftar urut kepangkatan pegawai',
            'Laporan kunjungan kerja dan studi banding',
            'Dokumen pemeliharaan infrastruktur publik'
        ];

        // Get random user (admin, staff, or intern)
        $user = User::inRandomOrder()->first();

        // If no user exists, create default users
        if (!$user) {
            $this->createDefaultUsers();
            $user = User::inRandomOrder()->first();
        }

        // Generate index number
        $indexNumber = $this->generateIndexNumber($classification, $kurunWaktuStart);

        return [
            'category_id' => $classification->category_id,
            'classification_id' => $classification->id,
            'index_number' => $indexNumber,
            'description' => $this->faker->randomElement($descriptions),
            'kurun_waktu_start' => $kurunWaktuStart,
            'tingkat_perkembangan' => $this->faker->randomElement(['Asli', 'Salinan', 'Tembusan']),
            'jumlah_berkas' => $this->faker->numberBetween(1, 50),
            'ket' => $this->faker->optional(0.3)->sentence(),
            'retention_aktif' => $classification->retention_aktif,
            'retention_inaktif' => $classification->retention_inaktif,
            'transition_active_due' => $transitionActiveDue,
            'transition_inactive_due' => $transitionInactiveDue,
            'status' => 'Aktif', // Will be calculated based on dates
            'manual_status_override' => false,
            'manual_override_at' => null,
            'manual_override_by' => null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];
    }

    /**
     * Generate realistic index number
     */
    private function generateIndexNumber($classification, $date): string
    {
        $year = Carbon::parse($date)->format('Y');
        $month = Carbon::parse($date)->format('m');
        $randomNumber = str_pad($this->faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);

        return "{$classification->code}/{$year}/{$month}/{$randomNumber}";
    }

    /**
     * Create default users if they don't exist
     */
    private function createDefaultUsers(): void
    {
        // Create admin user
        if (!User::where('email', 'admin@archivy.test')->exists()) {
            $admin = User::factory()->create([
                'name' => 'Administrator',
                'email' => 'admin@archivy.test',
                'password' => bcrypt('password'),
            ]);
            $admin->assignRole('admin');
        }

        // Create staff user
        if (!User::where('email', 'staff@archivy.test')->exists()) {
            $staff = User::factory()->create([
                'name' => 'Staff TU',
                'email' => 'staff@archivy.test',
                'password' => bcrypt('password'),
            ]);
            $staff->assignRole('staff');
        }

        // Create intern user
        if (!User::where('email', 'intern@archivy.test')->exists()) {
            $intern = User::factory()->create([
                'name' => 'Mahasiswa Magang',
                'email' => 'intern@archivy.test',
                'password' => bcrypt('password'),
            ]);
            $intern->assignRole('intern');
        }
    }

    /**
     * Archive with specific status
     */
    public function aktif()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Aktif',
                'kurun_waktu_start' => $this->faker->dateTimeBetween('2020-01-01', '2024-12-31'),
            ];
        });
    }

    public function inaktif()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Inaktif',
                'kurun_waktu_start' => $this->faker->dateTimeBetween('2015-01-01', '2019-12-31'),
            ];
        });
    }

    public function permanen()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Permanen',
                'kurun_waktu_start' => $this->faker->dateTimeBetween('2010-01-01', '2014-12-31'),
            ];
        });
    }

    public function musnah()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Musnah',
                'kurun_waktu_start' => $this->faker->dateTimeBetween('2000-01-01', '2009-12-31'),
            ];
        });
    }

    /**
     * Archive created by specific user
     */
    public function createdByAdmin()
    {
        return $this->state(function (array $attributes) {
            $admin = User::role('admin')->first();
            return [
                'created_by' => $admin ? $admin->id : 1,
                'updated_by' => $admin ? $admin->id : 1,
            ];
        });
    }

    public function createdByStaff()
    {
        return $this->state(function (array $attributes) {
            $staff = User::role('staff')->first();
            return [
                'created_by' => $staff ? $staff->id : 1,
                'updated_by' => $staff ? $staff->id : 1,
            ];
        });
    }

    public function createdByIntern()
    {
        return $this->state(function (array $attributes) {
            $intern = User::role('intern')->first();
            return [
                'created_by' => $intern ? $intern->id : 1,
                'updated_by' => $intern ? $intern->id : 1,
            ];
        });
    }
}
