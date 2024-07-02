<div id="addModal" class="modal">
    <div class="modal-content">
        <form id="addForm" action="create" method="POST">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Add note</h2>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td><label for="fname">Title:</label></td>
                        <td><input type="text" id="titel_add" name="titel"></td>
                    </tr>
                    <tr>
                        <td><label for="lname">Task:</label></td>
                        <td><input type="text" id="aufgabe_add" name="aufgabe"></td>
                    </tr>
                    <tr>
                        <td><label for="lname">Date:</label></td>
                        <td><input type="date" id="datum_add" name="datum"></td>
                    </tr>
                    <tr>
                        <td><label for="priority_add">Priority:</label></td>
                        <td>
                            <select name="priority" id="priority_add">
                                <option value="0">Incredibly important</option>
                                <option value="1">Very important</option>
                                <option value="2">Important</option>
                                <option value="3">Moderately important</option>
                                <option value="4">Not important</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <div class="select-button">
                    <button class="hinzufuegen" type="form-submit">Add</button>
                    <button class="verwerfen" type="reset">Discard</button>
                </div>
            </div>
        </form>
    </div>
</div>