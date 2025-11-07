@extends('layouts.app')

@section('title', 'Detail Permission')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('permissions.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Detail Permission</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap permission</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            @can('edit permissions')
                <a href="{{ route('permissions.edit', $permission) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Permission
                </a>
            @endcan
            @can('delete permissions')
                <button onclick="deletePermission({{ $permission->id }}, '{{ $permission->name }}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-trash mr-2"></i>
                    Delete
                </button>
            @endcan
        </div>
    </div>

    <!-- Permission Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-key text-purple-600 mr-2"></i>
                Informasi Permission
            </h3>
        </div>
        
        <div class="p-6 space-y-4">
            <!-- Permission Name -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-gray-500">Nama Permission</label>
                </div>
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-key text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $permission->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200"></div>

            <!-- Guard Name -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-gray-500">Guard Name</label>
                </div>
                <div class="md:col-span-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        {{ $permission->guard_name }}
                    </span>
                </div>
            </div>

            <div class="border-t border-gray-200"></div>

            <!-- Created At -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-gray-500">Dibuat Pada</label>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-900">
                        <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                        {{ $permission->created_at->format('d F Y, H:i') }}
                        <span class="text-gray-500 text-sm">({{ $permission->created_at->diffForHumans() }})</span>
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-200"></div>

            <!-- Updated At -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-gray-500">Terakhir Diupdate</label>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-900">
                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                        {{ $permission->updated_at->format('d F Y, H:i') }}
                        <span class="text-gray-500 text-sm">({{ $permission->updated_at->diffForHumans() }})</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles that have this permission -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-user-shield text-blue-600 mr-2"></i>
                Role yang Memiliki Permission Ini
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $permission->roles->count() }}
                </span>
            </h3>
        </div>
        
        <div class="p-6">
            @if($permission->roles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permission->roles as $role)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3
                                        @if($role->name === 'super_admin') bg-red-100
                                        @elseif($role->name === 'admin_utility') bg-blue-100
                                        @elseif($role->name === 'admin_departemen') bg-green-100
                                        @elseif($role->name === 'teknisi_utility') bg-yellow-100
                                        @else bg-gray-100 @endif">
                                        @if($role->name === 'super_admin')
                                            <i class="fas fa-crown text-red-600"></i>
                                        @elseif(str_contains($role->name, 'admin'))
                                            <i class="fas fa-user-shield text-blue-600"></i>
                                        @else
                                            <i class="fas fa-user-tag text-gray-600"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">
                                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                        </h4>
                                        <p class="text-xs text-gray-500">{{ $role->name }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $role->permissions->count() }} permissions
                                        </p>
                                    </div>
                                </div>
                                @can('view roles')
                                    <a href="{{ route('roles.show', $role) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-user-shield text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500">Tidak ada role yang memiliki permission ini</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deletePermission(permissionId, permissionName) {
    Swal.fire({
        title: 'Hapus Permission?',
        text: `Apakah Anda yakin ingin menghapus permission "${permissionName}"? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = `{{ route('permissions.index') }}/${permissionId}`;
            form.submit();
        }
    });
}
</script>
@endsection
