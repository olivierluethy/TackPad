<?php
use Dotenv\Dotenv;
class Notiz
{
	public $db;

	public function __construct()
	{
		$this->db = connectDatabase();
	}

	// TackPad Ansicht
	/* Alle Aufgaben */
	public function tackpad()
	{
		$statement = $this->db->prepare("SELECT * FROM notes WHERE fk_usersId = :id");
		$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
		$statement->execute();
		return $statement;
	}

	/* Notiz hinzufügen */
	public function createNotiz($titel, $aufgabe, $prioritaet, $status, $datum, $id)
	{
		// Eingabedaten säubern
		$titel = htmlspecialchars($titel);
		$aufgabe = htmlspecialchars($aufgabe);
		$prioritaet = htmlspecialchars($prioritaet);
		$status = htmlspecialchars($status);
		$datum = htmlspecialchars($datum);

		// Initialisierungsvektor (IV) generieren
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

		require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig

		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
		$dotenv->load();

		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');

		// Daten verschlüsseln
		$encrypted_titel = $this->encrypt($titel, $encryption_key, $iv);
		$encrypted_aufgabe = $this->encrypt($aufgabe, $encryption_key, $iv);
		$encrypted_prioritaet = $this->encrypt($prioritaet, $encryption_key, $iv);
		$encrypted_status = $this->encrypt($status, $encryption_key, $iv);
		$encrypted_datum = $this->encrypt($datum, $encryption_key, $iv);

		// IV kodieren, damit es in der Datenbank gespeichert werden kann
		$iv_base64 = base64_encode($iv);

		// SQL Statement vorbereiten
		$statement = $this->db->prepare("INSERT INTO `notes` (titel, notiz, prioritaet, status, date_to_complete, fk_usersId, iv) VALUES (:titel, :aufgabe, :prioritaet, :status, :date_to_complete, :fk_usersId, :iv)");
		$statement->bindParam(':titel', $encrypted_titel, PDO::PARAM_STR);
		$statement->bindParam(':aufgabe', $encrypted_aufgabe, PDO::PARAM_STR);
		$statement->bindParam(':prioritaet', $encrypted_prioritaet, PDO::PARAM_STR);
		$statement->bindParam(':status', $encrypted_status, PDO::PARAM_STR);
		$statement->bindParam(':date_to_complete', $encrypted_datum, PDO::PARAM_STR);
		$statement->bindParam(':fk_usersId', $id, PDO::PARAM_INT);
		$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);

		// SQL Statement ausführen
		$statement->execute();
	}

	public function decrypt($data, $key, $iv)
	{
		$decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
		if ($decrypted === false) {
			return 'Decryption error'; // Fehlerhinweis bei Fehlschlag
		}
		return $decrypted;
	}

	public function edit($titel, $notiz, $datum, $prioritaet, $id)
	{
		$titel = htmlspecialchars($_POST['titel']);
		$notiz = htmlspecialchars($_POST['aufgabe']);
		$datum = htmlspecialchars($_POST['datum']);
		$prioritaet = htmlspecialchars($_POST['priority']);
		$id = htmlspecialchars($id);

		// Initialisierungsvektor (IV) generieren
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

		require_once __DIR__ . '/../../vendor/autoload.php';

		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();

		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');

		// IV kodieren, damit es in der Datenbank gespeichert werden kann
		$iv_base64 = base64_encode($iv);

		// Holen des IV-Werts aus der Datenbank
		$statement = $this->db->prepare('SELECT `iv`, `status` FROM `notes` WHERE `NoteId` = :id');
		$statement->bindParam(':id', $id);
		$statement->execute();
		$status_row = $statement->fetch(PDO::FETCH_ASSOC);

		$status = $this->decrypt($status_row['status'], $encryption_key, base64_decode($status_row['iv']));

		// Daten verschlüsseln
		$encrypted_titel = $this->encrypt($titel, $encryption_key, base64_decode($iv_base64));
		$encrypted_aufgabe = $this->encrypt($notiz, $encryption_key, base64_decode($iv_base64));
		$encrypted_prioritaet = $this->encrypt($prioritaet, $encryption_key, base64_decode($iv_base64));
		$encrypted_datum = $this->encrypt($datum, $encryption_key, base64_decode($iv_base64));
		$encrypted_status = $this->encrypt($status, $encryption_key, base64_decode($iv_base64));

		$statement = $this->db->prepare('UPDATE notes SET titel = :titel, notiz = :notiz, prioritaet = :prioritaet, status = :status, date_to_complete = :date_to_complete, iv = :iv WHERE NoteId = :id');
		$statement->bindParam(':titel', $encrypted_titel, PDO::PARAM_STR);
		$statement->bindParam(':notiz', $encrypted_aufgabe, PDO::PARAM_STR);
		$statement->bindParam(':prioritaet', $encrypted_prioritaet, PDO::PARAM_STR);
		$statement->bindParam(':status', $encrypted_status, PDO::PARAM_STR);
		$statement->bindParam(':date_to_complete', $encrypted_datum, PDO::PARAM_STR);
		$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
	}

	public function getInfosFromTask($id)
	{
		$id = htmlspecialchars($id);
		$statement = $this->db->prepare('SELECT * FROM notes WHERE NoteId = :id');
		$statement->bindParam(':id', $id);
		$statement->execute();
		return $statement;
	}

	// Function to extract and clean username from email
	public function getUsernameFromEmail($email)
	{
		$username = explode('@', $email)[0]; // Extract username
		if (strpos($username, '.') !== false) {
			$username = substr($username, 0, strpos($username, '.')); // Remove second part if dot exists
		}
		return ucfirst($username); // Capitalize first letter
	}

	/* Offene Aufgaben */
	public function getOpenTasks()
	{
		try {
			$statement = $this->db->prepare("SELECT * FROM notes WHERE status = 0 AND fk_usersId = :id");
			$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
			$statement->execute();
			return $statement;
		} catch (PDOException $e) {
			// Log the error
			$error_message = "Database Error: " . $e->getMessage();
			error_log($error_message);

			// You might want to handle the error further, throw an exception, or return false
			throw new Exception('Failed to fetch not late but open tasks');
		}
	}

	/* Erledigte Aufgaben */
	public function getDoneTasks()
	{
		$statement = $this->db->prepare("SELECT * FROM notes WHERE status = 1 AND fk_usersId = :id");
		$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
		$statement->execute();
		return $statement;
	}

	// Funktion zur Verschlüsselung
	private function encrypt($data, $key, $iv)
	{
		return openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
	}

	public function removeNotiz($id)
	{
		$id = htmlspecialchars($id);
		$statement = $this->db->prepare('DELETE FROM `notes` WHERE NoteId = :id');
		$statement->bindParam(':id', $id);
		$statement->execute();
	}

	public function deleteAllDone()
	{
		$statement = $this->db->prepare('DELETE FROM `notes` WHERE status = 1');
		$statement->execute();
	}

	public function deleteAllOpen()
	{
		$statement = $this->db->prepare('DELETE FROM `notes` WHERE status = 0');
		$statement->execute();
	}

	public function istErledigt($ids)
	{
		// Check if $ids is a string and convert it to an array
		if (is_string($ids)) {
			$ids = explode(',', $ids);
		}

		// Prevent SQL injection by sanitizing the input
		$cleaned_ids = array_map('intval', $ids); // Assuming NoteId is an integer field

		require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig

		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
		$dotenv->load();

		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');

		// Daten verschlüsseln
		$status = '1';
		$date_when_completed = time();
		$last_change = time();

		foreach ($cleaned_ids as $id) {
			try {
				// Holen des IV-Werts aus der Datenbank
				$statement = $this->db->prepare('SELECT `iv` FROM `notes` WHERE `NoteId` = :id');
				$statement->bindParam(':id', $id);
				$statement->execute();
				$iv_row = $statement->fetch(PDO::FETCH_ASSOC);

				// Überprüfen, ob ein IV-Wert gefunden wurde
				if (isset($iv_row['iv'])) {
					// Encrypt values using the same iv
					$encrypted_status = $this->encrypt($status, $encryption_key, base64_decode($iv_row['iv']));
					$encrypted_date_when_completed = $this->encrypt($date_when_completed, $encryption_key, base64_decode($iv_row['iv']));
					$encrypted_last_change = $this->encrypt($last_change, $encryption_key, base64_decode($iv_row['iv']));

					// Update der Datenbank
					$update_statement = $this->db->prepare('UPDATE notes SET status = :status, date_when_completed = :date_when_completed, last_change = :last_change WHERE NoteId = :id');
					$update_statement->bindParam(':status', $encrypted_status);
					$update_statement->bindParam(':date_when_completed', $encrypted_date_when_completed);
					$update_statement->bindParam(':last_change', $encrypted_last_change);
					$update_statement->bindParam(':id', $id);
					$update_statement->execute();
				}
			} catch (PDOException $e) {
				$errors[] = "Database error for Task ID $id: " . $e->getMessage();
			} catch (Exception $e) {
				$errors[] = "An unexpected error occurred for Task ID $id: " . $e->getMessage();
			}
		}
		// Rückgabe des Ergebnisses
		if (empty($errors)) {
			return ["success" => true, "updated_ids" => $updated_ids];
		} else {
			return ["success" => false, "updated_ids" => $updated_ids, "errors" => $errors];
		}
	}


	public function renewNotiz($titel, $notice, $date, $id)
	{
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

	public function undone($ids)
	{
		// Check if $ids is a string and convert it to an array
		if (is_string($ids)) {
			$ids = explode(',', $ids);
		}

		// Prevent SQL injection by sanitizing the input
		$cleaned_ids = array_map('intval', $ids); // Assuming NoteId is an integer field

		require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig

		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
		$dotenv->load();

		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');

		// Daten vorbereiten
		$status = '0';
		$date_when_completed = time();
		$last_change = time();

		// Initialisierung von Erfolgs- und Fehlermeldungen
		$updated_ids = [];
		$errors = [];

		foreach ($cleaned_ids as $id) {
			try {
				// Holen des IV-Werts aus der Datenbank
				$statement = $this->db->prepare('SELECT `iv` FROM `notes` WHERE `NoteId` = :id');
				$statement->bindParam(':id', $id);
				$statement->execute();
				$iv_row = $statement->fetch(PDO::FETCH_ASSOC);

				// Überprüfen, ob ein IV-Wert gefunden wurde
				if (isset($iv_row['iv'])) {
					// Verschlüsselung der Werte mit dem IV
					$encrypted_status = $this->encrypt($status, $encryption_key, base64_decode($iv_row['iv']));
					$encrypted_date_when_completed = $this->encrypt($date_when_completed, $encryption_key, base64_decode($iv_row['iv']));
					$encrypted_last_change = $this->encrypt($last_change, $encryption_key, base64_decode($iv_row['iv']));

					// Update der Datenbank
					$update_statement = $this->db->prepare('UPDATE notes SET status = :status, date_when_completed = :date_when_completed, last_change = :last_change WHERE NoteId = :id');
					$update_statement->bindParam(':status', $encrypted_status);
					$update_statement->bindParam(':date_when_completed', $encrypted_date_when_completed);
					$update_statement->bindParam(':last_change', $encrypted_last_change);
					$update_statement->bindParam(':id', $id);
					$update_statement->execute();

					// Überprüfen, ob die Aktualisierung erfolgreich war
					if ($update_statement->rowCount() > 0) {
						$updated_ids[] = $id;
					} else {
						$errors[] = "Task with ID $id could not be updated.";
					}
				} else {
					$errors[] = "IV not found for Task ID $id.";
				}
			} catch (PDOException $e) {
				$errors[] = "Database error for Task ID $id: " . $e->getMessage();
			} catch (Exception $e) {
				$errors[] = "An unexpected error occurred for Task ID $id: " . $e->getMessage();
			}
		}

		// Rückgabe des Ergebnisses
		if (empty($errors)) {
			return ["success" => true, "updated_ids" => $updated_ids];
		} else {
			return ["success" => false, "updated_ids" => $updated_ids, "errors" => $errors];
		}
	}

	public function delete($ids)
	{
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
		try {
			$statement->execute($cleaned_ids);

			// Check if any rows were affected (i.e., deleted)
			if ($statement->rowCount() > 0) {
				return ["success" => true, "deleted_ids" => $cleaned_ids];
			} else {
				return ["success" => false, "error" => "No tasks were deleted. The provided IDs may be invalid."];
			}
		} catch (PDOException $e) {
			// Catch any database-related errors and return them
			return ["success" => false, "error" => "Database error: " . $e->getMessage()];
		} catch (Exception $e) {
			// Catch any other errors and return them
			return ["success" => false, "error" => "An unexpected error occurred: " . $e->getMessage()];
		}
	}
}