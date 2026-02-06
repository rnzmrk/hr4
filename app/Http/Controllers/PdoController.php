<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;

class PdoController extends Controller
{
    protected $pdo;

    public function __construct()
    {
        // PDO Connection
        $host = '127.0.0.1';       // your MariaDB host
        $db   = 'hr4';              // your database
        $user = 'root';              // your DB username
        $pass = '';      // your DB password
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        try {
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    // Example: Get all sessions
    public function getSessions()
    {
        $sql = "SELECT * FROM sessions LIMIT 10";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return response()->json($results);
    }

    // Example: Insert
    public function addSession(Request $request)
    {
        $sql = "INSERT INTO sessions (id, user_id, payload) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $request->id,
            $request->user_id,
            $request->payload
        ]);

        return response()->json(['message' => 'Inserted successfully']);
    }
}