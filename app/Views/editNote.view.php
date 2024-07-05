<div id="editModal" class="modal">
    <div class="modal-content">
        <form id="editForm" action="" method="post">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Edit Task</h2>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td><label for="titel_edit">Title:</label></td>
                        <td><input type="text" id="titel_edit" name="titel"></td>
                    </tr>
                    <tr>
                        <td><label for="aufgabe_edit">Task:</label></td>
                        <td><input type="text" id="aufgabe_edit" name="aufgabe"></td>
                    </tr>
                    <tr>
                        <td><label for="datum_edit">Date:</label></td>
                        <td><input type="date" id="datum_edit" name="datum"></td>
                    </tr>
                    <tr>
                        <td><label for="priority_edit">Priority:</label></td>
                        <td>
                            <select name="priority" id="priority_edit">
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
                    <button class="hinzufuegen" type="submit">Edit</button>
                    <button class="verwerfen" type="reset">Discard</button>
                </div>
            </div>
        </form>
    </div>
</div>
