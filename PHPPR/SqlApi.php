<?php

class SqlApi
{
    public function __construct()
    {
        $this->db = new PDO("sqlite:database.db");
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function createTable()
    {
        $dbInit = "CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            price INTEGER,
            description TEXT,
            image TEXT
        );
        CREATE TABLE IF NOT EXISTS admin (
            email TEXT PRIMARY KEY NOT NULL,
            password TEXT NOT NULL
        );
        ";
        try {
            $this->db->exec($dbInit);
            echo "Database created <br>";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // foncion to conect to conect user
    public function connectUser($email, $password)
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM admin WHERE email=':email' AND password=':password'");
        $stmt->execute(array(':email' => $email, ':password' => $password));
        $result = $stmt->fetch();
        if ($result[0] == 0) {
            return false;
        } else {
            return true;
        }
    }


    public function close()
    {
        $this->db = null;
    }

}