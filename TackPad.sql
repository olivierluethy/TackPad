-- SQL Structure for the TackPad DATABASE --

DROP DATABASE IF EXISTS TackPad;
CREATE DATABASE TackPad;
USE TackPad;

CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
	username VARCHAR(255) NOT NULL,
	istAdmin TINYINT(1) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notes (
    NoteId INTEGER AUTO_INCREMENT,
    titel VARCHAR(100) NOT NULL,
	notiz VARCHAR(100) NOT NULL,
    prioritaet TINYINT(5) NOT NULL,
	status TINYINT(1) NOT NULL,
    date_to_complete DATE NOT NULL,
    date_when_completed DATE,
    last_change DATE,
	fk_usersId INT NOT NULL,
	FOREIGN KEY (fk_usersId) REFERENCES users(id),
    PRIMARY KEY(NoteId)
);