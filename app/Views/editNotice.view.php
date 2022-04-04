<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Notiz bearbeiten</title>
    <link rel="shortcut icon" href="../assets/favicon.ico">
</head>

<body>
    <form action="edit?id=<?= $notiz[0][0] ?>" method="post">
        <fieldset>
            <legend>Notiz Daten</legend>
            <label for="titel">Titel:</label>
            <input type="text" name="title" id="titel" value="<?= $notiz[0][1] ?>"><br><br>

            <label for="notiz">Aufgabe:</label>
            <input type="text" name="notice" id="aufgabe" value="<?= $notiz[0][2] ?>"><br><br>

            <label for="notiz">Datum verändern:</label>
            <input type="date" name="date" id="date" value="<?= $notiz[0][4] ?>"><br><br>
        </fieldset>
        <button type="submit" name="form-submit">Notiz bearbeiten</button>
    </form>
    <p id="warningTitel"></p>
    <p id="warningAufgabe"></p>
    <p id="warningDate"></p>
    <script src="../public/js/clientSideValidationNotice.js"></script>
</body>

</html>