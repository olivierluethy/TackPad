<!-- The Modal --> <!-- Sobald das Modal hinzugefügt wird, funktioniert das ganze JavaScript nicht mehr -->
<div id="deleteModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <form action="" method="post">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Wollen Sie es wirklich löschen?</h2>
            </div>
            <div class="modal-body">
                <button onclick="deleteNote()" class="yesButton"><i class="fas fa-check"></i> Yes</button>
                <button class="noButton">No</button>
            </div>
        </form>
    </div>
</div>