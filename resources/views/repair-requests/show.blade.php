@extends('layouts.app')

@section('title', 'Detail Laporan - ' . $repairRequest->request_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('repair-requests.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $repairRequest->request_number }}</h1>
                <p class="text-gray-600 mt-1">Detail Laporan Perbaikan</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            @if($repairRequest->isPending() && auth()->user()->can('edit repair requests'))
                <a href="{{ route('repair-requests.edit', $repairRequest) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            
            <!-- Start Progress Button (for approved status) -->
            @if($repairRequest->isApproved() && auth()->user()->can('approve repair requests'))
                <form action="{{ route('repair-requests.start-progress', $repairRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" onclick="return confirm('Mulai pekerjaan perbaikan?')">
                        <i class="fas fa-play mr-2"></i>Mulai Pekerjaan
                    </button>
                </form>
            @endif
            
            <!-- Complete Button (for approved or in_progress status) -->
            @if(in_array($repairRequest->status, ['approved', 'in_progress']) && auth()->user()->can('complete repair requests'))
                <button onclick="showCompleteModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-check-circle mr-2"></i>Selesaikan
                </button>
            @endif
        </div>
    </div>

    <!-- Status Banner -->
    <div class="bg-white rounded-lg shadow-sm border-l-4 p-4 
        @if($repairRequest->isPending()) border-yellow-500
        @elseif($repairRequest->isApproved()) border-blue-500
        @elseif($repairRequest->isInProgress()) border-purple-500
        @elseif($repairRequest->isCompleted()) border-green-500
        @else border-red-500 @endif">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $repairRequest->getStatusBadgeClass() }}">
                    {{ ucfirst(str_replace('_', ' ', $repairRequest->status)) }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $repairRequest->getPriorityBadgeClass() }}">
                    Priority: {{ ucfirst($repairRequest->priority) }}
                </span>
            </div>
            <div class="text-sm text-gray-500">
                Dilaporkan {{ $repairRequest->created_at->diffForHumans() }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Facility Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Informasi Sarana/Prasarana
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Departemen</label>
                            <p class="text-gray-900 mt-1">{{ $repairRequest->department }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Lokasi</label>
                            <p class="text-gray-900 mt-1">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                {{ $repairRequest->location }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Jenis</label>
                            <p class="text-gray-900 mt-1">{{ $repairRequest->facility_type }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nama</label>
                            <p class="text-gray-900 mt-1 font-semibold">{{ $repairRequest->facility_name }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <label class="text-sm font-medium text-gray-500">Deskripsi Kerusakan</label>
                        <p class="text-gray-900 mt-2 whitespace-pre-line">{{ $repairRequest->description }}</p>
                    </div>

                    @if($repairRequest->notes)
                        <div class="border-t border-gray-200 pt-4">
                            <label class="text-sm font-medium text-gray-500">Catatan Tambahan</label>
                            <p class="text-gray-900 mt-2 whitespace-pre-line">{{ $repairRequest->notes }}</p>
                        </div>
                    @endif

                    @if($repairRequest->images && count($repairRequest->images) > 0)
                        <div class="border-t border-gray-200 pt-4">
                            <label class="text-sm font-medium text-gray-500 mb-3 block">Foto Kerusakan</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($repairRequest->images as $image)
                                    <img src="{{ Storage::url($image) }}" alt="Foto Kerusakan" class="rounded-lg border border-gray-200 w-full h-32 object-cover cursor-pointer hover:opacity-75 transition" onclick="showImage('{{ Storage::url($image) }}')">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval Section (for pending requests and utility admin) -->
            @if($repairRequest->isPending() && auth()->user()->can('approve repair requests'))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-clipboard-check text-blue-600 mr-2"></i>
                            Approval & Penugasan
                        </h3>
                    </div>
                    <form action="{{ route('repair-requests.approve', $repairRequest) }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        
                        <!-- Schedule -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jadwal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="scheduled_start" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jadwal Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="scheduled_end" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi Jadwal
                            </label>
                            <textarea name="schedule_description" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Detail pekerjaan yang akan dilakukan..."></textarea>
                        </div>

                        <!-- Technician Assignment -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Teknisi <span class="text-red-500">*</span>
                            </label>
                            <div id="technician-list" class="space-y-2 mb-2">
                                <!-- Technicians will be loaded here -->
                            </div>
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pilih teknisi yang tersedia (status: Free)
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan untuk Teknisi
                            </label>
                            <textarea name="assignment_notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Instruksi khusus atau informasi tambahan untuk teknisi..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Approval
                            </label>
                            <textarea name="approval_notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Catatan approval..."></textarea>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <button type="button" onclick="showRejectModal()" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                <i class="fas fa-times mr-2"></i>Tolak
                            </button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                <i class="fas fa-check mr-2"></i>Approve & Tugaskan
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Schedule Information (if approved) -->
            @if($repairRequest->schedule)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                            Jadwal Perbaikan
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Jadwal Mulai</label>
                                <p class="text-gray-900 mt-1">
                                    {{ $repairRequest->schedule->scheduled_start->format('d M Y, H:i') }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Jadwal Selesai</label>
                                <p class="text-gray-900 mt-1">
                                    {{ $repairRequest->schedule->scheduled_end->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                        @if($repairRequest->schedule->description)
                            <div class="border-t border-gray-200 pt-4">
                                <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                                <p class="text-gray-900 mt-2">{{ $repairRequest->schedule->description }}</p>
                            </div>
                        @endif
                        <div class="border-t border-gray-200 pt-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $repairRequest->schedule->getStatusBadgeClass() }}">
                                Status: {{ ucfirst(str_replace('_', ' ', $repairRequest->schedule->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Assigned Technicians -->
            @if($repairRequest->assignments->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-users text-green-600 mr-2"></i>
                            Teknisi yang Ditugaskan
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($repairRequest->assignments as $assignment)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $assignment->technician->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $assignment->technician->email }}</p>
                                            @if($assignment->notes)
                                                <p class="text-xs text-gray-600 mt-1">
                                                    <i class="fas fa-sticky-note mr-1"></i>{{ $assignment->notes }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->getStatusBadgeClass() }}">
                                        {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                    </span>
                                </div>
                                @if($assignment->work_notes)
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <label class="text-xs font-medium text-gray-500">Catatan Pekerjaan:</label>
                                        <p class="text-sm text-gray-900 mt-1">{{ $assignment->work_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Reporter Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Pelapor</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $repairRequest->reporter->name }}</p>
                            <p class="text-sm text-gray-500">{{ $repairRequest->reporter->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approver Info (if approved/rejected) -->
            @if($repairRequest->approver)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Approver</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-check text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $repairRequest->approver->name }}</p>
                                <p class="text-sm text-gray-500">{{ $repairRequest->approved_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @if($repairRequest->approval_notes)
                            <div class="border-t border-gray-200 pt-4">
                                <label class="text-sm font-medium text-gray-500">Catatan Approval</label>
                                <p class="text-sm text-gray-900 mt-2">{{ $repairRequest->approval_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Completion Info (if completed) -->
            @if($repairRequest->isCompleted())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            Status Penyelesaian
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-flag-checkered text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Perbaikan Selesai</p>
                                <p class="text-sm text-gray-500">{{ $repairRequest->completed_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @if($repairRequest->completion_notes)
                            <div class="border-t border-gray-200 pt-4">
                                <label class="text-sm font-medium text-gray-500">Catatan Penyelesaian</label>
                                <p class="text-sm text-gray-900 mt-2">{{ $repairRequest->completion_notes }}</p>
                            </div>
                        @endif
                        <div class="border-t border-gray-200 pt-4">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <p class="text-sm text-green-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Semua teknisi yang ditugaskan telah menyelesaikan pekerjaan dan kembali tersedia untuk tugas baru.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Laporan Dibuat</p>
                                <p class="text-xs text-gray-500">{{ $repairRequest->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @if($repairRequest->approved_at)
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $repairRequest->isRejected() ? 'Ditolak' : 'Di-approve' }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $repairRequest->approved_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        @if($repairRequest->completed_at)
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Selesai Dikerjakan</p>
                                    <p class="text-xs text-gray-500">{{ $repairRequest->completed_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Tolak Laporan</h3>
        </div>
        <form action="{{ route('repair-requests.reject', $repairRequest) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="approval_notes" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                          placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                    Tolak Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Complete Modal -->
<div id="completeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                Selesaikan Laporan Perbaikan
            </h3>
        </div>
        <form action="{{ route('repair-requests.complete', $repairRequest) }}" method="POST">
            @csrf
            <div class="p-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Dengan menyelesaikan laporan ini, semua teknisi yang ditugaskan akan kembali tersedia untuk tugas baru.
                    </p>
                </div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Penyelesaian (Opsional)
                </label>
                <textarea name="completion_notes" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                          placeholder="Tambahkan catatan penyelesaian jika diperlukan..."></textarea>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                <button type="button" onclick="hideCompleteModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-check-circle mr-2"></i>
                    Selesaikan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Load available technicians
document.addEventListener('DOMContentLoaded', function() {
    @if($repairRequest->isPending() && auth()->user()->can('approve repair requests'))
        loadAvailableTechnicians();
    @endif
});

function loadAvailableTechnicians() {
    fetch('{{ route("api.available-technicians") }}')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('technician-list');
            if (data.length === 0) {
                container.innerHTML = '<p class="text-sm text-red-600">Tidak ada teknisi yang tersedia saat ini.</p>';
                return;
            }
            
            container.innerHTML = data.map(tech => `
                <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="technician_ids[]" value="${tech.id}" class="mr-3 rounded text-blue-600">
                    <div class="flex items-center flex-1">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">${tech.name}</p>
                            <p class="text-xs text-gray-500">${tech.email}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>Available
                    </span>
                </label>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading technicians:', error);
            document.getElementById('technician-list').innerHTML = '<p class="text-sm text-red-600">Gagal memuat daftar teknisi.</p>';
        });
}

function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function showCompleteModal() {
    document.getElementById('completeModal').classList.remove('hidden');
}

function hideCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
}

function showImage(url) {
    Swal.fire({
        imageUrl: url,
        imageAlt: 'Foto Kerusakan',
        showConfirmButton: false,
        showCloseButton: true,
        width: '80%'
    });
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const rejectModal = document.getElementById('rejectModal');
    const completeModal = document.getElementById('completeModal');
    
    if (event.target === rejectModal) {
        hideRejectModal();
    }
    if (event.target === completeModal) {
        hideCompleteModal();
    }
});
</script>
@endsection
