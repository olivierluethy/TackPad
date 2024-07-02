<?php

class TackPadController
{
    /* For the TackPad Page */
    public function index() {
        // Initialize the session
        session_start();
    
        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }
    
        try {
            $notiz = new Notiz();
    
            // Get username from session email
            $username = $notiz->getUsernameFromEmail($_SESSION["email"]);
            if ($username === false) {
                throw new Exception('Username not found for email: ' . $_SESSION["email"]);
            }
    
            // Alle Aufgaben
            $alle_tasks = $notiz->tackpad()->fetchAll();
            if ($alle_tasks === false) {
                throw new Exception('Failed to fetch all tasks');
            }
    
            // Nicht zu späte und nicht erledigte Aufgaben
            $nicht_zu_spaet_offene_tasks = $notiz->getNotLateButOpenTasks()->fetchAll();
            if ($nicht_zu_spaet_offene_tasks === false) {
                throw new Exception('Failed to fetch not late but open tasks');
            }
    
            // Zu späte und nicht erledigte Aufgaben
            $zu_spaet_offene_tasks = $notiz->getLateAndOpenTasks()->fetchAll();
            if ($zu_spaet_offene_tasks === false) {
                throw new Exception('Failed to fetch late and open tasks');
            }
    
            // Erledigte Aufgaben
            $erledigte_tasks = $notiz->getDoneTasks()->fetchAll();
            if ($erledigte_tasks === false) {
                throw new Exception('Failed to fetch done tasks');
            }
    
            $anzahl_offen = count($zu_spaet_offene_tasks) + count($nicht_zu_spaet_offene_tasks);
    
            require 'app/Views/index.view.php';
    
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }
    

    /* Aufgabe hinzufügen */
    public function create(){
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = e($_POST['titel']);
            $aufgabe = e($_POST['aufgabe']);
            $status = 0;
            $datum = e($_POST['datum']);
            $prioritaet = e($_POST['priority']);

            $notiz->createNotiz($titel, $aufgabe, $status, $datum, $prioritaet, $_SESSION['id']);

            header('Location: http://localhost/TackPad/');
        }
    }

    /* Aufgabe löschen */
	public function delete(){
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }
        
        $notiz = new Notiz();

		$id = e($_GET['id']);

        $notiz->removeNotiz($id);
        
        header('Location: http://localhost/TackPad/');
	}

    public function deleteAllDone(){
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();

        $notiz->deleteAllDone();

        header('Location: http://localhost/TackPad/');

        require 'app/Views/tackpad.view.php';
    }

    public function deleteAllOpen(){
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();

        $notiz->deleteAllOpen();

        header('Location: http://localhost/TackPad/');

        require 'app/Views/tackpad.view.php';
    }

    public function deleteMultiple(){
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();

        $id = e($_GET['ids']);

        $notiz->deleteMultiple($id);

        header('Location: http://localhost/TackPad/');

        require 'app/Views/tackpad.view.php';
    }

    public function showEditPage() {
        // Initialize the session
        session_start();
    
        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }
    
        $notiz = new Notiz();
    
        // Fetch all tasks
        $alle_tasks = $notiz->tackpad()->fetchAll();
    
        require 'app/Views/index.view.php';
    }

    // Method to handle AJAX request for fetching task data
    public function getTaskData() {
        // Initialize the session
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        $notiz = new Notiz();
        $id = e($_POST['id']);

        // Fetch task data
        $taskData = $notiz->getInfosFromTask($id)->fetch();

        echo json_encode($taskData);
    }

    public function edit(){
        session_start();
        // Check if the user is logged in
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        $notiz = new Notiz();
        $id = e($_GET["id"]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = e($_POST['titel']);
            $aufgabe = e($_POST['aufgabe']);
            $datum = e($_POST['datum']);
            $prioritaet = e($_POST['priority']);

            $notiz->edit($titel, $aufgabe, $datum, $prioritaet, $id);

            header('Location: http://localhost/TackPad/');
        }
    }

    public function erledigt(){
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();

        $id = e($_GET['id']);

        $notiz->istErledigt($id);

        header('Location: http://localhost/TackPad/');
    }

    public function login(){
        // Initialize the session
        session_start();

        // Check if the user is already logged in, if yes then redirect him to index page
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            header("location: /home");
            exit;
        }

        // Unset all of the session variables
        $_SESSION = array();
        
        // Destroy the session.
        session_destroy();

        // Include config file
        include __DIR__ . '/../../core/db_config.php';

        // Use the $link variable for your query
        if ($link) {
            $stmt = mysqli_prepare($link, 'SELECT id, email, password FROM users WHERE email = ?');
            if ($stmt) {
                // Bind parameters, execute, etc.
            } else {
                echo "ERROR: Could not prepare the statement. " . mysqli_error($link);
            }
        } else {
            die("ERROR: Database connection not established.");
        }

        // Define variables and initialize with empty values
        $email = $password = "";
        $email_err = $password_err = "";

        // Processing form data when form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Check if email is empty
            if (empty(trim($_POST["email"]))) {
                $email_err = "Please enter email.";
            } else {
                $email = trim($_POST["email"]);
            }

            // Check if password is empty
            if (empty(trim($_POST["password"]))) {
                $password_err = "Please enter your password.";
            } else {
                $password = trim($_POST["password"]);
            }

            // Validate credentials
            if (empty($email_err) && empty($password_err)) {
                // Prepare a select statement
                $sql = "SELECT id, email, username, istAdmin, password FROM users WHERE email = ?";

                if ($stmt = mysqli_prepare($link, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "s", $param_email);

                    // Set parameters
                    $param_email = $email;

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {
                        // Store result
                        mysqli_stmt_store_result($stmt);

                        // Check if email exists, if yes then verify password
                        if (mysqli_stmt_num_rows($stmt) == 1) {
                            // Bind result variables
                            mysqli_stmt_bind_result($stmt, $id, $email, $username, $istAdmin, $hashed_password);
                            if (mysqli_stmt_fetch($stmt)) {
                                if (password_verify($password, $hashed_password)) {
                                    // Password is correct, so start a new session
                                    session_start();

                                    // Store data in session variables
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["id"] = $id;
                                    $_SESSION["email"] = $email;
                                    $_SESSION["username"] = $username;
                                    $_SESSION["istAdmin"] = $istAdmin;

                                    // Redirect user to index page
                                    header("location: home");
                                } else {
                                    // Display an error message if password is not valid
                                    $password_err = "The password you entered was not valid.";
                                }
                            }
                        } else {
                            // Display an error message if email doesn't exist
                            $email_err = "No account found with that email.";
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }

            // Close connection
            mysqli_close($link);
        }
        require 'app/Views/login.php';
    }

    public function logout(){
        require 'app/Views/logout.php';
    }

    public function config(){
        require 'core/db_config.php';
    }

    public function register(){
        // Include config file
        include __DIR__ . '/../../core/db_config.php';
        
        // Define variables and initialize with empty values
        $email = $password = $confirm_password = "";
        $email_err = $password_err = $confirm_password_err = "";
        
        // Processing form data when form is submitted
        if($_SERVER["REQUEST_METHOD"] == "POST"){
        
            // Validate email
            if(empty(trim($_POST["email"]))){
                $email_err = "Please enter your email adress.";
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $email_err = "Email can only contain letters.";
            } else{
                // Prepare a select statement
                $sql = "SELECT id FROM users WHERE email = ?";
                
                if($stmt = mysqli_prepare($link, $sql)){
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "s", $param_email);
                    
                    // Set parameters
                    $param_email = trim($_POST["email"]);
                    
                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        /* store result */
                        mysqli_stmt_store_result($stmt);
                        
                        if(mysqli_stmt_num_rows($stmt) == 1){
                            $email_err = "This email is already taken.";
                        } else{
                            $email = trim($_POST["email"]);
                        }
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }
            
            // Validate password
            if(empty(trim($_POST["password"]))){
                $password_err = "Please enter a password.";     
            } elseif(strlen(trim($_POST["password"])) < 6){
                $password_err = "Password must have atleast 6 characters.";
            } else{
                $password = trim($_POST["password"]);
            }
            
            // Validate confirm password
            if(empty(trim($_POST["confirm_password"]))){
                $confirm_password_err = "Please confirm password.";     
            } else{
                $confirm_password = trim($_POST["confirm_password"]);
                if(empty($password_err) && ($password != $confirm_password)){
                    $confirm_password_err = "Password did not match.";
                }
            }
            
            // Check input errors before inserting in database
            if(empty($email_err) && empty($password_err) && empty($confirm_password_err)){
                
                // Prepare an insert statement
                $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
                
                if($stmt = mysqli_prepare($link, $sql)){
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "ss", $param_email, $param_password);
                    
                    // Set parameters
                    $param_email = $email;
                    $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                    
                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        // Redirect to login page
                        header("location: /login");
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }
            // Close connection
            mysqli_close($link);
        }
        require 'app/Views/register.view.php';
    }
}