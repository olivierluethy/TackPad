<!-- The Modal -->
<div id="editModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <form action="/action_page.php">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2>Notiz bearbeiten</h2>
        </div>
        <div class="modal-body">
            <table>
                <tr>
                    <td><label for="fname">Titel:</label></td>
                    <td><input type="text" id="fname" name="titel"></td>
                </tr>
                <tr>
                    <td><label for="lname">Aufgabe:</label></td>
                    <td><input type="text" id="lname" name="aufgabe"></td>
                <tr>
                    <td><label for="lname">Datum:</label></td>
                    <td><input type="date" id="lname" name="date"></td>
                </tr>
                <tr>
                    <td><label for="lname">Priorität:</label></td>
                    <td><input type="text" id="lname" name="prioritaet"></td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <div class="submit">
                <button class="hinzufuegen" type="submit">Hinzufügen</button>
                <button class="verwerfen">Verwerfen</button>
            </div>
        </div>
    </div>
  </form>
</div>