<!-- Sessions Modal -->
<div class="modal fade" id="sessionsModal" tabindex="-1" role="dialog" aria-labelledby="sessionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sessionsModalLabel">Kelola Sesi Chat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="sessions-list" id="sessionsList">
                    <!-- Sessions will be loaded here via JavaScript -->
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Memuat...</span>
                        </div>
                        <p>Memuat daftar sesi...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
