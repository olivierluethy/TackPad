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

            // Current user's avatar (for the sidebar) — file wins over URL,
            // falling back to a monogram of the username's first letter.
            $currentUser = (new User())->getById((int) $_SESSION['id']);
            $avatarSrc = User::avatarSrc($currentUser);
            $initial = strtoupper(mb_substr($username, 0, 1));

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
                // Date and (optional) time arrive as separate inputs and are
                // merged into the single canonical stored shape here.
                $datum = combineDateTime($_POST['datum'] ?? '', $_POST['zeit'] ?? '');
                $datum = htmlspecialchars($datum);
                $prioritaet = htmlspecialchars($_POST['priority']);

                // last_change is stored raw (not encrypted) so the list view can
                // read it directly with strtotime — same contract as updateDate().
                $last_change = date('Y-m-d H:i:s');

                // Aufgabe erstellen (returns the new NoteId for the live insert)
                $newId = $notiz->createNotiz($titel, $aufgabe, $prioritaet, $status, $datum, $_SESSION['id'], $last_change);

                // Erfolgreich hinzugefügt, Rückgabe der neuen Aufgabe als JSON.
                // Raw date strings are returned so the client formats them with
                // the same rules as the server (formatTaskDate in taskStatus.js).
                echo json_encode([
                    'success' => true,
                    'task' => [
                        'id' => $newId,
                        'titel' => $titel,
                        'aufgabe' => $aufgabe,
                        'datum' => $datum,
                        'prioritaet' => $prioritaet,
                        'last_change' => $last_change,
                    ]
                ]);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'Invalid request method']);
        }
    }

    /* Aufgabe mit einem anderen Nutzer teilen */
    public function share()
    {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $email = (string) ($_POST['email'] ?? '');

        $notiz = new Notiz();
        $result = $notiz->shareNotiz($id, (int) $_SESSION['id'], $email);

        if (!$result['success']) {
            http_response_code(422);
        }
        echo json_encode($result);
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
        $result = $notiz->delete($_GET['id'] ?? '', (int) $_SESSION['id']);

        header("Content-Type: application/json");
        if ($result['success']) {
            echo json_encode(["success" => true, "ids" => $result['deleted_ids']]);
        } else {
            echo json_encode(["success" => false, "error" => $result['error']]);
        }
    }

    public function edit()
    {
        session_start();
        header('Content-Type: application/json');

        // Check if the user is logged in
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $notiz = new Notiz();
        $id = (int) ($_GET["id"] ?? 0);

        $titel = $_POST['titel'] ?? '';
        $aufgabe = $_POST['aufgabe'] ?? '';
        // Date and (optional) time arrive separately and merge into one value.
        $datum = combineDateTime($_POST['datum'] ?? '', $_POST['zeit'] ?? '');
        $prioritaet = $_POST['priority'] ?? '';
        $last_change = date('Y-m-d H:i:s');

        $ok = $notiz->edit($titel, $aufgabe, $datum, $prioritaet, $id, (int) $_SESSION['id'], $last_change);

        if (!$ok) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Task not found or not owned by user']);
            exit;
        }

        // Echo back the canonical stored values so the client updates the row in
        // place (no reload), formatted identically to a server render.
        echo json_encode([
            'success' => true,
            'task' => [
                'id' => $id,
                'datum' => $datum,
                'prioritaet' => $prioritaet,
                'last_change' => $last_change,
            ],
        ]);
    }

    /* Kalenderansicht (FullCalendar) */
    public function calendar()
    {
        session_start();

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $notiz = new Notiz();
        $username = $notiz->getUsernameFromEmail($_SESSION["email"]);

        $currentUser = (new User())->getById((int) $_SESSION['id']);
        $avatarSrc = User::avatarSrc($currentUser);
        $initial = strtoupper(mb_substr($username, 0, 1));

        require 'app/Views/calendar.view.php';
    }

    /* JSON-Feed der Aufgaben für FullCalendar (ISO 8601) */
    public function calendarEvents()
    {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Not logged in']);
            exit;
        }

        $encryption_key = getenv('ENCRYPTION_KEY');
        $notiz = new Notiz();
        $tasks = $notiz->tackpad()->fetchAll(); // already scoped to the session user

        $events = [];
        foreach ($tasks as $task) {
            $iv = base64_decode($task['iv']);
            $title    = $notiz->decrypt($task['titel'], $encryption_key, $iv);
            $note     = $notiz->decrypt($task['notiz'], $encryption_key, $iv);
            $status   = $notiz->decrypt($task['status'], $encryption_key, $iv);
            $priority = $notiz->decrypt($task['prioritaet'], $encryption_key, $iv);
            $rawDate  = $notiz->decrypt($task['date_to_complete'], $encryption_key, $iv);

            [$start, $allDay] = $this->toCalendarStart($rawDate);
            $isCompleted = $status === '1';

            $events[] = [
                'id'         => $task['NoteId'],
                'title'      => $title,
                'start'      => $start,
                'allDay'     => $allDay,
                'classNames' => ['tackpad-event--' . ($isCompleted ? 'completed' : 'open')],
                'extendedProps' => [
                    'note'      => $note,
                    'completed' => $isCompleted,
                    // Raw stored due-date and priority so the detail modal can
                    // format them with the same helpers as the task list.
                    'rawDate'   => $rawDate,
                    'priority'  => $priority,
                ],
            ];
        }

        echo json_encode($events);
    }

    /* Drag-and-drop reschedule: persist the new date in real time */
    public function updateTaskDate()
    {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $id     = (int) ($_POST['id'] ?? 0);
        $start  = (string) ($_POST['start'] ?? '');
        $allDay = ($_POST['allDay'] ?? 'true') === 'true';

        // Keep the wall-clock time the user dropped on — slice the ISO string
        // instead of round-tripping through strtotime to avoid timezone drift.
        if ($allDay) {
            $stored = substr($start, 0, 10);                        // YYYY-MM-DD
        } else {
            $stored = str_replace('T', ' ', substr($start, 0, 19)); // YYYY-MM-DD HH:MM:SS
        }

        if ($stored === '' || strtotime($stored) === false) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => 'Invalid date']);
            exit;
        }

        $notiz = new Notiz();
        $ok = $notiz->updateDate($id, $stored, (int) $_SESSION['id']);

        if (!$ok) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Task not found or not owned by user']);
            exit;
        }

        echo json_encode(['success' => true, 'id' => $id, 'start' => $stored, 'allDay' => $allDay]);
    }

    /* Optional bonus: export tasks as an iCalendar feed (Google/Proton/Apple/Outlook) */
    public function exportIcs()
    {
        session_start();

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: login");
            exit;
        }

        $encryption_key = getenv('ENCRYPTION_KEY');
        $notiz = new Notiz();
        $tasks = $notiz->tackpad()->fetchAll();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//TackPad//Task Calendar//EN',
            'CALSCALE:GREGORIAN',
        ];

        foreach ($tasks as $task) {
            $iv = base64_decode($task['iv']);
            $title   = $notiz->decrypt($task['titel'], $encryption_key, $iv);
            $note    = $notiz->decrypt($task['notiz'], $encryption_key, $iv);
            $rawDate = $notiz->decrypt($task['date_to_complete'], $encryption_key, $iv);

            $ts = strtotime($rawDate);
            if ($ts === false) {
                continue;
            }
            $isAllDay = strlen(trim($rawDate)) <= 10;

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:tackpad-' . $task['NoteId'] . '@tackpad.local';
            $lines[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
            $lines[] = $isAllDay
                ? 'DTSTART;VALUE=DATE:' . date('Ymd', $ts)
                : 'DTSTART:' . date('Ymd\THis', $ts);
            $lines[] = 'SUMMARY:' . $this->icsEscape($title);
            $lines[] = 'DESCRIPTION:' . $this->icsEscape($note);
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="tackpad.ics"');
        echo implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Normalises a stored due-date into a FullCalendar start value.
     * Date-only strings become all-day events; datetime strings become timed
     * events in ISO 8601 (local wall-clock, no timezone conversion).
     *
     * @return array{0:string,1:bool} [start, allDay]
     */
    private function toCalendarStart(string $rawDate): array
    {
        $rawDate = trim($rawDate);
        $timestamp = strtotime($rawDate);

        if ($timestamp === false) {
            return [date('Y-m-d'), true];
        }

        if (strlen($rawDate) <= 10) { // "YYYY-MM-DD"
            return [date('Y-m-d', $timestamp), true];
        }

        return [date('Y-m-d\TH:i:s', $timestamp), false];
    }

    /** Escapes a value per RFC 5545 for safe inclusion in an .ics field. */
    private function icsEscape(string $value): string
    {
        return str_replace(
            ["\\", ";", ",", "\r\n", "\n"],
            ["\\\\", "\\;", "\\,", "\\n", "\\n"],
            $value
        );
    }

    /* Mark task(s) as done — JSON so the client moves rows without a reload. */
    public function erledigt()
    {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }

        $ids = (string) ($_POST['id'] ?? $_GET['id'] ?? '');
        $result = (new Notiz())->istErledigt($ids, (int) $_SESSION['id']);
        echo json_encode($result);
    }

    /* Reopen completed task(s) — JSON, same no-reload contract. */
    public function unerledigt()
    {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }

        $ids = (string) ($_POST['id'] ?? $_GET['id'] ?? '');
        $result = (new Notiz())->undone($ids, (int) $_SESSION['id']);
        echo json_encode($result);
    }

    public function login()
    {
        session_start();

        // Already signed in → straight to the dashboard.
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            header("location: home");
            exit;
        }

        $mode = 'login';
        $errors = [];
        $old = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = strtolower(trim($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $old['email'] = $email;

            $validator = new Validator();
            $validator->checkEmail($email);
            if (trim($password) === '') {
                $validator->add('password', 'Please enter your password.');
            }

            if ($validator->passes()) {
                $user = (new User())->authenticate($email, $password);
                if ($user !== null) {
                    // New session id on privilege change (fixation defence).
                    session_regenerate_id(true);
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = (int) $user['id'];
                    $_SESSION["email"] = $email;
                    $_SESSION["email_hash"] = $user['email'];

                    header("location: home");
                    exit();
                }
                // Generic message: never reveal whether the email exists.
                $errors['password'] = 'The email or password is incorrect.';
            } else {
                $errors = $validator->errors();
            }
        }

        require 'app/Views/auth.view.php';
    }

    public function logout()
    {
        require 'app/Views/logout.php';
    }

    /**
     * Updates the signed-in user's avatar. Three modes, all returning JSON so
     * the profile modal updates in place without a reload:
     *   - mode=url:    apply a confirmed remote image URL (validated).
     *   - file upload: store an uploaded image under public/uploads/avatars.
     *   - mode=remove: clear the avatar (revert to the monogram).
     */
    public function updateAvatar()
    {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $user = new User();
        $userId = (int) $_SESSION['id'];
        $mode = $_POST['mode'] ?? '';

        // --- Remove --------------------------------------------------------
        if ($mode === 'remove') {
            $user->clearAvatar($userId);
            echo json_encode(['success' => true, 'src' => '']);
            exit;
        }

        // --- Uploaded file -------------------------------------------------
        if (!empty($_FILES['avatar']['name'])) {
            $file = $_FILES['avatar'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'Upload failed. Please try again.']);
                exit;
            }
            if ($file['size'] > 2 * 1024 * 1024) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'The image is too large (max 2 MB).']);
                exit;
            }

            // Trust the real content type, not the client-supplied name.
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
            if (!isset($allowed[$mime])) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'Please upload a JPEG, PNG, GIF or WebP image.']);
                exit;
            }

            $dir = __DIR__ . '/../../public/uploads/avatars';
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            $filename = $userId . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
            $target = $dir . '/' . $filename;

            if (!move_uploaded_file($file['tmp_name'], $target)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Could not save the image.']);
                exit;
            }

            // Remove the previous uploaded file, if any, to avoid orphans.
            $previous = $user->getById($userId);
            if ($previous && !empty($previous['avatar_path'])) {
                $prevPath = __DIR__ . '/../../' . $previous['avatar_path'];
                if (is_file($prevPath)) {
                    @unlink($prevPath);
                }
            }

            $relative = 'public/uploads/avatars/' . $filename;
            $user->setAvatarPath($userId, $relative);
            echo json_encode(['success' => true, 'src' => $relative]);
            exit;
        }

        // --- Remote URL ----------------------------------------------------
        if ($mode === 'url') {
            $url = (string) ($_POST['avatar_url'] ?? '');
            $validator = new Validator();
            $validator->checkAvatarUrl($url);
            if ($validator->fails()) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => $validator->firstError()]);
                exit;
            }
            $user->setAvatarUrl($userId, trim($url));
            echo json_encode(['success' => true, 'src' => trim($url)]);
            exit;
        }

        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Nothing to update.']);
    }

    public function register()
    {
        session_start();

        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            header("location: home");
            exit;
        }

        // Same unified screen as login(), opened on the Register tab.
        $mode = 'register';
        $errors = [];
        $old = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = strtolower(trim($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');
            $old['email'] = $email;

            $validator = new Validator();
            $validator->checkEmail($email);
            $validator->checkPassword($password);
            $validator->checkPasswordConfirmation($password, $confirm);

            $user = new User();
            if ($validator->passes() && $user->emailExists($email)) {
                $validator->add('email', 'This email address is already registered.');
            }

            if ($validator->passes()) {
                $newUserId = $user->create($email, $password);

                // Auto-login: registration creates the account AND signs the user
                // in, so they land straight on the dashboard.
                session_regenerate_id(true);
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $newUserId;
                $_SESSION["email"] = $email;

                header("location: home");
                exit();
            }

            $errors = $validator->errors();
        }

        require 'app/Views/auth.view.php';
    }
}