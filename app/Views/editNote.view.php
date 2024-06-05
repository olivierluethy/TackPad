<!-- The Modal --> <!-- Sobald das Modal hinzugefügt wird, funktioniert das ganze JavaScript nicht mehr -->
<div id="editModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <form id="editForm" action="" method="post">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Edit Task</h2>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td><label for="fname">Title:</label></td>
                        <td><input type="text" id="titel" name="titel"></td>
                    </tr>
                    <tr>
                        <td><label for="lname">Task:</label></td>
                        <td><input type="text" id="aufgabe" name="aufgabe"></td>
                    </tr>
                    <tr>
                        <td><label for="lname">Date:</label></td>
                        <td><input type="date" id="datum" name="datum"></td>
                    </tr>
                    <tr>
                        <td><label for="lname">Priority:</label></td>
                        <td>
                            <select name="priority" id="priority">
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
                <div class="submit">
                    <button class="hinzufuegen" type="submit">Edit</button>
                    <button class="verwerfen" type="reset">Disgard</button>
                </div>
            </div>
        </form>
    </div>
</div>
