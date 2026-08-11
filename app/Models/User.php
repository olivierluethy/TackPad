<?php

/**
 * User model — the single source of truth for reading and writing users.
 *
 * Emails are never stored in clear: each row keeps a per-user random `salt` and
 * stores `email` as `hash_hmac('sha256', <lowercased email>, salt)`. Matching an
 * email therefore means recomputing the HMAC against every user's salt. This
 * scheme is preserved exactly from the original inline auth code so existing
 * accounts keep working — do not change it without a data migration.
 *
 * All persistence for auth (login, registration) and avatars lives here, so a
 * change to the users table is made in exactly one place.
 */
class User
{
    /** @var PDO */
    private $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? connectDatabase();
    }

    /**
     * Finds the user id whose stored email HMAC matches $email, or null.
     * hash_equals guards the comparison against timing side-channels.
     */
    public function findIdByEmail(string $email): ?int
    {
        $email = strtolower(trim($email));
        if ($email === '') {
            return null;
        }

        $stmt = $this->db->query('SELECT id, email, salt FROM users');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (hash_equals($row['email'], hash_hmac('sha256', $email, $row['salt']))) {
                return (int) $row['id'];
            }
        }
        return null;
    }

    /** True when an account already exists for $email. */
    public function emailExists(string $email): bool
    {
        return $this->findIdByEmail($email) !== null;
    }

    /** Returns the full user row for an id, or null. */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Verifies credentials and returns the matching user row on success, or
     * null when the email is unknown or the password is wrong. Callers cannot
     * tell the two failures apart from the return value (avoids user
     * enumeration); use the boolean-return overload for tailored messages.
     */
    public function authenticate(string $email, string $password): ?array
    {
        $email = strtolower(trim($email));
        $stmt = $this->db->query('SELECT id, email, password, salt FROM users');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (hash_equals($row['email'], hash_hmac('sha256', $email, $row['salt']))) {
                return password_verify($password, $row['password']) ? $row : null;
            }
        }
        return null;
    }

    /**
     * Creates a new account and returns its id. Generates a fresh salt, stores
     * the salted email HMAC and a password hash. The caller is responsible for
     * validation (see Validator) and for checking emailExists() first.
     */
    public function create(string $email, string $password): int
    {
        $email = strtolower(trim($email));
        $salt = bin2hex(random_bytes(16));
        $emailHash = hash_hmac('sha256', $email, $salt);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            'INSERT INTO users (email, password, salt) VALUES (:email, :password, :salt)'
        );
        $stmt->execute([
            ':email' => $emailHash,
            ':password' => $passwordHash,
            ':salt' => $salt,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Sets the avatar from a confirmed remote URL, clearing any uploaded file
     * so the two sources stay mutually exclusive.
     */
    public function setAvatarUrl(int $id, string $url): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET avatar_url = :url, avatar_path = NULL WHERE id = :id'
        );
        $stmt->execute([':url' => $url, ':id' => $id]);
    }

    /**
     * Sets the avatar from an uploaded file path (relative to the web root),
     * clearing any remote URL.
     */
    public function setAvatarPath(int $id, string $path): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET avatar_path = :path, avatar_url = NULL WHERE id = :id'
        );
        $stmt->execute([':path' => $path, ':id' => $id]);
    }

    /** Removes any avatar, reverting the user to the monogram fallback. */
    public function clearAvatar(int $id): void
    {
        $this->db->prepare('UPDATE users SET avatar_url = NULL, avatar_path = NULL WHERE id = :id')
            ->execute([':id' => $id]);
    }

    /**
     * Resolves the best avatar source for a user row: the uploaded file wins
     * over a remote URL; returns '' when neither is set (monogram fallback).
     */
    public static function avatarSrc(?array $user): string
    {
        if (!$user) {
            return '';
        }
        if (!empty($user['avatar_path'])) {
            return $user['avatar_path'];
        }
        return !empty($user['avatar_url']) ? $user['avatar_url'] : '';
    }
}
