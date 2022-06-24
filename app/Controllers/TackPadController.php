<?php

class TackPadController
{
    /* For the Home Page */
	public function index()
	{	
		require 'app/Views/welcome.view.php';
	}

    /* For the About Page */
    public function about(){
        require 'app/Views/about.view.php';
    }

    /* For the TackPad Page */
    public function tackpad(){
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

        require 'app/Views/tackpad.view.php';
    }

    /* Aufgabe hinzufügen */
    public function create(){
        $notiz = new Notiz();

        // Initialize the session
        session_start();

        $title = '';
        $pdo = connectDatabase();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $notice = $_POST['notice'];
            $status = 0;
            $date = $_POST['date'];

            $notiz->createNotiz($titel, $notice, $status, $date, $_SESSION['id']);

            header('Location: http://localhost/TackPad/tackpad');
        }
    }

    /* Aufgabe löschen */
	public function delete(){
        $notiz = new Notiz();

        // Initialize the session
        session_start();
        
		$id = $_GET['id'];

        $title = '';
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $notiz->removeNotiz($id);
        
        header('Location: http://localhost/TackPad/tackpad');

        require 'app/Views/tackpad.view.php';
	}

    /* Aufgabe bearbeiten */
    public function update(){
        $notiz = new Notiz();

        // Initialize the session
        session_start();

        $id = $_GET['id'];

        $title = '';
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = $_POST['title'];
            $notice = $_POST['notice'];
            $date = $_POST['date'];

            $notiz->renewNotiz($titel, $notice, $date, $id);

            header('Location: http://localhost/TackPad/tackpad');
        }else{
            $statement = $pdo->prepare('SELECT * FROM notes WHERE NoteId = :id');
            $statement->bindParam(':id', $id);
            $statement->execute();
            $notiz = $statement->fetchAll();
        }
        require 'app/Views/editNotice.view.php';
    }

    public function erledigt(){
        $notiz = new Notiz();

        // Initialize the session
        session_start();

        $id = $_GET['id'];

        $title = '';
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $notiz->istErledigt($id);

        header('Location: http://localhost/TackPad/tackpad');
    }

    public function nichtmehrerledigt(){
        $notiz = new Notiz();

        // Initialize the session
        session_start();

        $id = $_GET['id'];

        $title = '';
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $notiz->istnichtmehrerledigt($id);

        header('Location: http://localhost/TackPad/tackpad');
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

    public function deleteAll(){
        $notiz = new Notiz();

        // Initialize the session
        session_start();

        $id = $_SESSION['id'];

        $title = '';
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $notiz->deleteEvery($id);
        
        header('Location: http://localhost/TackPad/tackpad');

        require 'app/Views/tackpad.view.php';
    }
}