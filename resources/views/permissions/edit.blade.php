@extends('layouts.app')

@section('title', 'Edit Permission')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('permissions.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Edit Permission</h1>
            <p class="text-gray-600 mt-1">Update informasi permission</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('permissions.update', $permission) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Current Permission Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Permission Saat Ini
                </h4>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-key text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-blue-900">
                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                        </p>
                        <p class="text-xs text-blue-700">{{ $permission->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Permission Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Permission <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $permission->name) }}"
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @else border-gray-300 @enderror"
                       placeholder="Masukkan nama permission (misal: kelola posting, lihat laporan)"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div class="mt-2 text-sm text-gray-500">
                    <p class="mb-1"><strong>Konvensi Penamaan:</strong></p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>Use lowercase with spaces (e.g., "view users", "create posts")</li>
                        <li>Start with action verb (view, create, edit, delete, manage)</li>
                        <li>Be specific and descriptive</li>
                        <li>Avoid spaces at the beginning or end</li>
                    </ul>
                </div>
            </div>

            <!-- Roles using this permission -->
            @if($permission->roles->count() > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-medium text-yellow-900 mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Peringatan: Permission Digunakan oleh Role
                    </h4>
                    <p class="text-sm text-yellow-800 mb-3">
                        Permission ini saat ini digunakan oleh {{ $permission->roles->count() }} role. Perubahan akan mempengaruhi role berikut:
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($permission->roles as $role)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                @if($role->name === 'super_admin') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                @if($role->name === 'super_admin')
                                    <i class="fas fa-crown mr-1"></i>
                                @else
                                    <i class="fas fa-user-shield mr-1"></i>
                                @endif
                                {{ ucwords(str_replace('_', ' ', $role->name)) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Permission Examples -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 mb-2">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Permission Examples
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-blue-800">
                    <div class="space-y-1">
                        <p><strong>User Management:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-0.5 text-xs">
                            <li>view users</li>
                            <li>create users</li>
                            <li>edit users</li>
                            <li>delete users</li>
                        </ul>
                    </div>
                    <div class="space-y-1">
                        <p><strong>Content Management:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-0.5 text-xs">
                            <li>view posts</li>
                            <li>create posts</li>
                            <li>publish posts</li>
                            <li>moderate comments</li>
                        </ul>
                    </div>
                    <div class="space-y-1">
                        <p><strong>System Access:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-0.5 text-xs">
                            <li>access admin panel</li>
                            <li>view reports</li>
                            <li>manage settings</li>
                            <li>view audit logs</li>
                        </ul>
                    </div>
                    <div class="space-y-1">
                        <p><strong>Financial:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-0.5 text-xs">
                            <li>view transactions</li>
                            <li>process payments</li>
                            <li>generate invoices</li>
                            <li>manage billing</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('permissions.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                    Update Permission
                </button>
            </div>
        </form>
    </div>

    <!-- Additional Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-info-circle text-gray-600 mr-2"></i>
                Informasi Tambahan
            </h3>
        </div>
        
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <div class="flex items-start">
                <i class="fas fa-calendar-alt text-gray-400 mr-3 mt-0.5"></i>
                <div>
                    <p class="font-medium text-gray-700">Dibuat pada:</p>
                    <p>{{ $permission->created_at->format('d F Y, H:i') }} ({{ $permission->created_at->diffForHumans() }})</p>
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-clock text-gray-400 mr-3 mt-0.5"></i>
                <div>
                    <p class="font-medium text-gray-700">Terakhir diupdate:</p>
                    <p>{{ $permission->updated_at->format('d F Y, H:i') }} ({{ $permission->updated_at->diffForHumans() }})</p>
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-shield-alt text-gray-400 mr-3 mt-0.5"></i>
                <div>
                    <p class="font-medium text-gray-700">Guard:</p>
                    <p>{{ $permission->guard_name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
