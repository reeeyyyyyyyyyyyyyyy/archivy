<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-excel text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900">
                        Export Data Arsip ke Excel
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-tag mr-1"></i>Status: {{ $statusTitle }} 
                        <span class="mx-2">•</span>
                        <i class="fas fa-user mr-1"></i>{{ auth()->user()->getRoleDisplayName() }}
                        <span class="mx-2">•</span>
                        <i class="fas fa-calendar mr-1"></i>{{ now()->format('d F Y') }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Export Format</div>
                <div class="flex items-center text-green-600 font-semibold">
                    <i class="fas fa-file-excel mr-2"></i>
                    Microsoft Excel (.xlsx)
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Export Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-100 px-8 py-6 border-b border-green-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-download text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Pengaturan Export Data</h3>
                                <p class="text-gray-600 mt-1">Sesuaikan filter dan parameter export sesuai kebutuhan</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-green-600">{{ $statusTitle }}</div>
                            <div class="text-sm text-gray-500">Status Dipilih</div>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-8">
                    <!-- Quick Export Button -->
                    <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-100 rounded-xl border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-bolt text-blue-600 text-3xl mr-4"></i>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900">Export Cepat</h4>
                                    <p class="text-gray-600">Export semua data {{ strtolower($statusTitle) }} tanpa filter tambahan</p>
                                </div>
                            </div>
                            <form action="{{ 
                                auth()->user()->isAdmin() ? route('admin.archives.export.process') : 
                                (auth()->user()->isStaff() ? route('staff.export.process') : route('intern.export.process'))
                            }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors transform hover:scale-105">
                                    <i class="fas fa-download mr-2"></i>
                                    Export Sekarang
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Detailed Export Form -->
                    <div class="border-t border-gray-200 pt-8">
                        <h4 class="text-lg font-bold text-gray-900 mb-6">
                            <i class="fas fa-filter mr-2 text-purple-600"></i>
                            Export dengan Filter Advanced
                        </h4>
                        
                        <form action="{{ 
                            auth()->user()->isAdmin() ? route('admin.archives.export.process') : 
                            (auth()->user()->isStaff() ? route('staff.export.process') : route('intern.export.process'))
                        }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="status" value="{{ $status }}">
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Status Info -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-tag mr-1"></i>Status Arsip
                                    </label>
                                    <div class="p-3 bg-blue-100 rounded-lg border border-blue-200">
                                        <span class="font-semibold text-blue-800">{{ $statusTitle }}</span>
                                    </div>
                                </div>

                                <!-- Created By Filter -->
                                <div>
                                    <label for="created_by" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-user mr-1"></i>Filter Berdasarkan Pembuat
                                    </label>
                                    <select name="created_by" id="created_by" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Semua Data (Admin)</option>
                                        <option value="current_user">Data Saya Saja</option>
                                        @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Mahasiswa: pilih "Data Saya Saja"</p>
                                </div>

                                <!-- Year Range Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-calendar-alt mr-1"></i>Filter Rentang Tahun (Opsional)
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="year_from" class="block text-xs text-gray-500 mb-1">Dari Tahun</label>
                                            <input type="number" 
                                                   name="year_from" 
                                                   id="year_from" 
                                                   min="2000" 
                                                   max="{{ date('Y') + 1 }}" 
                                                   placeholder="2020"
                                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                        </div>
                                        <div>
                                            <label for="year_to" class="block text-xs text-gray-500 mb-1">Sampai Tahun</label>
                                            <input type="number" 
                                                   name="year_to" 
                                                   id="year_to" 
                                                   min="2000" 
                                                   max="{{ date('Y') + 1 }}" 
                                                   placeholder="{{ date('Y') }}"
                                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan kedua field untuk export semua tahun</p>
                                    
                                    <!-- Quick Year Buttons -->
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <button type="button" onclick="setYearRange({{ date('Y') }}, {{ date('Y') }})" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded border">
                                            {{ date('Y') }}
                                        </button>
                                        <button type="button" onclick="setYearRange({{ date('Y') - 1 }}, {{ date('Y') }})" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded border">
                                            {{ date('Y') - 1 }}-{{ date('Y') }}
                                        </button>
                                        <button type="button" onclick="setYearRange({{ date('Y') - 4 }}, {{ date('Y') }})" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded border">
                                            5 Tahun Terakhir
                                        </button>
                                        <button type="button" onclick="clearYearRange()" class="px-2 py-1 text-xs bg-red-100 hover:bg-red-200 text-red-600 rounded border">
                                            Reset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Info -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-yellow-600 mt-1 mr-2"></i>
                                    <div class="text-sm text-yellow-800">
                                        <p class="font-medium mb-2">Format Export Excel:</p>
                                        <ul class="list-disc list-inside space-y-1 text-xs">
                                            <li><strong>Header:</strong> Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu</li>
                                            <li><strong>Kolom Otomatis:</strong> No, Kode Klasifikasi, Indeks, Uraian, Kurun Waktu, Tingkat Perkembangan, Jumlah, Keterangan, Jangka Simpan</li>
                                            <li><strong>Kolom Manual:</strong> Nomor Definitif, Nomor Boks, Rak, Baris, Lokasi Simpan (kosong untuk input manual)</li>
                                            <li><strong>Styling:</strong> Header berwarna biru, tabel dengan border, ready untuk print</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition duration-200">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>
                                
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-200 shadow-lg">
                                    <i class="fas fa-download mr-2"></i>Download Excel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Quick Export Buttons -->
                    {{-- <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach(['all' => 'Semua', 'Aktif' => 'Aktif', 'Inaktif' => 'Inaktif', 'Permanen' => 'Permanen', 'Musnah' => 'Usul Musnah'] as $key => $label)
                            <form action="{{ route('admin.archives.export') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="{{ $key }}">
                                <button type="submit" class="w-full p-3 text-sm font-medium text-center text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 transition duration-200">
                                    <i class="fas fa-file-excel text-green-600 mb-1"></i>
                                    <div>Export {{ $label }}</div>
                                    <div class="text-xs text-gray-500">Semua Tahun</div>
                                </button>
                            </form>
                        @endforeach
                    </div> --}}

                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @endpush

    @push('scripts')
        <script>
            // Set year range for quick buttons
            function setYearRange(from, to) {
                document.getElementById('year_from').value = from;
                document.getElementById('year_to').value = to;
            }
            
            // Clear year range
            function clearYearRange() {
                document.getElementById('year_from').value = '';
                document.getElementById('year_to').value = '';
            }
            
            // Validate year range on form submit
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form');
                const yearFrom = document.getElementById('year_from');
                const yearTo = document.getElementById('year_to');
                
                form.addEventListener('submit', function(e) {
                    const fromVal = parseInt(yearFrom.value);
                    const toVal = parseInt(yearTo.value);
                    
                    // If both filled, validate range
                    if (fromVal && toVal && fromVal > toVal) {
                        e.preventDefault();
                        alert('Tahun "Dari" tidak boleh lebih besar dari tahun "Sampai"');
                        return false;
                    }
                    
                    // If only one filled, alert user
                    if ((fromVal && !toVal) || (!fromVal && toVal)) {
                        if (!confirm('Anda hanya mengisi satu tahun. Lanjutkan export dengan filter tahun tunggal?')) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });
                
                // Auto-fill "to" field when "from" is filled
                yearFrom.addEventListener('change', function() {
                    if (this.value && !yearTo.value) {
                        yearTo.value = this.value;
                    }
                });
            });
        </script>
    @endpush
</x-app-layout> 