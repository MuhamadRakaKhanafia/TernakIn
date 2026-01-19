@extends('layouts.app')

@section('title', 'Detail Jadwal Vaksinasi - Admin TernakIN')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900">
                            <i class="fas fa-tachometer-alt mr-2"></i> Admin Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="{{ route('admin.vaccinations.index') }}" class="text-gray-700 hover:text-gray-900">Review Vaksinasi</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-500">Detail Jadwal</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detail Jadwal Vaksinasi</h1>
                        <p class="mt-1 text-sm text-gray-600">Review dan validasi jadwal vaksinasi pengguna</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.vaccinations.index') }}"
                           class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-gray-700 transition duration-150 ease-in-out">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Vaccination Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Animal Type Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-cow text-green-600 mr-2"></i> Informasi Jenis Hewan
                        </h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jenis Hewan</dt>
                                <dd class="text-sm text-gray-900">{{ $vaccination->animalType->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $vaccination->animalType->category == 'poultry' ? 'Unggas' : ($vaccination->animalType->category == 'large_animal' ? 'Ternak Besar' : 'Lainnya') }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Vaccination Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-syringe text-blue-600 mr-2"></i> Informasi Vaksinasi
                        </h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jenis Vaksin</dt>
                                <dd class="text-sm text-gray-900">{{ $vaccination->vaccine_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Vaksinasi</dt>
                                <dd class="text-sm text-gray-900">{{ $vaccination->vaccination_date ? $vaccination->vaccination_date->format('l, d F Y') : 'Tidak ditentukan' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="text-sm">{!! $vaccination->status_badge !!}</dd>
                            </div>
                            @if($vaccination->reminder_enabled)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pengingat</dt>
                                    <dd class="text-sm text-gray-900">
                                        <i class="fas fa-bell text-yellow-500 mr-1"></i>
                                        {{ $vaccination->reminder_date ? $vaccination->reminder_date->format('d M Y') : 'Aktif' }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- User Information -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user text-purple-600 mr-2"></i> Informasi Pemilik
                    </h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama</dt>
                            <dd class="text-sm text-gray-900">{{ $vaccination->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="text-sm text-gray-900">{{ $vaccination->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Daftar</dt>
                            <dd class="text-sm text-gray-900">{{ $vaccination->created_at->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Hewan Ternak</dt>
                            <dd class="text-sm text-gray-900">{{ $vaccination->user->livestocks()->count() }} ekor</dd>
                        </div>
                    </dl>
                </div>

                @if($vaccination->isPending())
                    <!-- Validation Form -->
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Validasi Jadwal Vaksinasi</h3>

                        <form method="POST" action="{{ route('admin.vaccinations.approve', $vaccination) }}" class="mb-6">
                            @csrf
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <h4 class="text-md font-medium text-green-800 mb-4 flex items-center">
                                    <i class="fas fa-check-circle text-green-600 mr-2"></i> Setujui Jadwal Vaksinasi
                                </h4>

                                <div class="mb-4">
                                    <label for="admin_notes_approve" class="block text-sm font-medium text-gray-700 mb-2">
                                        Catatan Admin (Opsional)
                                    </label>
                                    <textarea id="admin_notes_approve" name="admin_notes" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                              placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="admin_recommendations_approve" class="block text-sm font-medium text-gray-700 mb-2">
                                        Rekomendasi (Opsional)
                                    </label>
                                    <textarea id="admin_recommendations_approve" name="admin_recommendations" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                              placeholder="Berikan rekomendasi jadwal vaksinasi yang lebih baik jika ada..."></textarea>
                                </div>

                                <button type="submit"
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-green-700 transition duration-150 ease-in-out">
                                    <i class="fas fa-check mr-2"></i> Setujui Jadwal
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('admin.vaccinations.reject', $vaccination) }}">
                            @csrf
                            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                                <h4 class="text-md font-medium text-red-800 mb-4 flex items-center">
                                    <i class="fas fa-times-circle text-red-600 mr-2"></i> Tolak Jadwal Vaksinasi
                                </h4>

                                <div class="mb-4">
                                    <label for="admin_notes_reject" class="block text-sm font-medium text-gray-700 mb-2">
                                        Alasan Penolakan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="admin_notes_reject" name="admin_notes" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                              placeholder="Jelaskan alasan penolakan jadwal vaksinasi..."
                                              required></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="admin_recommendations_reject" class="block text-sm font-medium text-gray-700 mb-2">
                                        Rekomendasi Jadwal Baru (Opsional)
                                    </label>
                                    <textarea id="admin_recommendations_reject" name="admin_recommendations" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                              placeholder="Sarankan jadwal vaksinasi yang lebih sesuai..."></textarea>
                                </div>

                                <button type="submit"
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-red-700 transition duration-150 ease-in-out">
                                    <i class="fas fa-times mr-2"></i> Tolak Jadwal
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Admin Notes and Recommendations -->
                    @if($vaccination->admin_notes || $vaccination->admin_recommendations)
                        <div class="border-t border-gray-200 pt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan Admin</h3>

                            @if($vaccination->admin_notes)
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <h4 class="text-sm font-medium text-blue-800 mb-2">Catatan Validasi</h4>
                                    <p class="text-sm text-blue-700">{{ $vaccination->admin_notes }}</p>
                                </div>
                            @endif

                            @if($vaccination->admin_recommendations)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-yellow-800 mb-2">Rekomendasi</h4>
                                    <p class="text-sm text-yellow-700">{{ $vaccination->admin_recommendations }}</p>
                                </div>
                            @endif

                            @if($vaccination->adminValidator)
                                <p class="text-xs text-gray-500 mt-2">
                                    Divalidasi oleh: {{ $vaccination->adminValidator->name }} pada {{ $vaccination->admin_validated_at ? $vaccination->admin_validated_at->format('d M Y H:i') : 'Tidak diketahui' }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if($vaccination->isApproved())
                        <div class="border-t border-gray-200 pt-8">
                            <form method="POST" action="{{ route('admin.vaccinations.complete', $vaccination) }}">
                                @csrf
                                <button type="submit"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-blue-700 transition duration-150 ease-in-out">
                                    <i class="fas fa-check-double mr-2"></i> Tandai sebagai Selesai
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
