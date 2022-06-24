<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/favicon.ico">
    <meta name="author" content="Olivier Luethy">
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <!-- Importing font from google -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="public/css/welcome.css">
    <link rel="stylesheet" type="text/css" href="public/css/nav.css">
    <title>Website To TackPad</title>
</head>

<body>
    <!-- Navigation Part -->
    <header>
        <?php
            include("nav.view.php");
        ?>
        <h1>TackPad</h1>
        <h2>The best way for taking notes!</h2>
    </header>
    <main>
        <h2>Notizen</h2>
        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Reiciendis ad ullam odit, corrupti dolorum sit
            blanditiis doloribus. Eligendi, ab, quis vitae placeat eum, corporis facilis inventore optio possimus et
            quia.</p>
    </main>
</body>

</html>