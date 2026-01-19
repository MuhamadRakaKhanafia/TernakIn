@extends('layouts.app')

@section('title', 'Tambah Vaksinasi Baru')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tambah Vaksinasi Baru</h1>
        <p class="mt-2 text-gray-600">Buat jadwal vaksinasi baru untuk hewan ternak</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Detail Vaksinasi</h2>
        </div>

        <form method="POST" action="{{ route('admin.vaccinations.store') }}" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Selection -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Pemilik Hewan Ternak *</label>
                    <select name="user_id" id="user_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            required onchange="filterLivestocks()">
                        <option value="">Pilih Pemilik</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Livestock Selection -->
                <div>
                    <label for="livestock_id" class="block text-sm font-medium text-gray-700">Hewan Ternak *</label>
                    <select name="livestock_id" id="livestock_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            required>
                        <option value="">Pilih Hewan Ternak</option>
                        @foreach($livestocks as $livestock)
                            <option value="{{ $livestock->id }}"
                                    data-user-id="{{ $livestock->user_id }}"
                                    {{ old('livestock_id') == $livestock->id ? 'selected' : '' }}
                                    {{ $livestock->user_id == old('user_id') || empty(old('user_id')) ? '' : 'style="display: none;"' }}>
                                {{ $livestock->name }} - {{ $livestock->animalType->name }} ({{ $livestock->user->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('livestock_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vaccine Name -->
                <div>
                    <label for="vaccine_name" class="block text-sm font-medium text-gray-700">Nama Vaksin *</label>
                    <input type="text" name="vaccine_name" id="vaccine_name" value="{{ old('vaccine_name') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                           required>
                    @error('vaccine_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vaccination Date -->
                <div>
                    <label for="vaccination_date" class="block text-sm font-medium text-gray-700">Tanggal Vaksinasi *</label>
                    <input type="date" name="vaccination_date" id="vaccination_date" value="{{ old('vaccination_date') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                           required>
                    @error('vaccination_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Next Vaccination Date -->
                <div>
                    <label for="next_vaccination_date" class="block text-sm font-medium text-gray-700">Tanggal Vaksinasi Selanjutnya</label>
                    <input type="date" name="next_vaccination_date" id="next_vaccination_date" value="{{ old('next_vaccination_date') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('next_vaccination_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" id="status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            required>
                        <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="col-span-full">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Admin Notes -->
                <div class="col-span-full">
                    <label for="admin_notes" class="block text-sm font-medium text-gray-700">Catatan Admin</label>
                    <textarea name="admin_notes" id="admin_notes" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                              placeholder="Catatan internal untuk admin">{{ old('admin_notes') }}</textarea>
                    @error('admin_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Admin Recommendations -->
                <div class="col-span-full">
                    <label for="admin_recommendations" class="block text-sm font-medium text-gray-700">Rekomendasi Admin</label>
                    <textarea name="admin_recommendations" id="admin_recommendations" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                              placeholder="Rekomendasi yang akan diberikan kepada user">{{ old('admin_recommendations') }}</textarea>
                    @error('admin_recommendations')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.vaccinations.index') }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Batal
                </a>
                <button type="submit"
                        class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-save mr-2"></i> Simpan Vaksinasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function filterLivestocks() {
    const userId = document.getElementById('user_id').value;
    const livestockOptions = document.querySelectorAll('#livestock_id option');

    livestockOptions.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block'; // Always show the placeholder
            return;
        }

        const optionUserId = option.getAttribute('data-user-id');
        if (userId === '' || optionUserId === userId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });

    // Reset livestock selection if current selection doesn't match the selected user
    const currentLivestockId = document.getElementById('livestock_id').value;
    if (currentLivestockId) {
        const selectedOption = document.querySelector(`#livestock_id option[value="${currentLivestockId}"]`);
        if (selectedOption && selectedOption.style.display === 'none') {
            document.getElementById('livestock_id').value = '';
        }
    }
}

// Initialize filter on page load
document.addEventListener('DOMContentLoaded', function() {
    filterLivestocks();
});
</script>
@endsection
