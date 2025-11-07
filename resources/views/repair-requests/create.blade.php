@extends('layouts.app')

@section('title', 'Buat Laporan Kerusakan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('repair-requests.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Buat Laporan Kerusakan</h1>
            <p class="text-gray-600 mt-1">Laporkan kerusakan sarana dan prasarana</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('repair-requests.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        Departemen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="department" 
                           name="department" 
                           value="{{ old('department') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('department') border-red-500 @else border-gray-300 @enderror"
                           placeholder="Contoh: IT, HRD, Produksi"
                           required>
                    @error('department')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           value="{{ old('location') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @else border-gray-300 @enderror"
                           placeholder="Contoh: Lantai 2 Ruang Meeting"
                           required>
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Facility Type -->
                <div>
                    <label for="facility_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Sarana/Prasarana <span class="text-red-500">*</span>
                    </label>
                    <select id="facility_type" 
                            name="facility_type" 
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('facility_type') border-red-500 @else border-gray-300 @enderror"
                            required>
                        <option value="">Pilih Jenis</option>
                        <option value="AC" {{ old('facility_type') == 'AC' ? 'selected' : '' }}>AC</option>
                        <option value="Listrik" {{ old('facility_type') == 'Listrik' ? 'selected' : '' }}>Listrik</option>
                        <option value="Mesin" {{ old('facility_type') == 'Mesin' ? 'selected' : '' }}>Mesin</option>
                        <option value="Komputer" {{ old('facility_type') == 'Komputer' ? 'selected' : '' }}>Komputer</option>
                        <option value="Plumbing" {{ old('facility_type') == 'Plumbing' ? 'selected' : '' }}>Plumbing</option>
                        <option value="Furniture" {{ old('facility_type') == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                        <option value="Lainnya" {{ old('facility_type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('facility_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Facility Name -->
                <div>
                    <label for="facility_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Sarana/Prasarana <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="facility_name" 
                           name="facility_name" 
                           value="{{ old('facility_name') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('facility_name') border-red-500 @else border-gray-300 @enderror"
                           placeholder="Contoh: AC Split Panasonic Unit 2"
                           required>
                    @error('facility_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Priority -->
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                    Prioritas <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('priority') == 'low' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                        <input type="radio" name="priority" value="low" class="mr-2" {{ old('priority', 'medium') == 'low' ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-medium text-gray-900">Rendah</span>
                            <span class="text-xs text-gray-500">Tidak mendesak</span>
                        </div>
                    </label>
                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('priority') == 'medium' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300' }}">
                        <input type="radio" name="priority" value="medium" class="mr-2" {{ old('priority', 'medium') == 'medium' ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-medium text-gray-900">Sedang</span>
                            <span class="text-xs text-gray-500">Normal</span>
                        </div>
                    </label>
                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('priority') == 'high' ? 'border-orange-500 bg-orange-50' : 'border-gray-300' }}">
                        <input type="radio" name="priority" value="high" class="mr-2" {{ old('priority') == 'high' ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-medium text-gray-900">Tinggi</span>
                            <span class="text-xs text-gray-500">Penting</span>
                        </div>
                    </label>
                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('priority') == 'urgent' ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                        <input type="radio" name="priority" value="urgent" class="mr-2" {{ old('priority') == 'urgent' ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-medium text-gray-900">Mendesak</span>
                            <span class="text-xs text-gray-500">Sangat penting</span>
                        </div>
                    </label>
                </div>
                @error('priority')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi Kerusakan <span class="text-red-500">*</span>
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="5"
                          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @else border-gray-300 @enderror"
                          placeholder="Jelaskan detail kerusakan yang terjadi..."
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Tambahan (Opsional)
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Tambahkan informasi tambahan jika ada...">{{ old('notes') }}</textarea>
            </div>

            <!-- Images -->
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                    Foto Kerusakan (Opsional)
                </label>
                <input type="file" 
                       id="images" 
                       name="images[]" 
                       multiple
                       accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Upload foto kerusakan (max 2MB per foto, bisa multiple)</p>
                @error('images.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('repair-requests.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
