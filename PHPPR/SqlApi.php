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
        $dbInit = "
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
            image varchar(255) not null
        );
        create table client(
            id INTEGER PRIMARY KEY,
            name varchar(255) not null,
            email varchar(255) not null,
            password varchar(255) not null,
            address varchar(255) not null,
            city varchar(255) not null,
            zip_code varchar(255) not null,
            country varchar(255) not null
        );
        create table fournisseur(
            id INTEGER PRIMARY KEY,
            name varchar(255) not null,
            email varchar(255) not null,
            password varchar(255) not null,
            address varchar(255) not null,
            city varchar(255) not null,
            zip_code varchar(255) not null,
            country varchar(255) not null
        );
        create table facturation(
            
            id INTEGER PRIMARY KEY,
            client_id int not null,
            fournisseur_id int not null,
            product_id int not null,
            quantity int not null,
            total float not null,
            date date not null,
            foreign key (client_id) references client(id),
            foreign key (fournisseur_id) references fournisseur(id),
            foreign key (product_id) references products(id)
        );
        create table gestionStock(
            product_id int not null,
            quantity int not null,  
            foreign key (product_id) references products(id)
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
        $sqlQuery = "INSERT INTO products (id,title,ref, public_price,paid_price, description, image) VALUES (NULL,:title,:ref, :public_price,:paid_price, :description, :image)";
        $stmt = $this->db->prepare($sqlQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array('title' => $name,'ref' => $ref, 'public_price' => $public_price,'paid_price' => $paid_price, 'description' => $description, 'image' => $image));
    }

    public function getProducts(): array
    {
        $result = $this->db->query("SELECT * FROM products");
        $qty = $this->db->query("SELECT quantity FROM gestionStock");
        $result = $result->fetchAll();
        $result = array_map(function ($product) use ($qty) {
            $product["quantity"] = $qty->fetch()["quantity"];
            return $product;
        }, $result);
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
        $qty = $this->db->prepare("SELECT quantity FROM gestionStock WHERE product_id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $qty->execute(array('id' => $id));
        $result = $stmt->fetch();
        $result["quantity"] = $qty->fetch()["quantity"];
        return $result;
    }
    public function searchProduct(string $search)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE title LIKE :search OR ref LIKE :search", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('search' => "%$search%"));
        $result = $stmt->fetchAll();
        return $result;
    }

    public function deleteProduct(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('id' => $id));
    }

    public function getClients(){
        $result = $this->db->query("SELECT id,name,email,address,city,zip_code,country FROM client");
        $result = $result->fetchAll();
        $clients = [];
        foreach ($result as $client) {
            $clients[] = $client;
        }
        return $clients;
    }
    public function deleteClient(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM client WHERE id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('id' => $id));
    }

    public function close()
    {
        $this->db = null;
    }

}