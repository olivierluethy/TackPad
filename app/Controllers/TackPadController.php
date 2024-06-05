<?php

class TackPadController
{
    /* For the TackPad Page */
    public function index(){
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();

        /* Alle Aufgaben */
        $alle_tasks = $notiz -> tackpad()-> fetchAll();

        /* Nicht zu späte und nicht erledigte Aufgaben */
        $nicht_zu_spaet_offene_tasks = $notiz -> getNotLateButOpenTasks()-> fetchAll();

        /* Zu späte und nicht erledigte Aufgaben */
        $zu_spaet_offene_tasks = $notiz -> getLateAndOpenTasks() -> fetchAll();

        /* Erledigte Aufgaben */
        $erledigte_tasks = $notiz -> getDoneTasks()-> fetchAll();

        $anzahl_offen = count($zu_spaet_offene_tasks) + count($nicht_zu_spaet_offene_tasks);

        require 'app/Views/index.view.php';
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

        $title = '';
        $pdo = connectDatabase();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = $_POST['titel'];
            $aufgabe = $_POST['aufgabe'];
            $status = 0;
            $datum = $_POST['datum'];
            $prioritaet = $_POST['prioritaet'];

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

		$id = $_GET['id'];

        $notiz->removeNotiz($id);
        
        header('Location: http://localhost/TackPad/');
	}

    public function deleteAllNichtZuSpaetOffeneTasks(){
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();

        $notiz->removeAllNichtZuSpaetOffeneTasks();

        header('Location: http://localhost/TackPad/');

        require 'app/Views/tackpad.view.php';
    }

    public function showEditPage(){
        // Initialize the session
        session_start();

        // Check if the user is logged in, if not then redirect to login page
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();

        $id = $_GET['id'];

        /* Infos von Aufgabe */
        $getInfosFromTask = $notiz -> getInfosFromTask($id)-> fetchAll();

        require 'app/Views/index.view.php';
        require 'app/Views/editNote.view.php';
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

        $id = $_GET['id'];

        $notiz->istErledigt($id);

        header('Location: http://localhost/TackPad/');
    }

    public function login(){
        require 'app/Views/login.php';
    }

    public function logout(){
        require 'app/Views/logout.php';
    }

    public function config(){
        require 'app/Views/config.php';
    }

    public function register(){
        require 'app/Views/register.view.php';
    }
}