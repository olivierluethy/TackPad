<?php

function connectDatabase() {
    try {
        return new PDO('mysql:host=127.0.0.1;dbname=tackpad', 'root', '');
    } catch (PDOException $e) {
        die('Keine Verbindung zur Datenbank möglich: ' . $e->getMessage());
    }
}