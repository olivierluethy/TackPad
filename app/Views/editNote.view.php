<div id="editModal" class="modal">
    <div class="modal-content">
        <form id="editForm" action="" method="post">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Edit task</h2>
            </div>
            <div class="modal-body">
                <div class="field-row">
                    <label class="field-label" for="titel_edit">Title</label>
                    <input type="text" id="titel_edit" name="titel" autocomplete="off">
                </div>
                <div class="field-row">
                    <label class="field-label" for="aufgabe_edit">Task</label>
                    <input type="text" id="aufgabe_edit" name="aufgabe" autocomplete="off">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="field-row">
                        <label class="field-label" for="datum_edit">Date</label>
                        <input type="date" id="datum_edit" name="datum">
                    </div>
                    <div class="field-row">
                        <label class="field-label" for="zeit_edit">Time</label>
                        <input type="time" id="zeit_edit" name="zeit">
                    </div>
                </div>
                <div class="field-row">
                    <label class="field-label" for="priority_edit">Priority</label>
                    <select name="priority" id="priority_edit">
                        <option value="0">Incredibly important</option>
                        <option value="1">Very important</option>
                        <option value="2">Important</option>
                        <option value="3">Moderately important</option>
                        <option value="4">Not important</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <div class="select-button">
                    <button class="btn-edit" type="submit">Save changes</button>
                    <button class="btn-danger" type="reset">Discard</button>
                </div>
            </div>
        </form>
    </div>
</div>
