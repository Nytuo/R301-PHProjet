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
        create table products (
            id INTEGER PRIMARY KEY,
            ref varchar(255) not null,
            title varchar(255) not null,
            public_price float not null,
            paid_price float not null,
            description text not null,
            image varchar(255) not null,
            quantity int not null
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

    public function insertProduct(string $name,string $ref, int $public_price,int $paid_price, string $description, string $image, int $quantity)
    {
        $sqlQuery = "INSERT INTO products (id,title,ref, public_price,paid_price, description, image, quantity) VALUES (NULL,:title,:ref, :public_price,:paid_price, :description, :image, :quantity)";
        $stmt = $this->db->prepare($sqlQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array('title' => $name,'ref' => $ref, 'public_price' => $public_price,'paid_price' => $paid_price, 'description' => $description, 'image' => $image, 'quantity' => $quantity));

    }

    public function getProducts(): array
    {
        $result = $this->db->query("SELECT * FROM products");
        $result = $result->fetchAll();
        $products = [];
        foreach ($result as $product) {
            $products[] = $product;
        }
        return $products;
    }

    public function getProduct(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('id' => $id));
        $result = $stmt->fetch();
        return $result;
    }


    public function close()
    {
        $this->db = null;
    }

}