<?php

require 'config.php';

class Task
{
    private $conn;

    public function __construct()
    {
        $this->conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function addTask($userId, $task)
    {
        $stmt = $this->conn->prepare("INSERT INTO tasks (user_id, task) VALUES (?, ?)");
        $stmt->execute([$userId, $task]);

        return true;
    }

    public function getTasks($userId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}