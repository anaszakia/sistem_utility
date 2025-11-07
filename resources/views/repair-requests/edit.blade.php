@extends('layouts.app')

@section('title', 'Edit Laporan - ' . $repairRequest->request_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('repair-requests.show', $repairRequest) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Edit Laporan Kerusakan</h1>
            <p class="text-gray-600 mt-1">{{ $repairRequest->request_number }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('repair-requests.update', $repairRequest) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        Departemen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="department" 
                           name="department" 
                           value="{{ old('department', $repairRequest->department) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                           value="{{ old('location', $repairRequest->location) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="">Pilih Jenis</option>
                        <option value="AC" {{ old('facility_type', $repairRequest->facility_type) == 'AC' ? 'selected' : '' }}>AC</option>
                        <option value="Listrik" {{ old('facility_type', $repairRequest->facility_type) == 'Listrik' ? 'selected' : '' }}>Listrik</option>
                        <option value="Mesin" {{ old('facility_type', $repairRequest->facility_type) == 'Mesin' ? 'selected' : '' }}>Mesin</option>
                        <option value="Komputer" {{ old('facility_type', $repairRequest->facility_type) == 'Komputer' ? 'selected' : '' }}>Komputer</option>
                        <option value="Plumbing" {{ old('facility_type', $repairRequest->facility_type) == 'Plumbing' ? 'selected' : '' }}>Plumbing</option>
                        <option value="Furniture" {{ old('facility_type', $repairRequest->facility_type) == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                        <option value="Lainnya" {{ old('facility_type', $repairRequest->facility_type) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                           value="{{ old('facility_name', $repairRequest->facility_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('priority', $repairRequest->priority) == 'low' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                        <input type="radio" name="priority" value="low" class="mr-2" {{ old('priority', $repairRequest->priority) == 'low' ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-medium text-gray-900">Rendah</span>
                            <span class="text-xs text-gray-500">Tidak mendesak</span>
                        </div>
                    </label>
                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('priority', $repairRequest->priority) == 'medium' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300' }}">
                        <input type="radio" name="priority" value="medium" class="mr-2" {{ old('priority', $repairRequest->priority) == 'medium' ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-medium text-gray-900">Sedang</span>
                            <span class="text-xs text-gray-500">Normal</span>
                        </div>
                    </label>
                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('priority', $repairRequest->priority) == 'high' ? 'border-orange-500 bg-orange-50' : 'border-gray-300' }}">
                        <input type="radio" name="priority" value="high" class="mr-2" {{ old('priority', $repairRequest->priority) == 'high' ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-medium text-gray-900">Tinggi</span>
                            <span class="text-xs text-gray-500">Penting</span>
                        </div>
                    </label>
                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('priority', $repairRequest->priority) == 'urgent' ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                        <input type="radio" name="priority" value="urgent" class="mr-2" {{ old('priority', $repairRequest->priority) == 'urgent' ? 'checked' : '' }}>
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
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          required>{{ old('description', $repairRequest->description) }}</textarea>
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
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $repairRequest->notes) }}</textarea>
            </div>

            <!-- Existing Images -->
            @if($repairRequest->images && count($repairRequest->images) > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Foto yang Sudah Ada
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($repairRequest->images as $image)
                            <img src="{{ Storage::url($image) }}" alt="Foto" class="rounded-lg border border-gray-200 w-full h-24 object-cover">
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- New Images -->
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                    Tambah Foto Baru (Opsional)
                </label>
                <input type="file" 
                       id="images" 
                       name="images[]" 
                       multiple
                       accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Upload foto tambahan jika diperlukan (max 2MB per foto)</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('repair-requests.show', $repairRequest) }}" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Update Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
