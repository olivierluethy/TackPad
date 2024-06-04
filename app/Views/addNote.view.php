<!-- The Modal -->
<div id="addModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <form action="create" method="POST" id="createForm">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Add note</h2>
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
                                <option value="1">Incredibly important</option>
                                <option value="2">Very important</option>
                                <option value="3">Important</option>
                                <option value="4">Moderately important</option>
                                <option value="5">Not important</option>
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