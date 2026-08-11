<?php
/**
 * Adds avatar support to `users`.
 *
 * Two nullable columns per the brief: `avatar_url` for a remote image the user
 * pasted (confirmed via preview before applying) and `avatar_path` for a file
 * they uploaded (stored under public/uploads/avatars). Exactly one is set at a
 * time; the resolver prefers the uploaded file. Both null → monogram fallback.
 *
 * Idempotent: each column is added only when missing.
 */
return [
    'name' => '002_add_user_avatar',
    'up' => function (PDO $db): array {
        $done = [];
        if (!columnExists($db, 'users', 'avatar_url')) {
            $db->exec('ALTER TABLE `users` ADD COLUMN `avatar_url` VARCHAR(1024) NULL DEFAULT NULL');
            $done[] = 'added users.avatar_url';
        }
        if (!columnExists($db, 'users', 'avatar_path')) {
            $db->exec('ALTER TABLE `users` ADD COLUMN `avatar_path` VARCHAR(255) NULL DEFAULT NULL');
            $done[] = 'added users.avatar_path';
        }
        return $done ?: ['skipped (avatar columns already exist)'];
    },
];
