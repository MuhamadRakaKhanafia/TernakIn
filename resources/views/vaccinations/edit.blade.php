@extends('layouts.app')

@section('title', 'Edit Jadwal Vaksinasi - TernakIN')

@section('content')
<div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900">
                            <i class="fas fa-home mr-2"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="{{ route('vaccinations.index') }}" class="text-gray-700 hover:text-gray-900">Jadwal Vaksinasi</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-500">Edit Jadwal</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Jadwal Vaksinasi</h1>

                <form method="POST" action="{{ route('vaccinations.update', $vaccination) }}">
                    @csrf
                    @method('PUT')

                    <!-- Livestock Selection -->
                    <div class="mb-6">
                        <label for="livestock_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Hewan Ternak <span class="text-red-500">*</span>
                        </label>
                        <select id="animal_type_id" name="animal_type_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required>
                            <option value="">-- Pilih Jenis Hewan --</option>
                            @foreach($animalTypes as $animalType)
                                <option value="{{ $animalType->id }}"
                                        {{ (old('animal_type_id', $vaccination->animal_type_id)) == $animalType->id ? 'selected' : '' }}>
                                    {{ $animalType->name }} ({{ $animalType->category == 'poultry' ? 'Unggas' : ($animalType->category == 'large_animal' ? 'Ternak Besar' : 'Lainnya') }})
                                </option>
                            @endforeach
                        </select>
                        @error('livestock_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Vaccine Type -->
                    <div class="mb-6">
                        <label for="vaccine_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Vaksin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="vaccine_type" name="vaccine_type"
                               value="{{ old('vaccine_type', $vaccination->vaccine_type) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                               placeholder="Contoh: Vaksin Newcastle, Vaksin Anthrax, dll."
                               required>
                        @error('vaccine_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Scheduled Date -->
                    <div class="mb-6">
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Vaksinasi <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="scheduled_date" name="scheduled_date"
                               value="{{ old('scheduled_date', $vaccination->vaccination_date->format('Y-m-d')) }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                               required>
                        @error('scheduled_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reminder Settings -->
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="reminder_enabled" name="reminder_enabled"
                                   value="1" {{ old('reminder_enabled', $vaccination->reminder_enabled) ? 'checked' : '' }}
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="reminder_enabled" class="ml-2 block text-sm text-gray-900">
                                Aktifkan Pengingat Otomatis
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Anda akan mendapat notifikasi sebelum jadwal vaksinasi.</p>
                    </div>

                    <!-- Reminder Date -->
                    <div class="mb-6 reminder-date-section" style="{{ old('reminder_enabled', $vaccination->reminder_enabled) ? '' : 'display: none;' }}">
                        <label for="reminder_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Pengingat
                        </label>
                        <input type="date" id="reminder_date" name="reminder_date"
                               value="{{ old('reminder_date', $vaccination->reminder_date?->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <p class="mt-1 text-sm text-gray-500">Pilih tanggal sebelum jadwal vaksinasi untuk mendapat pengingat.</p>
                        @error('reminder_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('vaccinations.index') }}"
                           class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Batal
                        </a>
                        <button type="submit"
                                class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-save mr-2"></i> Update Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reminderCheckbox = document.getElementById('reminder_enabled');
    const reminderDateSection = document.querySelector('.reminder-date-section');
    const reminderDateInput = document.getElementById('reminder_date');
    const scheduledDateInput = document.getElementById('scheduled_date');

    function toggleReminderDate() {
        if (reminderCheckbox.checked) {
            reminderDateSection.style.display = 'block';
            reminderDateInput.required = true;
        } else {
            reminderDateSection.style.display = 'none';
            reminderDateInput.required = false;
            reminderDateInput.value = '';
        }
    }

    function updateReminderMaxDate() {
        if (scheduledDateInput.value) {
            const scheduledDate = new Date(scheduledDateInput.value);
            const maxReminderDate = new Date(scheduledDate);
            maxReminderDate.setDate(scheduledDate.getDate() - 1);
            reminderDateInput.max = maxReminderDate.toISOString().split('T')[0];
        }
    }

    reminderCheckbox.addEventListener('change', toggleReminderDate);
    scheduledDateInput.addEventListener('change', updateReminderMaxDate);

    // Initialize on page load
    toggleReminderDate();
    updateReminderMaxDate();
});
</script>
@endsection
