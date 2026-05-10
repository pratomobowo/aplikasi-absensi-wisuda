@extends('layouts.admin')

@section('title', 'Detail User')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">&larr; Kembali</a>
            <h1 class="text-2xl font-bold text-gray-900">Detail User</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">ID</span>
                    <span class="text-sm font-medium text-gray-900">{{ $user->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Nama</span>
                    <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Email</span>
                    <span class="text-sm font-medium text-gray-900">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Role</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Dibuat</span>
                    <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Diperbarui</span>
                    <span class="text-sm font-medium text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</span>
                </div>
            </div>

            <div class="mt-6 flex items-center space-x-3">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">Edit</a>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Kembali</a>
            </div>
        </div>
    </div>
@endsection