<?php
class Notiz
{
	public $db;

	public function __construct()
	{
		$this->db = connectDatabase();
	}

	// TackPad Ansicht
	public function tackpad(){
		/* Alle Aufgaben */
		$statement = $this->db->prepare("SELECT * FROM notes WHERE fk_usersId = :id");
		$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
		$statement->execute();
		return $statement;
	}

	public function getNotLateButOpenTasks(){
		/* Nicht zu späte und nicht erledigte Aufgaben */
		$statement = $this->db->prepare("SELECT * FROM notes WHERE status = 0 AND fk_usersId = :id AND NOT date_to_complete + INTERVAL 1 DAY < NOW()");
		$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
		$statement->execute();
		return $statement;
	}

	public function getLateAndOpenTasks(){
		/* Zu späte und nicht erledigte Aufgaben */
		$statement = $this->db->prepare("SELECT * FROM notes WHERE status = 0 AND fk_usersId = :id AND date_to_complete + INTERVAL 1 DAY < NOW()");
        $statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
        $statement->execute();
		return $statement;
	}

	public function getDoneTasks(){
		/* Erledigte Aufgaben */
        $statement = $this->db->prepare("SELECT * FROM notes WHERE status = 1 AND fk_usersId = :id");
        $statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
        $statement->execute();
        return $statement;
	}

	/* Notiz hinzufügen */
	public function createNotiz($titel, $notice, $status, $date, $id){
		$title = htmlspecialchars($_POST['title']);
		$notice = htmlspecialchars($_POST['notice']);
		$date = htmlspecialchars($_POST['date']);

		$statement = $this->db->prepare("INSERT INTO `notes` (titel, notiz, status, date_to_complete, fk_usersId) VALUES (:titel, :notiz, :status, :date_to_complete, :fk_usersId)");
		$statement->bindParam(':titel', $title, PDO::PARAM_STR);
		$statement->bindParam(':notiz', $notice, PDO::PARAM_STR);
		$statement->bindParam(':status', $status, PDO::PARAM_STR);
		$statement->bindParam(':date_to_complete', $date, PDO::PARAM_STR);
		$statement->bindParam(':fk_usersId', $id, PDO::PARAM_STR);
		$statement->execute();
	}

	public function removeNotiz($id){
		$statement = $this->db->prepare('DELETE FROM `notes` WHERE NoteID = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
	}

	public function renewNotiz($titel, $notice, $date, $id){
		$titel = htmlspecialchars($_POST['title']);
		$notice = htmlspecialchars($_POST['notice']);
		$date = htmlspecialchars($_POST['date']);

		$statement = $this->db->prepare('UPDATE notes SET titel = :titel, notiz = :notiz, date_to_complete = :date WHERE NoteId = :id');
		$statement->bindParam(':titel', $titel);
		$statement->bindParam(':notiz', $notice);
		$statement->bindParam(':date', $date);
		$statement->bindParam(':id', $id);
		$statement->execute();
	}

	public function istErledigt($id){
		$statement = $this->db->prepare('UPDATE `notes` SET status = 1 WHERE NoteId = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
	}

	public function istnichtmehrerledigt($id){
		$statement = $this->db->prepare('UPDATE `notes` SET status = 0 WHERE NoteId = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
	}

	public function deleteEvery($id){
		$statement = $this->db->prepare('DELETE FROM notes WHERE fk_usersId = :id');
		$statement->bindParam(':id', $id);
        $statement->execute();
	}
}