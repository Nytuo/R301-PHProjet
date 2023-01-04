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
    public function connectUser(string $email ,string $ashPassword) : bool
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM admin WHERE email=:email AND password=:password",[PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('email' => $email, 'password' => $ashPassword));
        $result = $stmt->fetch();
        if ($result["count(*)"] == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function insertProduct(string $name, int $price, string $description, string $image, int $quantity)
    {
        $sqlQuery = "INSERT INTO products (name, price, description, image, quantity, category) VALUES (:name, :price, :description, :image, :quantity, :category)";
        $stmt = $this->db->prepare($sqlQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(':name' => $name, ':price' => $price, ':description' => $description, ':image' => $image, ':quantity' => $quantity));

    }


    public function close()
    {
        $this->db = null;
    }

}