<!-- The Modal -->
<div id="addModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <form action="create" method="POST" id="createForm">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Notiz hinzufügen</h2>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td><label for="fname">Titel:</label></td>
                        <td><input type="text" id="titel" name="titel"></td>
                    </tr>
                    <tr>
                        <td><label for="lname">Aufgabe:</label></td>
                        <td><input type="text" id="aufgabe" name="aufgabe"></td>
                    </tr>
                    <tr>
                        <td><label for="lname">Datum:</label></td>
                        <td><input type="date" id="datum" name="datum"></td>
                    </tr>
                    <tr>
                        <td><label for="lname">Priorität:</label></td>
                        <td>
                            <select name="prioritaet" id="prioritaet">
                                <option value="">
                                    <--- Select --->
                                </option>
                                <option value="1">Unglaublich wichtig</option>
                                <option value="2">Sehr wichtig</option>
                                <option value="3">Wichtig</option>
                                <option value="4">Mässig wichtig</option>
                                <option value="5">Nicht wichtig</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <div class="select-button">
                    <button class="hinzufuegen" type="form-submit">Hinzufügen</button>
                    <button class="verwerfen" type="reset">Verwerfen</button>
                </div>
            </div>
        </form>
    </div>
</div>