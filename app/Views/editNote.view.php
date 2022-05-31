<!-- The Modal --> <!-- Sobald das Modal hinzugefügt wird, funktioniert das ganze JavaScript nicht mehr -->
<div id="editModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <form action="" method="post">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Notiz bearbeiten</h2>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td><label for="fname">Titel:</label></td>
                        <td><input type="text" id="fname" name="titel" value="<?= $getInfosFromTask[0][1]?>"></td>
                    </tr>
                    <tr>
                        <td><label for="lname">Aufgabe:</label></td>
                        <td><input type="text" id="lname" name="aufgabe" value="<?= $getInfosFromTask[0][2]?>"></td>
                    <tr>
                        <td><label for="lname">Datum:</label></td>
                        <td><input type="date" id="lname" name="date" value="<?= $getInfosFromTask[0][3]?>"></td>
                    </tr>
                    <tr>
                        <td><label for="lname">Priorität:</label></td>
                        <td><input type="text" id="lname" name="prioritaet" value="<?= $getInfosFromTask[0][4]?>"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <div class="submit">
                    <button class="hinzufuegen" type="submit">Hinzufügen</button>
                    <button class="verwerfen">Verwerfen</button>
                </div>
            </div>
        </form>
    </div>
</div>