<div id="addModal" class="modal">
    <div class="modal-content">
        <form id="addForm" action="create" method="POST">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Add task</h2>
            </div>
            <div class="modal-body">
                <div class="field-row">
                    <label class="field-label" for="titel_add">Title</label>
                    <input type="text" id="titel_add" name="titel" autocomplete="off">
                </div>
                <div class="field-row">
                    <label class="field-label" for="aufgabe_add">Task</label>
                    <input type="text" id="aufgabe_add" name="aufgabe" autocomplete="off">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="field-row">
                        <label class="field-label" for="datum_add">Date</label>
                        <input type="date" id="datum_add" name="datum">
                    </div>
                    <div class="field-row">
                        <label class="field-label" for="zeit_add">Time</label>
                        <input type="time" id="zeit_add" name="zeit">
                    </div>
                </div>
                <div class="field-row">
                    <label class="field-label" for="priority_add">Priority</label>
                    <select name="priority" id="priority_add">
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
                    <button class="btn-primary" type="submit">Add task</button>
                    <button class="btn-danger" type="reset">Discard</button>
                </div>
            </div>
        </form>
    </div>
</div>
