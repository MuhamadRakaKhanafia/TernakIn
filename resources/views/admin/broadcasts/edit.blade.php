10@extends('layouts.app')

@section('title', 'Edit Broadcast - Admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Broadcast</h1>
        <p class="mt-2 text-gray-600">Ubah informasi broadcast yang akan ditampilkan kepada pengguna</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Detail Broadcast</h2>
        </div>

        <form method="POST" action="{{ route('admin.broadcasts.update', $broadcast) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Pesan Broadcast</label>
                <textarea name="message" id="message" rows="4"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          placeholder="Masukkan pesan yang akan ditampilkan sebagai tulisan berjalan..."
                          required>{{ old('message', $broadcast->message) }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Pesan ini akan ditampilkan sebagai tulisan berjalan di bawah navbar.</p>
            </div>

            <!-- Link URL -->
            <div>
                <label for="link_url" class="block text-sm font-medium text-gray-700">URL Link (Opsional)</label>
                <input type="url" name="link_url" id="link_url" value="{{ old('link_url', $broadcast->link_url) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                       placeholder="https://example.com">
                @error('link_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Tambahkan link jika broadcast ini mengarah ke halaman tertentu.</p>
            </div>

            <!-- Link Text -->
            <div>
                <label for="link_text" class="block text-sm font-medium text-gray-700">Teks Link (Opsional)</label>
                <input type="text" name="link_text" id="link_text" value="{{ old('link_text', $broadcast->link_text) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                       placeholder="Pelajari Lebih Lanjut">
                @error('link_text')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Teks yang akan ditampilkan untuk link. Jika kosong, akan menggunakan "Pelajari Lebih Lanjut".</p>
            </div>

            <!-- Expires At -->
            <div>
                <label for="expires_at" class="block text-sm font-medium text-gray-700">Tanggal Kadaluarsa (Opsional)</label>
                <input type="datetime-local" name="expires_at" id="expires_at"
                       value="{{ old('expires_at', $broadcast->expires_at ? $broadcast->expires_at->format('Y-m-d\TH:i') : '') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                @error('expires_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Broadcast akan otomatis dinonaktifkan setelah tanggal ini. Kosongkan jika tidak ada batas waktu.</p>
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $broadcast->is_active) ? 'checked' : '' }}
                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Aktifkan broadcast ini
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.broadcasts.index') }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Batal
                </a>
                <button type="submit"
                        class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-save mr-2"></i> Update Broadcast
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
