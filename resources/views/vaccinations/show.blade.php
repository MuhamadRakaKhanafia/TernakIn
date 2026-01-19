@extends('layouts.app')

@section('title', 'Detail Vaksinasi')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Detail Vaksinasi</h1>
            <div class="flex space-x-2">
                <a href="{{ route('vaccinations.edit', $vaccination) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('vaccinations.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Vaksinasi</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Nama Ternak</label>
                        <p class="text-sm text-gray-900">{{ $vaccination->livestock->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Nama Vaksin</label>
                        <p class="text-sm text-gray-900">{{ $vaccination->vaccine_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Tanggal Vaksinasi</label>
                        <p class="text-sm text-gray-900">{{ $vaccination->vaccination_date->format('d M Y') }}</p>
                    </div>
                    @if($vaccination->next_vaccination_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Vaksinasi Selanjutnya</label>
                        <p class="text-sm text-gray-900">{{ $vaccination->next_vaccination_date->format('d M Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Catatan & Status</h3>
                <div class="space-y-3">
                    @if($vaccination->notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Catatan</label>
                        <p class="text-sm text-gray-900">{{ $vaccination->notes }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($vaccination->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($vaccination->status === 'approved') bg-green-100 text-green-800
                            @elseif($vaccination->status === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($vaccination->status === 'pending') Menunggu Validasi
                            @elseif($vaccination->status === 'approved') Disetujui
                            @elseif($vaccination->status === 'rejected') Ditolak
                            @else {{ ucfirst($vaccination->status) }} @endif
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Dibuat Pada</label>
                        <p class="text-sm text-gray-900">{{ $vaccination->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Terakhir Diupdate</label>
                        <p class="text-sm text-gray-900">{{ $vaccination->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($vaccination->status === 'pending')
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Menunggu Validasi Admin
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Jadwal vaksinasi Anda sedang dalam proses validasi oleh admin. Anda akan menerima notifikasi setelah diverifikasi.</p>
                    </div>
                </div>
            </div>
        </div>
        @elseif($vaccination->status === 'approved')
        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">
                        Jadwal Vaksinasi Disetujui
                    </h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Jadwal vaksinasi Anda telah disetujui oleh admin. Pastikan untuk melakukan vaksinasi sesuai jadwal yang telah ditentukan.</p>
                    </div>
                </div>
            </div>
        </div>
        @elseif($vaccination->status === 'rejected')
        <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Jadwal Vaksinasi Ditolak
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Jadwal vaksinasi Anda ditolak oleh admin. Silakan periksa kembali dan ajukan ulang dengan informasi yang lebih lengkap.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
