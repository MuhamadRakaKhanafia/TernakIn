@extends('layouts.app')

@section('title', 'Edit Disease')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Disease</h1>
                    <p class="text-gray-600">Update disease information</p>
                </div>
                <a href="{{ route('admin.diseases.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Back to Diseases
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.diseases.update', $disease) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Disease Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Disease Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $disease->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Disease Code -->
                    <div>
                        <label for="disease_code" class="block text-sm font-medium text-gray-700 mb-2">Disease Code *</label>
                        <input type="text" id="disease_code" name="disease_code" value="{{ old('disease_code', $disease->disease_code) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('disease_code') border-red-500 @enderror"
                               required>
                        @error('disease_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Causative Agent -->
                    <div>
                        <label for="causative_agent" class="block text-sm font-medium text-gray-700 mb-2">Causative Agent *</label>
                        <select id="causative_agent" name="causative_agent"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('causative_agent') border-red-500 @enderror"
                                required>
                            <option value="">Select Causative Agent</option>
                            <option value="virus" {{ old('causative_agent', $disease->causative_agent) == 'virus' ? 'selected' : '' }}>Virus</option>
                            <option value="bakteri" {{ old('causative_agent', $disease->causative_agent) == 'bakteri' ? 'selected' : '' }}>Bakteri</option>
                            <option value="parasit" {{ old('causative_agent', $disease->causative_agent) == 'parasit' ? 'selected' : '' }}>Parasit</option>
                            <option value="fungi" {{ old('causative_agent', $disease->causative_agent) == 'fungi' ? 'selected' : '' }}>Fungi</option>
                            <option value="defisiensi_nutrisi" {{ old('causative_agent', $disease->causative_agent) == 'defisiensi_nutrisi' ? 'selected' : '' }}>Defisiensi Nutrisi</option>
                            <option value="lainnya" {{ old('causative_agent', $disease->causative_agent) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('causative_agent')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Other Names -->
                    <div class="md:col-span-2">
                        <label for="other_names" class="block text-sm font-medium text-gray-700 mb-2">Other Names</label>
                        <input type="text" id="other_names" name="other_names" value="{{ old('other_names', $disease->other_names) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Alternative names separated by commas">
                        @error('other_names')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $disease->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Symptoms -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Symptoms</label>
                        <div id="symptoms-container">
                            @foreach($disease->symptoms as $index => $symptom)
                                <div class="symptom-item flex gap-2 mb-2">
                                    <select name="symptoms[{{ $index }}][symptom_id]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Symptom</option>
                                        @foreach($symptoms as $availableSymptom)
                                            <option value="{{ $availableSymptom->id }}" {{ $availableSymptom->id == $symptom->id ? 'selected' : '' }}>{{ $availableSymptom->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="symptoms[{{ $index }}][probability]" placeholder="Probability %" min="1" max="100"
                                           value="{{ $symptom->pivot->probability * 100 }}"
                                           class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="symptoms[{{ $index }}][is_primary]" {{ $symptom->pivot->is_primary ? 'checked' : '' }} class="mr-2">
                                        Primary
                                    </label>
                                    <button type="button" class="remove-symptom text-red-600 hover:text-red-800">Remove</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-symptom" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">+ Add Symptom</button>
                    </div>

                    <!-- Animal Types -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Affected Animal Types *</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($animalTypes as $animalType)
                                <label class="flex items-center">
                                    <input type="checkbox" name="animal_types[]" value="{{ $animalType->id }}"
                                           {{ $disease->animalTypes->contains($animalType->id) ? 'checked' : '' }}
                                           class="mr-2 @error('animal_types') border-red-500 @enderror">
                                    {{ $animalType->name }}
                                </label>
                            @endforeach
                        </div>
                        @error('animal_types')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Treatment -->
                    <div class="md:col-span-2">
                        <label for="treatment" class="block text-sm font-medium text-gray-700 mb-2">Treatment</label>
                        <textarea id="treatment" name="treatment" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('treatment', $disease->treatment) }}</textarea>
                        @error('treatment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prevention -->
                    <div class="md:col-span-2">
                        <label for="prevention" class="block text-sm font-medium text-gray-700 mb-2">Prevention</label>
                        <textarea id="prevention" name="prevention" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('prevention', $disease->prevention) }}</textarea>
                        @error('prevention')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Active -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $disease->is_active) ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.diseases.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        Update Disease
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
