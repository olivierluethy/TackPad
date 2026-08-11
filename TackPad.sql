-- SQL Structure for the TackPad DATABASE --

DROP DATABASE IF EXISTS TackPad;
CREATE DATABASE TackPad;
USE TackPad;

CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    salt VARCHAR(255) NOT NULL UNIQUE,
    -- Avatar: a confirmed remote URL OR an uploaded file path (mutually
    -- exclusive; uploaded file wins). Both null => monogram fallback.
    avatar_url VARCHAR(1024),
    avatar_path VARCHAR(255)
);

CREATE TABLE notes (
    NoteId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    titel VARCHAR(256) NOT NULL,
    notiz VARCHAR(256) NOT NULL,
    prioritaet VARCHAR(256) NOT NULL,
    status VARCHAR(256) NOT NULL,
    date_to_complete VARCHAR(256) NOT NULL,
    date_when_completed VARCHAR(256),
    -- Plain (unencrypted) completion timestamp, set when a task is marked done
    -- and cleared when reopened. Directly displayable/sortable, unlike the
    -- legacy encrypted date_when_completed.
    completed_at DATETIME NULL DEFAULT NULL,
    last_change VARCHAR(256),
    fk_usersId INT NOT NULL,
    iv VARCHAR(256) NOT NULL,
    -- Plain boolean flag (not encrypted content): marks a task the owner has
    -- shared with another user, used only to show a "shared" indicator.
    shared TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (fk_usersId) REFERENCES users(id)
);