<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2>Delete this task?</h2>
        </div>
        <div class="modal-body">
            <p class="text-center text-tp-black mb-2">This can't be undone.</p>
            <div class="select-button">
                <button onclick="deleteNote()" class="btn-danger" type="button"><i class="fas fa-trash"></i>&nbsp;Delete</button>
                <button onclick="closeReallyDelete()" class="btn-primary" type="button"><i class="fas fa-xmark"></i>&nbsp;Keep</button>
            </div>
        </div>
        <div class="modal-footer"></div>
    </div>
</div>
