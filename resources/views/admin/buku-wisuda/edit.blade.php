@extends('layouts.admin')

@section('title', 'Edit Buku Wisuda')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.buku-wisuda.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">&larr; Kembali</a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Buku Wisuda</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('admin.buku-wisuda.update', $bukuWisuda) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Acara Wisuda *</label>
                        <select name="graduation_event_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            @foreach(\App\Models\GraduationEvent::pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}" {{ old('graduation_event_id', $bukuWisuda->graduation_event_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">File Saat Ini</label>
                        <p class="text-sm text-gray-900">{{ $bukuWisuda->filename }}</p>
                        <p class="text-sm text-gray-500">Ukuran: {{ $bukuWisuda->getHumanFileSize() }}</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center space-x-3">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">Update</button>
                    <a href="{{ route('admin.buku-wisuda.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection