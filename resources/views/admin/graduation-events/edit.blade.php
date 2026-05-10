@extends('layouts.admin')

@section('title', 'Edit Acara Wisuda')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.graduation-events.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">&larr; Kembali</a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Acara Wisuda</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('admin.graduation-events.update', $graduationEvent) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Acara *</label>
                        <input type="text" name="name" value="{{ old('name', $graduationEvent->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                            <input type="date" name="date" value="{{ old('date', $graduationEvent->date->format('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu *</label>
                            <input type="time" name="time" value="{{ old('time', $graduationEvent->time->format('H:i')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi *</label>
                        <input type="text" name="location_name" value="{{ old('location_name', $graduationEvent->location_name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lokasi *</label>
                        <textarea name="location_address" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('location_address', $graduationEvent->location_address) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                            <input type="number" name="location_lat" value="{{ old('location_lat', $graduationEvent->location_lat) }}" step="0.00000001"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                            <input type="number" name="location_lng" value="{{ old('location_lng', $graduationEvent->location_lng) }}" step="0.00000001"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL Maps</label>
                        <input type="url" name="maps_url" value="{{ old('maps_url', $graduationEvent->maps_url) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Unggulan</label>
                        @if($graduationEvent->feature_image)
                            <img src="{{ asset('storage/event-features/' . $graduationEvent->feature_image) }}" alt="Feature" class="w-32 h-20 object-cover rounded mb-2">
                        @endif
                        <input type="file" name="feature_image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select name="status" id="status-select" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                                onchange="if(this.value === 'completed' && !confirm('PERINGATAN: Menandai acara sebagai SELESAI akan mengarsipkan semua data tiket, kehadiran, dan konsumsi. Data tidak akan terhapus tapi tidak akan muncul di dashboard. Lanjutkan?')) { this.value = '{{ old('status', $graduationEvent->status) }}'; }">
                            <option value="upcoming" {{ old('status', $graduationEvent->status) == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                            <option value="active" {{ old('status', $graduationEvent->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="completed" {{ old('status', $graduationEvent->status) == 'completed' ? 'selected' : '' }}>Selesai (Arsip)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            <strong>Akan Datang:</strong> Event belum aktif | 
                            <strong>Aktif:</strong> Event sedang berlangsung | 
                            <strong>Selesai:</strong> Event selesai, data diarsipkan
                        </p>
                        @if($graduationEvent->status === 'completed')
                            <p class="text-xs text-red-600 mt-1 font-medium">⚠️ Acara ini sudah diarsip. Mengubah status akan mengembalikan data ke dashboard.</p>
                        @endif
                    </div>

                    <input type="hidden" name="is_active" value="{{ $graduationEvent->is_active ? '1' : '0' }}">
                </div>

                <div class="mt-6 flex items-center space-x-3">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">Update</button>
                    <a href="{{ route('admin.graduation-events.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection