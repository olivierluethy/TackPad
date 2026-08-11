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
	public function createNotiz($titel, $aufgabe, $prioritaet, $status, $datum, $id, $last_change = null)
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

		// last_change is stored raw (not encrypted) to match the list view's
		// read path; default to "now" when the caller does not supply one.
		$last_change = $last_change ?? date('Y-m-d H:i:s');

		// SQL Statement vorbereiten
		$statement = $this->db->prepare("INSERT INTO `notes` (titel, notiz, prioritaet, status, date_to_complete, last_change, fk_usersId, iv) VALUES (:titel, :aufgabe, :prioritaet, :status, :date_to_complete, :last_change, :fk_usersId, :iv)");
		$statement->bindParam(':titel', $encrypted_titel, PDO::PARAM_STR);
		$statement->bindParam(':aufgabe', $encrypted_aufgabe, PDO::PARAM_STR);
		$statement->bindParam(':prioritaet', $encrypted_prioritaet, PDO::PARAM_STR);
		$statement->bindParam(':status', $encrypted_status, PDO::PARAM_STR);
		$statement->bindParam(':date_to_complete', $encrypted_datum, PDO::PARAM_STR);
		$statement->bindParam(':last_change', $last_change, PDO::PARAM_STR);
		$statement->bindParam(':fk_usersId', $id, PDO::PARAM_INT);
		$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);

		// SQL Statement ausführen
		$statement->execute();

		// Return the new NoteId so callers can render the row without a reload.
		return (int) $this->db->lastInsertId();
	}

	public function decrypt($data, $key, $iv)
	{
		$decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
		if ($decrypted === false) {
			return 'Decryption error'; // Fehlerhinweis bei Fehlschlag
		}
		return $decrypted;
	}

	/**
	 * Edits a task's editable fields (title, note, due date, priority) and
	 * stamps last_change. Reuses the row's EXISTING IV so the untouched
	 * encrypted fields (status, date_when_completed) stay decryptable — the
	 * previous version generated a fresh IV but only re-encrypted some fields,
	 * which silently corrupted the rest. Scoped by $userId to prevent editing
	 * another user's task (IDOR), mirroring updateDate().
	 *
	 * @return bool True when a row was updated, false if not found / not owned.
	 */
	public function edit($titel, $notiz, $datum, $prioritaet, $id, $userId, $last_change = null)
	{
		$titel = htmlspecialchars($titel);
		$notiz = htmlspecialchars($notiz);
		$datum = htmlspecialchars($datum);
		$prioritaet = htmlspecialchars($prioritaet);
		$id = (int) $id;
		$userId = (int) $userId;
		$last_change = $last_change ?? date('Y-m-d H:i:s');

		require_once __DIR__ . '/../../vendor/autoload.php';

		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->safeLoad();

		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');

		// Holen des IV-Werts und Besitzers aus der Datenbank
		$statement = $this->db->prepare('SELECT `iv`, `fk_usersId` FROM `notes` WHERE `NoteId` = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
		$row = $statement->fetch(PDO::FETCH_ASSOC);

		if (!$row || (int) $row['fk_usersId'] !== $userId) {
			return false;
		}

		// Reuse the existing IV for every re-encrypted field.
		$iv = base64_decode($row['iv']);
		$encrypted_titel = $this->encrypt($titel, $encryption_key, $iv);
		$encrypted_aufgabe = $this->encrypt($notiz, $encryption_key, $iv);
		$encrypted_prioritaet = $this->encrypt($prioritaet, $encryption_key, $iv);
		$encrypted_datum = $this->encrypt($datum, $encryption_key, $iv);

		// last_change stored raw to match the list view's read path. status, iv
		// and date_when_completed are intentionally left untouched.
		$statement = $this->db->prepare('UPDATE notes SET titel = :titel, notiz = :notiz, prioritaet = :prioritaet, date_to_complete = :date_to_complete, last_change = :last_change WHERE NoteId = :id AND fk_usersId = :userId');
		$statement->bindParam(':titel', $encrypted_titel, PDO::PARAM_STR);
		$statement->bindParam(':notiz', $encrypted_aufgabe, PDO::PARAM_STR);
		$statement->bindParam(':prioritaet', $encrypted_prioritaet, PDO::PARAM_STR);
		$statement->bindParam(':date_to_complete', $encrypted_datum, PDO::PARAM_STR);
		$statement->bindParam(':last_change', $last_change, PDO::PARAM_STR);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->bindParam(':userId', $userId, PDO::PARAM_INT);
		$statement->execute();

		return true;
	}

	/**
	 * Reschedules a task by re-encrypting only its due date with the row's
	 * existing IV. Verifies the task belongs to $userId before writing, which
	 * prevents one user from rescheduling another user's task (IDOR).
	 *
	 * @param int    $id       NoteId to update.
	 * @param string $newDate  Wall-clock date ("Y-m-d") or datetime ("Y-m-d H:i:s").
	 * @param int    $userId   Owning user (session id).
	 * @return bool            True on success, false if not found / not owned.
	 */
	public function updateDate($id, $newDate, $userId)
	{
		$id = (int) $id;
		$userId = (int) $userId;

		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->safeLoad();
		$encryption_key = getenv('ENCRYPTION_KEY');

		// Fetch the row's IV and owner; reuse the same IV so the other
		// (untouched) encrypted fields in this row stay decryptable.
		$statement = $this->db->prepare('SELECT `iv`, `fk_usersId` FROM `notes` WHERE `NoteId` = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
		$row = $statement->fetch(PDO::FETCH_ASSOC);

		if (!$row || (int) $row['fk_usersId'] !== $userId) {
			return false;
		}

		$iv = base64_decode($row['iv']);
		$encrypted_date = $this->encrypt($newDate, $encryption_key, $iv);
		$last_change = date('Y-m-d H:i:s'); // stored raw to match the view's read path

		$update = $this->db->prepare('UPDATE notes SET date_to_complete = :date, last_change = :last_change WHERE NoteId = :id AND fk_usersId = :userId');
		$update->bindParam(':date', $encrypted_date, PDO::PARAM_STR);
		$update->bindParam(':last_change', $last_change, PDO::PARAM_STR);
		$update->bindParam(':id', $id, PDO::PARAM_INT);
		$update->bindParam(':userId', $userId, PDO::PARAM_INT);
		$update->execute();

		return true;
	}

	/**
	 * Resolves a plaintext email to a user id. Emails are stored as a salted
	 * HMAC (never in clear), so the only way to match is to recompute the HMAC
	 * with each user's salt — the same scheme used by login/register.
	 *
	 * @param string $email The plaintext email to look up.
	 * @return int|null The matching user id, or null if no user matches.
	 */
	public function findUserIdByEmail($email)
	{
		$email = strtolower(trim($email));
		if ($email === '') {
			return null;
		}

		$statement = $this->db->query('SELECT id, email, salt FROM users');
		while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			if (hash_equals($row['email'], hash_hmac('sha256', $email, $row['salt']))) {
				return (int) $row['id'];
			}
		}
		return null;
	}

	/**
	 * Shares one of the owner's tasks with another registered user by copying
	 * it into that user's account and flagging the original as shared.
	 *
	 * The copy is a fresh row (its own IV) so the two users' tasks stay fully
	 * independent — editing or completing one never touches the other. The
	 * owner check prevents sharing a task you do not own (IDOR).
	 *
	 * @param int    $noteId      The task to share.
	 * @param int    $ownerId     The sharing user (session id).
	 * @param string $targetEmail The recipient's email.
	 * @return array{success:bool,error?:string}
	 */
	public function shareNotiz($noteId, $ownerId, $targetEmail)
	{
		$noteId = (int) $noteId;
		$ownerId = (int) $ownerId;
		$targetEmail = strtolower(trim($targetEmail));

		if ($targetEmail === '') {
			return ['success' => false, 'error' => 'Please enter an email address.'];
		}

		$targetUserId = $this->findUserIdByEmail($targetEmail);
		if ($targetUserId === null) {
			return ['success' => false, 'error' => 'No TackPad user found with that email address.'];
		}
		if ($targetUserId === $ownerId) {
			return ['success' => false, 'error' => 'You cannot share a task with yourself.'];
		}

		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->safeLoad();
		$encryption_key = getenv('ENCRYPTION_KEY');

		// Fetch the owner's task (ownership enforced in the WHERE clause).
		$statement = $this->db->prepare('SELECT * FROM `notes` WHERE `NoteId` = :id AND `fk_usersId` = :owner');
		$statement->bindParam(':id', $noteId, PDO::PARAM_INT);
		$statement->bindParam(':owner', $ownerId, PDO::PARAM_INT);
		$statement->execute();
		$note = $statement->fetch(PDO::FETCH_ASSOC);

		if (!$note) {
			return ['success' => false, 'error' => 'Task not found.'];
		}

		// Decrypt the source fields with the source row's IV.
		$iv = base64_decode($note['iv']);
		$titel = $this->decrypt($note['titel'], $encryption_key, $iv);
		$aufgabe = $this->decrypt($note['notiz'], $encryption_key, $iv);
		$prioritaet = $this->decrypt($note['prioritaet'], $encryption_key, $iv);
		$status = $this->decrypt($note['status'], $encryption_key, $iv);
		$datum = $this->decrypt($note['date_to_complete'], $encryption_key, $iv);
		$dateCompleted = $note['date_when_completed'] !== null
			? $this->decrypt($note['date_when_completed'], $encryption_key, $iv)
			: null;

		// Re-encrypt for the recipient under a brand-new, independent IV.
		$newIv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
		$newIvB64 = base64_encode($newIv);
		$last_change = date('Y-m-d H:i:s');

		$enc_titel = $this->encrypt($titel, $encryption_key, $newIv);
		$enc_aufgabe = $this->encrypt($aufgabe, $encryption_key, $newIv);
		$enc_prioritaet = $this->encrypt($prioritaet, $encryption_key, $newIv);
		$enc_status = $this->encrypt($status, $encryption_key, $newIv);
		$enc_datum = $this->encrypt($datum, $encryption_key, $newIv);
		$enc_dateCompleted = $dateCompleted !== null
			? $this->encrypt($dateCompleted, $encryption_key, $newIv)
			: null;

		$insert = $this->db->prepare('INSERT INTO `notes` (titel, notiz, prioritaet, status, date_to_complete, date_when_completed, last_change, fk_usersId, iv, shared) VALUES (:titel, :aufgabe, :prioritaet, :status, :date_to_complete, :date_when_completed, :last_change, :fk_usersId, :iv, 0)');
		$insert->bindParam(':titel', $enc_titel, PDO::PARAM_STR);
		$insert->bindParam(':aufgabe', $enc_aufgabe, PDO::PARAM_STR);
		$insert->bindParam(':prioritaet', $enc_prioritaet, PDO::PARAM_STR);
		$insert->bindParam(':status', $enc_status, PDO::PARAM_STR);
		$insert->bindParam(':date_to_complete', $enc_datum, PDO::PARAM_STR);
		$insert->bindParam(':date_when_completed', $enc_dateCompleted, $enc_dateCompleted === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$insert->bindParam(':last_change', $last_change, PDO::PARAM_STR);
		$insert->bindParam(':fk_usersId', $targetUserId, PDO::PARAM_INT);
		$insert->bindParam(':iv', $newIvB64, PDO::PARAM_STR);
		$insert->execute();

		// Flag the original so the owner sees it has been shared.
		$flag = $this->db->prepare('UPDATE `notes` SET `shared` = 1 WHERE `NoteId` = :id AND `fk_usersId` = :owner');
		$flag->bindParam(':id', $noteId, PDO::PARAM_INT);
		$flag->bindParam(':owner', $ownerId, PDO::PARAM_INT);
		$flag->execute();

		return ['success' => true];
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

	/**
	 * Marks the given tasks as completed for $userId.
	 *
	 * `status` stays encrypted (the list decrypts it to decide open/completed).
	 * The completion moment is recorded in the plain `completed_at` column so it
	 * can be displayed and ordered directly, and `last_change` is stored plain
	 * (matching create/edit/updateDate). Every task is scoped to the owner so a
	 * user cannot complete another user's task (IDOR).
	 *
	 * @return array{success:bool,updated:array<int,array{id:int,completed_at:string,last_change:string}>,errors:string[]}
	 */
	public function istErledigt($ids, $userId)
	{
		return $this->setCompletion($ids, (int) $userId, true);
	}


	/**
	 * Reopens the given completed tasks for $userId: status back to '0' and
	 * `completed_at` cleared. Owner-scoped like istErledigt().
	 *
	 * @return array{success:bool,updated:array<int,array{id:int,completed_at:string,last_change:string}>,errors:string[]}
	 */
	public function undone($ids, $userId)
	{
		return $this->setCompletion($ids, (int) $userId, false);
	}

	/**
	 * Shared implementation of complete / reopen. Re-encrypts only `status`
	 * (reusing the row's own IV so the other encrypted fields stay decryptable),
	 * sets the plain `completed_at` and `last_change`, and enforces ownership.
	 *
	 * @param string|array $ids       Comma string or array of NoteIds.
	 * @param int          $userId    Owning user.
	 * @param bool         $completed True to complete, false to reopen.
	 */
	private function setCompletion($ids, int $userId, bool $completed): array
	{
		if (is_string($ids)) {
			$ids = explode(',', $ids);
		}
		$cleaned_ids = array_map('intval', $ids);

		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->safeLoad();
		$encryption_key = getenv('ENCRYPTION_KEY');

		$status = $completed ? '1' : '0';
		$updated = [];
		$errors = [];

		foreach ($cleaned_ids as $id) {
			try {
				$statement = $this->db->prepare('SELECT `iv`, `fk_usersId` FROM `notes` WHERE `NoteId` = :id');
				$statement->bindParam(':id', $id, PDO::PARAM_INT);
				$statement->execute();
				$row = $statement->fetch(PDO::FETCH_ASSOC);

				if (!$row || (int) $row['fk_usersId'] !== $userId) {
					$errors[] = "Task $id not found or not owned by user.";
					continue;
				}

				$iv = base64_decode($row['iv']);
				$encrypted_status = $this->encrypt($status, $encryption_key, $iv);
				$last_change = date('Y-m-d H:i:s');
				$completed_at = $completed ? date('Y-m-d H:i:s') : null;

				$update = $this->db->prepare(
					'UPDATE notes SET status = :status, completed_at = :completed_at, last_change = :last_change
					 WHERE NoteId = :id AND fk_usersId = :userId'
				);
				$update->bindParam(':status', $encrypted_status, PDO::PARAM_STR);
				$update->bindValue(':completed_at', $completed_at, $completed_at === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
				$update->bindParam(':last_change', $last_change, PDO::PARAM_STR);
				$update->bindParam(':id', $id, PDO::PARAM_INT);
				$update->bindParam(':userId', $userId, PDO::PARAM_INT);
				$update->execute();

				$updated[] = [
					'id' => $id,
					'completed_at' => $completed_at ?? '',
					'last_change' => $last_change,
				];
			} catch (PDOException $e) {
				$errors[] = "Database error for Task $id: " . $e->getMessage();
			}
		}

		return ['success' => empty($errors) || !empty($updated), 'updated' => $updated, 'errors' => $errors];
	}

	/**
	 * Deletes the given tasks, scoped to $userId so a user can never delete
	 * another user's task (IDOR). Ids are cast to integers (NoteId is an int
	 * column) rather than html-escaped, which is the correct sanitisation for a
	 * numeric key. Returns the ids that were actually deleted.
	 */
	public function delete($ids, $userId)
	{
		if (is_string($ids)) {
			$ids = explode(',', $ids);
		}
		$cleaned_ids = array_values(array_filter(array_map('intval', $ids)));
		$userId = (int) $userId;

		if ($cleaned_ids === []) {
			return ["success" => false, "error" => "No valid task ids were provided."];
		}

		$placeholders = implode(',', array_fill(0, count($cleaned_ids), '?'));
		$statement = $this->db->prepare(
			"DELETE FROM notes WHERE NoteId IN ($placeholders) AND fk_usersId = ?"
		);

		try {
			$statement->execute(array_merge($cleaned_ids, [$userId]));

			if ($statement->rowCount() > 0) {
				return ["success" => true, "deleted_ids" => $cleaned_ids];
			}
			return ["success" => false, "error" => "No tasks were deleted. The provided ids may be invalid."];
		} catch (PDOException $e) {
			return ["success" => false, "error" => "Database error: " . $e->getMessage()];
		}
	}
}