<?php
class Notiz
{
	public $db;

	public function __construct()
	{
		$this->db = connectDatabase();
	}

	// TackPad Ansicht
	/* Alle Aufgaben */
	public function tackpad(){
		$statement = $this->db->prepare("SELECT * FROM notes WHERE fk_usersId = :id");
		$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
		$statement->execute();
		return $statement;
	}

	public function edit($titel, $notiz, $datum, $prioritaet, $id){
		$titel = htmlspecialchars($_POST['titel']);
		$notiz = htmlspecialchars($_POST['aufgabe']);
		$datum = htmlspecialchars($_POST['datum']);
		$prioritaet = htmlspecialchars($_POST['priority']);
		$id = htmlspecialchars($id);

		$statement = $this->db->prepare('UPDATE notes SET titel = :titel, notiz = :notiz, date_to_complete = :date_to_complete, prioritaet = :prioritaet WHERE NoteId = :id');
		$statement->bindParam(':titel', $titel, PDO::PARAM_STR);
		$statement->bindParam(':notiz', $notiz, PDO::PARAM_STR);
		$statement->bindParam(':date_to_complete', $datum, PDO::PARAM_STR);
		$statement->bindParam(':prioritaet', $prioritaet, PDO::PARAM_STR);
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();
	}

	public function getInfosFromTask($id){
		$id = htmlspecialchars($id);
		$statement = $this->db->prepare('SELECT * FROM notes WHERE NoteId = :id');
		$statement->bindParam(':id', $id);
        $statement->execute();
		return $statement;
	}

	// Function to extract and clean username from email
    public function getUsernameFromEmail($email) {
        $username = explode('@', $email)[0]; // Extract username
        if (strpos($username, '.') !== false) {
            $username = substr($username, 0, strpos($username, '.')); // Remove second part if dot exists
        }
        return ucfirst($username); // Capitalize first letter
    }

	/* Nicht zu späte und nicht erledigte Aufgaben */
	public function getNotLateButOpenTasks(){
		$statement = $this->db->prepare("SELECT * FROM notes WHERE status = 0 AND fk_usersId = :id AND NOT date_to_complete + INTERVAL 1 DAY < NOW() ORDER BY prioritaet, NOT date_to_complete + INTERVAL 1 DAY < NOW()");
		$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
		$statement->execute();
		return $statement;
	}

	/* Zu späte und nicht erledigte Aufgaben */
	public function getLateAndOpenTasks(){
		$statement = $this->db->prepare("SELECT * FROM notes WHERE status = 0 AND fk_usersId = :id AND date_to_complete + INTERVAL 1 DAY < NOW() ORDER BY prioritaet, date_to_complete + INTERVAL 1 DAY < NOW()");
        $statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
        $statement->execute();
		return $statement;
	}

	/* Erledigte Aufgaben */
	public function getDoneTasks(){
        $statement = $this->db->prepare("SELECT * FROM notes WHERE status = 1 AND fk_usersId = :id");
        $statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
        $statement->execute();
        return $statement;
	}

	/* Notiz hinzufügen */
	public function createNotiz($titel, $aufgabe, $status, $datum, $prioritaet, $id){
		$titel = htmlspecialchars($_POST['titel']);
		$aufgabe = htmlspecialchars($_POST['aufgabe']);
		$datum = htmlspecialchars($_POST['datum']);
		$prioritaet = htmlspecialchars($_POST['priority']);

		$statement = $this->db->prepare("INSERT INTO `notes` (titel, notiz, status, date_to_complete, prioritaet, fk_usersId) VALUES (:titel, :aufgabe, :status, :date_to_complete, :prioritaet, :fk_usersId)");
		$statement->bindParam(':titel', $titel, PDO::PARAM_STR);
		$statement->bindParam(':aufgabe', $aufgabe, PDO::PARAM_STR);
		$statement->bindParam(':status', $status, PDO::PARAM_STR);
		$statement->bindParam(':prioritaet', $prioritaet, PDO::PARAM_STR);
		$statement->bindParam(':date_to_complete', $datum, PDO::PARAM_STR);
		$statement->bindParam(':fk_usersId', $id, PDO::PARAM_STR);
		$statement->execute();
	}

	public function removeNotiz($id){
		$id = htmlspecialchars($id);
		$statement = $this->db->prepare('DELETE FROM `notes` WHERE NoteId = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
	}

	public function deleteAllDone(){
		$statement = $this->db->prepare('DELETE FROM `notes` WHERE status = 1');
        $statement->execute();
	}

	public function deleteAllOpen(){
		$statement = $this->db->prepare('DELETE FROM `notes` WHERE status = 0');
        $statement->execute();
	}

	public function istErledigt($ids) {
		// Check if $ids is a string and convert it to an array
		if (is_string($ids)) {
			$ids = explode(',', $ids);
		}
	
		// Prevent SQL injection by sanitizing the input
		$cleaned_ids = array_map('intval', $ids); // Assuming NoteId is an integer field
	
		// Create a placeholder string for the SQL query
		$placeholders = implode(',', array_fill(0, count($cleaned_ids), '?'));
	
		// Prepare the SQL statement
		$sql = "UPDATE `notes` 
				SET `status` = 1, 
					`date_when_completed` = CURRENT_TIMESTAMP, 
					`last_change` = CURRENT_TIMESTAMP 
				WHERE `NoteId` IN ($placeholders)";
		
		$statement = $this->db->prepare($sql);
	
		// Execute the statement with the cleaned IDs as parameters
		$statement->execute($cleaned_ids);
	}	

	public function renewNotiz($titel, $notice, $date, $id){
		$titel = htmlspecialchars($_POST['title']);
		$notice = htmlspecialchars($_POST['notice']);
		$date = htmlspecialchars($_POST['date']);
		$id = htmlspecialchars($id);

		$statement = $this->db->prepare('UPDATE notes SET titel = :titel, notiz = :notiz, date_to_complete = :date WHERE NoteId = :id');
		$statement->bindParam(':titel', $titel);
		$statement->bindParam(':notiz', $notice);
		$statement->bindParam(':date', $date);
		$statement->bindParam(':id', $id);
		$statement->execute();
	}

	public function istnichtmehrerledigt($id){
		$id = htmlspecialchars($id);
		$statement = $this->db->prepare('UPDATE `notes` SET status = 0 WHERE NoteId = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
	}

	public function delete($ids) {
		// Check if $ids is a string and convert it to an array
		if (is_string($ids)) {
			$ids = explode(',', $ids);
		}
	
		// Prevent SQL injection by sanitizing the input
		$cleaned_ids = array_map('htmlspecialchars', $ids);
	
		// Create a placeholder string for the SQL query
		$placeholders = implode(',', array_fill(0, count($cleaned_ids), '?'));
	
		// Prepare the SQL query
		$statement = $this->db->prepare("DELETE FROM notes WHERE NoteId IN ($placeholders)");
	
		// Execute the query with the sanitized IDs as parameters
		$statement->execute($cleaned_ids);
	}	
}