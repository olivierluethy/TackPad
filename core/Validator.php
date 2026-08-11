<?php

/**
 * Small validation layer shared by every write path (create/edit task,
 * register, share, avatar). Each `check*` method appends a field→message pair
 * to $errors when the value is invalid; `passes()`/`errors()` report the result.
 *
 * The point is a single, consistent place that decides what a valid task /
 * account / avatar looks like, so the controller never hand-rolls ad-hoc checks
 * and every endpoint rejects bad input the same way.
 */
class Validator
{
    /** @var array<string,string> field => first error message */
    private $errors = [];

    public const TITLE_MAX = 256;
    public const NOTE_MAX = 256;
    public const PASSWORD_MIN = 8;
    public const VALID_PRIORITIES = ['0', '1', '2', '3', '4'];

    /** Records an error for a field unless one is already set for it. */
    public function add(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    /** @return array<string,string> */
    public function errors(): array
    {
        return $this->errors;
    }

    /** First error message, or '' when valid — handy for single-field endpoints. */
    public function firstError(): string
    {
        return $this->errors === [] ? '' : reset($this->errors);
    }

    private function required(string $field, string $value, string $label): bool
    {
        if (trim($value) === '') {
            $this->add($field, "Please enter a {$label}.");
            return false;
        }
        return true;
    }

    // ---- Task fields ----------------------------------------------------

    public function checkTitle(string $value): void
    {
        if ($this->required('titel', $value, 'title') && mb_strlen(trim($value)) > self::TITLE_MAX) {
            $this->add('titel', 'The title is too long (max ' . self::TITLE_MAX . ' characters).');
        }
    }

    public function checkNote(string $value): void
    {
        if ($this->required('aufgabe', $value, 'task') && mb_strlen(trim($value)) > self::NOTE_MAX) {
            $this->add('aufgabe', 'The task is too long (max ' . self::NOTE_MAX . ' characters).');
        }
    }

    /**
     * Validates the date and optional time inputs together. Date must be a real
     * YYYY-MM-DD; time, when present, must be HH:MM.
     */
    public function checkDate(string $date, string $time = ''): void
    {
        $date = trim($date);
        if (!$this->required('datum', $date, 'date')) {
            return;
        }
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            $this->add('datum', 'Please enter a valid date.');
            return;
        }
        $time = trim($time);
        if ($time !== '' && !preg_match('/^\d{2}:\d{2}$/', $time)) {
            $this->add('zeit', 'Please enter a valid time (HH:MM).');
        }
    }

    public function checkPriority(string $value): void
    {
        if (!in_array($value, self::VALID_PRIORITIES, true)) {
            $this->add('priority', 'Please choose a valid priority.');
        }
    }

    // ---- Account fields -------------------------------------------------

    public function checkEmail(string $value, string $field = 'email'): void
    {
        $value = trim($value);
        if ($value === '') {
            $this->add($field, 'Please enter an email address.');
        } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->add($field, 'Please enter a valid email address.');
        }
    }

    public function checkPassword(string $value, string $field = 'password'): void
    {
        if (trim($value) === '') {
            $this->add($field, 'Please enter a password.');
        } elseif (mb_strlen($value) < self::PASSWORD_MIN) {
            $this->add($field, 'The password must be at least ' . self::PASSWORD_MIN . ' characters.');
        }
    }

    public function checkPasswordConfirmation(string $password, string $confirm): void
    {
        if (trim($confirm) === '') {
            $this->add('confirm_password', 'Please confirm the password.');
        } elseif ($password !== $confirm) {
            $this->add('confirm_password', 'The passwords do not match.');
        }
    }

    // ---- Avatar ---------------------------------------------------------

    /** A remote avatar URL must be a well-formed http(s) URL. */
    public function checkAvatarUrl(string $value): void
    {
        $value = trim($value);
        if ($value === '') {
            $this->add('avatar_url', 'Please enter an image URL.');
            return;
        }
        if (!filter_var($value, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $value)) {
            $this->add('avatar_url', 'Please enter a valid http(s) image URL.');
        }
    }
}
