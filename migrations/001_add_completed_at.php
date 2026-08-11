<?php
/**
 * Adds a plain `completed_at` DATETIME to `notes`.
 *
 * The legacy `date_when_completed` column stores an *encrypted* unix timestamp,
 * which cannot be read or sorted without decrypting every row. `completed_at`
 * is plain metadata (like `last_change` and `shared`) so the exact completion
 * moment can be displayed and ordered directly. Set when a task is marked done,
 * cleared when it is reopened.
 *
 * Idempotent: only adds the column when it is missing.
 */
return [
    'name' => '001_add_completed_at',
    'up' => function (PDO $db): string {
        if (columnExists($db, 'notes', 'completed_at')) {
            return 'skipped (notes.completed_at already exists)';
        }
        $db->exec('ALTER TABLE `notes` ADD COLUMN `completed_at` DATETIME NULL DEFAULT NULL AFTER `date_when_completed`');
        return 'added notes.completed_at';
    },
];
