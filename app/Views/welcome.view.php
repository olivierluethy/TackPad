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
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,669;0,700;0,800;0,900;1,400;1,500;1,600;1,669;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

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