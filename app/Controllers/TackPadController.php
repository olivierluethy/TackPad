<?php
use Dotenv\Dotenv;

class TackPadController
{
    /* For the TackPad Page */
    public function index()
    {
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        try {
            $notiz = new Notiz();

            require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig

            // Laden der .env-Datei
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
            $dotenv->load();

            // Hole den Verschlüsselungsschlüssel aus der .env-Datei
            $encryption_key = getenv('ENCRYPTION_KEY');

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

            require 'app/Views/index.view.php';

        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }

    /* Aufgabe hinzufügen */
    public function create()
    {
        session_start();

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            echo json_encode(['error' => 'User not logged in']);
            exit;
        }

        $notiz = new Notiz();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $titel = htmlspecialchars($_POST['titel']);
                $aufgabe = htmlspecialchars($_POST['aufgabe']);
                $status = 0; // Standardwert
                $datum = htmlspecialchars($_POST['datum']);
                $prioritaet = htmlspecialchars($_POST['priority']);

                // Aufgabe erstellen
                $notiz->createNotiz($titel, $aufgabe, $prioritaet, $status, $datum, $_SESSION['id']);

                // Erfolgreich hinzugefügt, Rückgabe der neuen Aufgabe als JSON
                echo json_encode([
                    'success' => true,
                    'task' => [
                        'titel' => $titel,
                        'aufgabe' => $aufgabe,
                        'datum' => $datum,
                        'prioritaet' => $prioritaet,
                    ]
                ]);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'Invalid request method']);
        }
    }

    /* Aufgabe löschen */
    public function delete()
    {
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("Content-Type: application/json");
            echo json_encode(["error" => "Not logged in"]);
            exit;
        }

        $notiz = new Notiz();
        $ids = e($_GET['id']);

        $result = $notiz->delete($ids);

        header("Content-Type: application/json");
        if ($result['success']) {
            echo json_encode(["success" => true, "ids" => $result['deleted_ids']]);
        } else {
            echo json_encode(["success" => false, "error" => $result['error']]);
        }
    }

    public function deleteAllDone()
    {
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();

        $notiz->deleteAllDone();

        header('Location: home');

        require 'app/Views/tackpad.view.php';
    }

    public function deleteAllOpen()
    {
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();

        $notiz->deleteAllOpen();

        header('Location: home');

        require 'app/Views/tackpad.view.php';
    }

    public function showEditPage()
    {
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
    public function getTaskData()
    {
        // Initialize the session
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig

        // Laden der .env-Datei
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
        $dotenv->load();

        // Hole den Verschlüsselungsschlüssel aus der .env-Datei
        $encryption_key = getenv('ENCRYPTION_KEY');

        $notiz = new Notiz();
        $id = e($_POST['id']);

        // Fetch task data
        $taskData = $notiz->getInfosFromTask($id)->fetch();

        // Entschlüsselung der Daten
        $decrypted_title = $notiz->decrypt($taskData['titel'], $encryption_key, base64_decode($taskData['iv']));
        $decrypted_task = $notiz->decrypt($taskData['notiz'], $encryption_key, base64_decode($taskData['iv']));
        $decrypted_date = $notiz->decrypt($taskData['date_to_complete'], $encryption_key, base64_decode($taskData['iv']));
        $decrypted_priority = $notiz->decrypt($taskData['prioritaet'], $encryption_key, base64_decode($taskData['iv']));

        // Erstellen eines entschlüsselten Datensatzes
        $decryptedTaskData = [
            'titel' => $decrypted_title,
            'notiz' => $decrypted_task,
            'date_to_complete' => $decrypted_date,
            'priority' => $decrypted_priority,
            'id' => $id
        ];

        // JSON-Encode der entschlüsselten Daten
        echo json_encode($decryptedTaskData);
    }

    public function edit()
    {
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

            header('Location: home');
        }
    }

    public function erledigt()
    {
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();
        $ids = e($_GET['id']);
        $result = $notiz->istErledigt($ids);

        header("Content-Type: application/json");
        if ($result['success']) {
            echo json_encode(["success" => true, "ids" => $result['updated_ids']]); // Konsistentes Rückgabeformat
        } else {
            echo json_encode(["success" => false, "error" => implode(", ", $result['errors'])]);
        }
    }

    public function unerledigt()
    {
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();
        $ids = e($_GET['id']);
        $result = $notiz->undone($ids);

        header("Content-Type: application/json");
        if ($result['success']) {
            echo json_encode(["success" => true, "ids" => $result['updated_ids']]); // Hier korrigieren
        } else {
            echo json_encode(["success" => false, "error" => implode(", ", $result['errors'])]); // Fehler ausgeben
        }
    }

    public function login()
    {
        // Initialize the session
        session_start();

        // Check if the user is already logged in, if yes then redirect to home page
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            header("location: home");
            exit;
        }

        // Include config file
        include __DIR__ . '/../../core/db_config.php';

        // Define variables and initialize with empty values
        $email = $password = "";
        $email_err = $password_err = "";

        // Processing form data when form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check if email is empty
            if (empty(trim($_POST["email"]))) {
                $email_err = "Bitte geben Sie eine E-Mail-Adresse ein.";
            } else {
                $email = strtolower(trim($_POST["email"]));
            }

            // Check if password is empty
            if (empty(trim($_POST["password"]))) {
                $password_err = "Bitte geben Sie Ihr Passwort ein.";
            } else {
                $password = trim($_POST["password"]);
            }

            // Validate credentials
            if (empty($email_err) && empty($password_err)) {
                // Prepare a select statement to get salt and email hash
                $sql = "SELECT id, email, password, salt FROM users";

                if ($result = mysqli_query($link, $sql)) {
                    $found = false;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $generated_hash = hash_hmac('sha256', $email, $row['salt']);

                        if ($generated_hash === $row['email']) {
                            $found = true;
                            if (password_verify($password, $row['password'])) {
                                // Password is correct, start a new session
                                session_start();

                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $row['id'];
                                $_SESSION["email"] = $email;
                                $_SESSION["email_hash"] = $row['email'];

                                // Redirect user to home page
                                header("location: home");
                                exit();
                            } else {
                                // Display an error message if password is not valid
                                $password_err = "Das Passwort ist nicht gültig.";
                            }
                        }
                    }
                    if (!$found) {
                        // Display an error message if email doesn't exist
                        $email_err = "Kein Konto mit dieser E-Mail-Adresse gefunden.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Free result set
                mysqli_free_result($result);
            }

            // Close connection
            mysqli_close($link);
        }

        // Load login view with appropriate error messages
        require 'app/Views/login.php';
    }

    public function logout()
    {
        require 'app/Views/logout.php';
    }

    public function config()
    {
        require 'core/db_config.php';
    }

    public function register()
    {
        // Include config file
        include __DIR__ . '/../../core/db_config.php';

        // Define variables and initialize with empty values
        $email = $password = $confirm_password = "";
        $email_err = $password_err = $confirm_password_err = "";

        // Processing form data when form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Validate email
            if (empty(trim($_POST['email']))) {
                $email_err = "Bitte geben Sie eine E-Mail-Adresse ein.";
            } else {
                $email = strtolower(trim($_POST['email'])); // Email to lowercase
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email_err = "Bitte geben Sie eine gültige E-Mail-Adresse ein.";
                } else {
                    // Prepare a select statement to check if the email already exists
                    $sql = "SELECT email, salt FROM users";
                    if ($result = mysqli_query($link, $sql)) {
                        $email_exists = false;
                        while ($row = mysqli_fetch_assoc($result)) {
                            $stored_email_hash = $row['email'];
                            $stored_salt = $row['salt'];
                            $check_email_hash = hash_hmac('sha256', $email, $stored_salt);
                            if ($stored_email_hash === $check_email_hash) {
                                $email_exists = true;
                                break;
                            }
                        }
                        mysqli_free_result($result);

                        if ($email_exists) {
                            $email_err = "Diese E-Mail-Adresse ist bereits vergeben.";
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                }
            }

            // Validate password
            if (empty(trim($_POST["password"]))) {
                $password_err = "Bitte geben Sie ein Passwort ein.";
            } elseif (strlen(trim($_POST["password"])) < 6) {
                $password_err = "Das Passwort muss mindestens 6 Zeichen haben.";
            } else {
                $password = trim($_POST["password"]);
            }

            // Validate confirm password
            if (empty(trim($_POST["confirm_password"]))) {
                $confirm_password_err = "Bitte bestätigen Sie das Passwort.";
            } else {
                $confirm_password = trim($_POST["confirm_password"]);
                if (empty($password_err) && ($password != $confirm_password)) {
                    $confirm_password_err = "Die Passwörter stimmen nicht überein.";
                }
            }

            // Check input errors before inserting in database
            if (empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
                // Generate salt
                $salt = bin2hex(random_bytes(16)); // 16 bytes = 128 bits
                // Hash the email with the salt
                $email_hash = hash_hmac('sha256', $email, $salt);
                // Hash the password
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

                // Prepare an insert statement
                $sql = "INSERT INTO users (email, password, salt) VALUES (?, ?, ?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sss", $email_hash, $param_password, $salt);
                    if (mysqli_stmt_execute($stmt)) {
                        // Redirect to login page
                        header("location: login");
                        exit();
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    mysqli_stmt_close($stmt);
                }
            }

            // Close connection
            mysqli_close($link);
        }

        // Generate a new CSRF token
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        require 'app/Views/register.view.php';
    }
}