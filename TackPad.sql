-- SQL Structure for the TackPad DATABASE --

DROP DATABASE IF EXISTS TackPad;
CREATE DATABASE TackPad;
USE TackPad;

CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    salt VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE notes (
    NoteId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    titel VARCHAR(256) NOT NULL,
    notiz VARCHAR(256) NOT NULL,
    prioritaet VARCHAR(256) NOT NULL,
    status VARCHAR(256) NOT NULL,
    date_to_complete VARCHAR(256) NOT NULL,
    date_when_completed VARCHAR(256),
    last_change VARCHAR(256),
    fk_usersId INT NOT NULL,
    iv VARCHAR(256) NOT NULL,
    FOREIGN KEY (fk_usersId) REFERENCES users(id)
);