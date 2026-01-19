@extends('layouts.app')

@section('title', 'Kelola Broadcast - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Broadcast</h1>
                <p class="mt-2 text-gray-600">Kelola pesan broadcast yang ditampilkan kepada pengguna</p>
            </div>
            <a href="{{ route('admin.broadcasts.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-150 ease-in-out flex items-center">
                <i class="fas fa-plus mr-2"></i> Buat Broadcast Baru
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h2>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.broadcasts.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Cari Pesan</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                           placeholder="Cari berdasarkan pesan atau teks link...">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="is_active" id="is_active"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-150 ease-in-out">
                        <i class="fas fa-search mr-2"></i> Cari
                    </button>
                    <a href="{{ route('admin.broadcasts.index') }}"
                       class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-150 ease-in-out">
                        <i class="fas fa-times mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Broadcasts List -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        @if($broadcasts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kadaluarsa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($broadcasts as $broadcast)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $broadcast->message }}">
                                    {!! Str::limit(strip_tags($broadcast->message), 50) !!}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($broadcast->link_url)
                                    <a href="{{ $broadcast->link_url }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        {{ $broadcast->link_text ?: 'Link' }}
                                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                    </a>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $isExpired = $broadcast->expires_at && $broadcast->expires_at->isPast();
                                    $isActive = $broadcast->is_active && !$isExpired;
                                @endphp
                                @if($isActive)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                    </span>
                                @elseif($isExpired)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-clock mr-1"></i> Kadaluarsa
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <i class="fas fa-pause-circle mr-1"></i> Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($broadcast->expires_at)
                                    {{ $broadcast->expires_at->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $broadcast->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.broadcasts.edit', $broadcast) }}"
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.broadcasts.destroy', $broadcast) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus broadcast ini?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $broadcasts->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-bullhorn text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada broadcast</h3>
                <p class="text-gray-500 mb-6">Mulai buat broadcast pertama untuk menginformasikan pengguna.</p>
                <a href="{{ route('admin.broadcasts.create') }}"
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-150 ease-in-out inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i> Buat Broadcast Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
