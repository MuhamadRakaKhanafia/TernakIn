<div id="newSessionForm" class="new-session-form">
    <div class="session-form-container">
        <div class="form-header">
            <h3>Mulai Konsultasi Baru</h3>
            <p>Pilih jenis ternak untuk memulai konsultasi dengan AI</p>
        </div>
        
        <form id="startSessionForm">
            @csrf
            <div class="form-group">
                <label for="animalType">Jenis Ternak *</label>
                <select name="animal_type_id" id="animalType" class="form-control" required>
                    <option value="">Pilih Jenis Ternak</option>
                    @foreach($animalTypes as $type)
                        <option value="{{ $type->id }}">
                            {{ $type->name }} ({{ $type->category ?? 'Umum' }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="sessionTitle">Judul Sesi (Opsional)</label>
                <input type="text" name="title" id="sessionTitle" class="form-control" 
                       placeholder="Contoh: Masalah pencernaan sapi">
            </div>
            
            <div class="form-group">
                <label for="initialMessage">Pesan Awal (Opsional)</label>
                <textarea name="initial_message" id="initialMessage" class="form-control" 
                          rows="3" placeholder="Jelaskan masalah yang dialami ternak Anda..."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-start">
                    <i class="fas fa-comment-medical"></i> Mulai Konsultasi
                </button>
            </div>
        </form>
    </div>
</div>