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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../public/css/about.css">

    <link rel="shortcut icon" href="../images/favicon.ico">
    <meta name="author" content="Olivier Luethy">
    <title>Document</title>
</head>
<body>
    <!-- Navigation Part -->
    <header>
        <nav>
            <?php
            if(isset($_SESSION['loggedin']) == true){
                echo "<logo id='logo'></logo>
                <a href='home'>Home</a>
                <a class='active' href='about'>About</a>
                <a href='tackpad'>TackPad</a>
                <a href='logout'>Logout</a>
            </div>";
            }else{
                echo "<logo id='logo'></logo>
                <a href='home'>Home</a>
                <a class='active' href='about'>About</a>
                <a href='tackpad'>TackPad</a>
                <a href='login'>Login</a>
            </div>";
            }?>
        </nav>
        <h1>About</h1>
        <h2>Everything you need to know about us!</h2>
    </header>

    <main>
        <h2>About us</h2>
        <p>This website has been developed by Olivier Lüthy. He is currently in the 3th year of his 4 year apprenticeship as a comuter scientist.</p>
    </main>

</body>
</html>