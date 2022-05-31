<?php

class TackPadController
{
    /* For the TackPad Page */
    public function index(){
        // Initialize the session
        session_start();

        $notiz = new Notiz();

        /* Alle Aufgaben */
        $alle_tasks = $notiz -> tackpad();
        $alle_tasks = $alle_tasks -> fetchAll();

        /* Nicht zu späte und nicht erledigte Aufgaben */
        $nicht_zu_spaet_offene_tasks = $notiz -> getNotLateButOpenTasks();
        $nicht_zu_spaet_offene_tasks = $nicht_zu_spaet_offene_tasks -> fetchAll();

        /* Zu späte und nicht erledigte Aufgaben */
        $zu_spaet_offene_tasks = $notiz -> getLateAndOpenTasks();
        $zu_spaet_offene_tasks = $zu_spaet_offene_tasks -> fetchAll();

        /* Erledigte Aufgaben */
        $erledigte_tasks = $notiz -> getDoneTasks();
        $erledigte_tasks = $erledigte_tasks -> fetchAll();

        require 'app/Views/index.view.php';
    }

    /* Aufgabe hinzufügen */
    public function create(){
        $notiz = new Notiz();

        // Initialize the session
        session_start();

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
        $notiz = new Notiz();

        // Initialize the session
        session_start();
        
		$id = $_GET['id'];

        $notiz->removeNotiz($id);
        
        header('Location: http://localhost/TackPad/');

        require 'app/Views/tackpad.view.php';
	}

    public function showEditPage(){
        $notiz = new Notiz();

        // Initialize the session
        session_start();

        $id = $_GET['id'];

        /* Infos von Aufgabe */
        $getInfosFromTask = $notiz -> getInfosFromTask($id);
        $getInfosFromTask = $getInfosFromTask -> fetchAll();

        require 'app/Views/index.view.php';
        require 'app/Views/editNote.view.php';
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