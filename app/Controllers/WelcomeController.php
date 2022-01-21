<?php

class WelcomeController
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

        /* Alle Aufgaben */
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement = $pdo->prepare("SELECT * FROM notes WHERE fk_usersId = :id");
        $statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
        $statement->execute();
        $alle_tasks = $statement->fetchAll();

        /* Nicht zu späte und nicht erledigte Aufgaben */
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement = $pdo->prepare("SELECT * FROM notes WHERE status = 0 AND fk_usersId = :id AND NOT date_to_complete + INTERVAL 1 DAY < NOW()");
        $statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
        $statement->execute();
        $nicht_zu_spaet_offene_tasks = $statement->fetchAll();

        /* Zu späte und nicht erledigte Aufgaben */
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement = $pdo->prepare("SELECT * FROM notes WHERE status = 0 AND fk_usersId = :id AND date_to_complete + INTERVAL 1 DAY < NOW()");
        $statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
        $statement->execute();
        $zu_spaet_offene_tasks = $statement->fetchAll();

        /* Erledigte Aufgaben */
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement = $pdo->prepare("SELECT * FROM notes WHERE status = 1 AND fk_usersId = :id");
        $statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
        $statement->execute();
        $erledigte_tasks = $statement->fetchAll();

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

            header('Location: http://localhost/TackPad/notes/tackpad'); // Besser: header('Location: http://localhost/deinProjekt/task);
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
        
        header('Location: http://localhost/TackPad/notes/tackpad');

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

            header('Location: http://localhost/TackPad/notes/tackpad');
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

        header('Location: http://localhost/TackPad/notes/tackpad');
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

        header('Location: http://localhost/TackPad/notes/tackpad');
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
        
        header('Location: http://localhost/TackPad/notes/tackpad');

        require 'app/Views/tackpad.view.php';
    }
}

