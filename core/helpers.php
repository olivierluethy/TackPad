<?php
/**
 * Safely escapes a string for HTML output.
 *
 * This function converts special characters to HTML entities to prevent XSS attacks.
 * By default, it uses ENT_QUOTES and UTF-8 encoding.
 *
 * @param string $value The string to be escaped.
 * @param int $flags Optional. A bitmask of one or more of the following flags, combined using the bitwise OR (|) operator:
 *                   - ENT_COMPAT: Will convert double-quotes and leave single-quotes alone.
 *                   - ENT_QUOTES: Will convert both double and single quotes.
 *                   - ENT_NOQUOTES: Will leave both double and single quotes unconverted.
 *                   - ENT_HTML401, ENT_HTML5, ENT_XML1, ENT_XHTML: Handle quotes differently according to different standards.
 *                   Default is ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401.
 * @param string $encoding Optional. An optional argument defining the encoding used in the conversion.
 *                         Default is 'UTF-8'.
 * @param bool $double_encode Optional. A boolean parameter that determines whether to convert existing html entities to html entities again.
 *                            Default is false.
 * @return string The escaped string.
 */
function e(string $value, int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, string $encoding = 'UTF-8', bool $double_encode = false): string
{
    return htmlspecialchars($value, $flags, $encoding, $double_encode);
}

/**
 * Nutze diese Funktion um auf einen POST-Wert
 * zuzugreifen.
 */
function post(string $key, $default = '')
{
    return $_POST[$key] ?? $default;
}

/**
 * Stellt eine Verbindung zur Datenbank her und gibt die
 * Datenbankverbindung als PDO zurück.
 */
$dbInstance = null;

function db(): PDO
{
    global $dbInstance;

    if ($dbInstance) {
        return $dbInstance;
    }

    try {
        $dbInstance = new PDO('mysql:host=127.0.0.1;dbname=' . $db['name'], $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ]);
    } catch (PDOException $e) {
        die('Keine Verbindung zur Datenbank möglich: ' . $e->getMessage());
    }
}