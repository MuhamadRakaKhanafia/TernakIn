@extends('layouts.app')

@section('title', 'Add New Disease')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add New Disease</h1>
        <p class="mt-2 text-gray-600">Create a new disease entry in the system</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Disease Details</h2>
        </div>

        <form method="POST" action="{{ route('admin.diseases.store') }}" class="p-6 space-y-6">
            @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Disease Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Disease Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Disease Code -->
            <div>
                <label for="disease_code" class="block text-sm font-medium text-gray-700">Disease Code *</label>
                <input type="text" name="disease_code" id="disease_code" value="{{ old('disease_code') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                       required>
                @error('disease_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Causative Agent -->
            <div>
                <label for="causative_agent" class="block text-sm font-medium text-gray-700">Causative Agent *</label>
                <select name="causative_agent" id="causative_agent"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                        required>
                    <option value="">Select Causative Agent</option>
                    <option value="virus" {{ old('causative_agent') === 'virus' ? 'selected' : '' }}>Virus</option>
                    <option value="bakteri" {{ old('causative_agent') === 'bakteri' ? 'selected' : '' }}>Bakteri</option>
                    <option value="parasit" {{ old('causative_agent') === 'parasit' ? 'selected' : '' }}>Parasit</option>
                    <option value="fungi" {{ old('causative_agent') === 'fungi' ? 'selected' : '' }}>Fungi</option>
                    <option value="defisiensi_nutrisi" {{ old('causative_agent') === 'defisiensi_nutrisi' ? 'selected' : '' }}>Defisiensi Nutrisi</option>
                    <option value="lainnya" {{ old('causative_agent') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('causative_agent')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Other Names -->
            <div>
                <label for="other_names" class="block text-sm font-medium text-gray-700">Other Names</label>
                <input type="text" name="other_names" id="other_names" value="{{ old('other_names') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                       placeholder="Alternative names separated by commas">
                @error('other_names')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Symptoms -->
            <div class="col-span-full">
                <label class="block text-sm font-medium text-gray-700">Symptoms</label>
                <div id="symptoms-container" class="mt-1 space-y-2">
                    <div class="symptom-item flex gap-2">
                        <select name="symptoms[0][symptom_id]" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Symptom</option>
                            @foreach($symptoms as $symptom)
                                <option value="{{ $symptom->id }}">{{ $symptom->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="symptoms[0][probability]" placeholder="Probability %" min="1" max="100"
                               class="w-24 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <label class="flex items-center">
                            <input type="checkbox" name="symptoms[0][is_primary]" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-900">Primary</span>
                        </label>
                        <button type="button" class="remove-symptom text-red-600 hover:text-red-800 text-sm" style="display: none;">Remove</button>
                    </div>
                </div>
                <button type="button" id="add-symptom" class="mt-2 text-green-600 hover:text-green-700 text-sm font-medium">+ Add Symptom</button>
            </div>

            <!-- Animal Types -->
            <div class="col-span-full">
                <label class="block text-sm font-medium text-gray-700">Affected Animal Types *</label>
                <div class="mt-1 grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($animalTypes as $animalType)
                        <label class="flex items-center">
                            <input type="checkbox" name="animal_types[]" value="{{ $animalType->id }}"
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded @error('animal_types') border-red-500 @enderror">
                            <span class="ml-2 block text-sm text-gray-900">{{ $animalType->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('animal_types')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Treatment -->
            <div class="col-span-full">
                <label for="treatment" class="block text-sm font-medium text-gray-700">Treatment</label>
                <textarea name="treatment" id="treatment" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('treatment') }}</textarea>
                @error('treatment')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Prevention -->
            <div class="col-span-full">
                <label for="prevention" class="block text-sm font-medium text-gray-700">Prevention</label>
                <textarea name="prevention" id="prevention" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('prevention') }}</textarea>
                @error('prevention')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Active
                </label>
            </div>
                </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.diseases.index') }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Batal
                </a>
                <button type="submit"
                        class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-save mr-2"></i> Simpan Penyakit
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('add-symptom').addEventListener('click', function() {
    const container = document.getElementById('symptoms-container');
    const symptomItems = container.querySelectorAll('.symptom-item');
    const index = symptomItems.length;

    const newSymptom = document.createElement('div');
    newSymptom.className = 'symptom-item flex gap-2 mb-2';
    newSymptom.innerHTML = `
        <select name="symptoms[${index}][symptom_id]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Select Symptom</option>
            @foreach($symptoms as $symptom)
                <option value="{{ $symptom->id }}">{{ $symptom->name }}</option>
            @endforeach
        </select>
        <input type="number" name="symptoms[${index}][probability]" placeholder="Probability %" min="1" max="100"
               class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <label class="flex items-center">
            <input type="checkbox" name="symptoms[${index}][is_primary]" class="mr-2">
            Primary
        </label>
        <button type="button" class="remove-symptom text-red-600 hover:text-red-800">Remove</button>
    `;

    container.appendChild(newSymptom);
    updateRemoveButtons();
});

function updateRemoveButtons() {
    const symptomItems = document.querySelectorAll('.symptom-item');
    symptomItems.forEach((item, index) => {
        const removeBtn = item.querySelector('.remove-symptom');
        if (symptomItems.length > 1) {
            removeBtn.style.display = 'block';
        } else {
            removeBtn.style.display = 'none';
        }
    });
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-symptom')) {
        e.target.closest('.symptom-item').remove();
        updateRemoveButtons();
    }
});

updateRemoveButtons();
</script>
@endsection
