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
            total varchar(255) not null,
            date varchar(255) not null,          
            products text not null,           
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
    public function connectUser(string $email, string $ashPassword): bool
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM admin WHERE email=:email AND password=:password", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('email' => $email, 'password' => $ashPassword));
        $result = $stmt->fetch();
        if ($result["count(*)"] == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function insertProduct(string $name, string $ref, int $public_price, int $paid_price, string $description, string $image, int $quantity)
    {
        $sqlQuery = "INSERT INTO products (id,title,ref, public_price,paid_price, description, image) VALUES (NULL,:title,:ref, :public_price,:paid_price, :description, :image)";
        $stmt = $this->db->prepare($sqlQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array('title' => $name, 'ref' => $ref, 'public_price' => $public_price, 'paid_price' => $paid_price, 'description' => $description, 'image' => $image));
    }

    public function getProducts(): array
    {
        $result = $this->db->query("SELECT * FROM products");
        $result = $result->fetchAll();
        $qty = $this->db->query("SELECT products.id,quantity FROM gestionStock,products WHERE gestionStock.product_id=products.id");
        $qty = $qty->fetchAll();
        $result = array_map(function ($product) use ($qty) {
            foreach ($qty as $q) {
                if ($product['id'] == $q['id']) {
                    $product['quantity'] = $q['quantity'];
                }
            }
            return $product;
        }, $result);
           return $result;
    }

    public function getProduct(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('id' => $id));
        $qty = $this->db->prepare("SELECT quantity FROM gestionStock WHERE product_id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $qty->execute(array('id' => $id));
        $result = $stmt->fetch();
        $result2=$qty->fetch();
        $result["quantity"] = $result2["quantity"]??0;
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

    public function getClients()
    {
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

    public function getFour()
    {
        $result = $this->db->query("SELECT id,name,email,address,city,zip_code,country FROM fournisseur");
        $result = $result->fetchAll();
        $clients = [];
        foreach ($result as $client) {
            $clients[] = $client;
        }
        return $clients;
    }

    public function deleteFour(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM fournisseur WHERE id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('id' => $id));
    }

    public function getCommands()
    {
        $result = $this->db->query("SELECT id,name,email,address,city,zip_code,country FROM fournisseur");
        $result = $result->fetchAll();
        $clients = [];
        foreach ($result as $client) {
            $clients[] = $client;
        }
        return $clients;
    }

    public function deleteCommands(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM fournisseur WHERE id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('id' => $id));
    }

    public function getNbVente()
    {
        $result = $this->db->query("SELECT count(*) FROM facturation");
        $result = $result->fetch();
        return $result["count(*)"];
    }

    public function getChiffreAffaire()
    {
        $result = $this->db->query("SELECT sum(total) FROM facturation WHERE strftime('%Y',date) = strftime('%Y','now')");
        $result = $result->fetch();
        return $result["sum(total)"] == null ? 0 : $result["sum(total)"];
    }

    function getBenefices()
    {
        $totalVente = $this->db->query("SELECT sum(total) FROM facturation WHERE strftime('%Y',date) = strftime('%Y','now')");
        $totalVente = $totalVente->fetch();
        $qtyParProduct = $this->db->query("SELECT  products.title,facturation.product_id, sum(facturation.quantity), sum(gestionStock.quantity) FROM facturation, products,gestionStock WHERE facturation.product_id = products.id AND strftime('%Y',facturation.date) = strftime('%Y','now') GROUP BY facturation.product_id;");
        $qtyParProduct = $qtyParProduct->fetchAll();
        $qtyParProduct = array_map(function ($product) {
            $product["quantity"] = $product["sum(facturation.quantity)"] + $product["sum(gestionStock.quantity)"];
            return $product;
        }, $qtyParProduct);
        $paidPrice = $this->db->query("SELECT facturation.product_id, paid_price FROM facturation INNER JOIN products ON facturation.product_id = products.id WHERE strftime('%Y',facturation.date) = strftime('%Y','now') GROUP BY facturation.product_id;");
        $paidPrice = $paidPrice->fetchAll();
        $qtyParProduct = array_map(function ($product) use ($paidPrice) {
            foreach ($paidPrice as $product2) {
                if ($product["product_id"] == $product2["product_id"]) {
                    $product["montant"] = $product2["paid_price"] * $product["quantity"];
                    $product["paid_price"] = $product2["paid_price"];
                }
            }
            return $product;
        }, $qtyParProduct);
        $benefice = ($totalVente["sum(total)"] == null ? 0 : $totalVente["sum(total)"]) - array_reduce($qtyParProduct, function ($carry, $item) {
            return $carry + ($item["paid_price"] * $item["quantity"]);
        }, 0);
        return $benefice;
    }

    public function getAchatsEtMontant()
    {
        $result = $this->db->query("SELECT  products.title,facturation.product_id, sum(facturation.quantity), sum(gestionStock.quantity) FROM facturation, products,gestionStock WHERE facturation.product_id = products.id AND strftime('%Y',facturation.date) = strftime('%Y','now') GROUP BY facturation.product_id;");
        $result = $result->fetchAll();

        $result = array_map(function ($product) {
            $product["quantity"] = $product["sum(facturation.quantity)"] + $product["sum(gestionStock.quantity)"];
            return $product;
        }, $result);
        $result2 = $this->db->query("SELECT facturation.product_id, paid_price FROM facturation INNER JOIN products ON facturation.product_id = products.id WHERE strftime('%Y',facturation.date) = strftime('%Y','now') GROUP BY facturation.product_id;");
        $result2 = $result2->fetchAll();
        $result = array_map(function ($product) use ($result2) {
            foreach ($result2 as $product2) {
                if ($product["product_id"] == $product2["product_id"]) {
                    $product["montant"] = $product2["paid_price"] * $product["quantity"];
                    $product["paid_price"] = $product2["paid_price"];
                }
            }
            return $product;
        }, $result);
        $res = "<table id='achatMontant' class='responsive-table centered highlight'>
                    <thead>
                        <tr>
                            <th> ID</th>
                            <th> Titre</th>
                            <th> Quantité Acheté</th>
                            <th> Montant unitaire </th>
                            <th> Montant total </th>
                        </tr>
                    </thead>
                    <tbody>";
        foreach ($result as $product) {
            $res .= "<tr>
                        <td>" . $product["product_id"] . "</td>
                        <td>" . $product["title"] . "</td>
                        <td>" . $product["quantity"] . "</td>
                        <td class='priceFOnly'>" . $product["paid_price"] . "€</td>
                        <td class='priceFOnly'>" . $product["montant"] . "€</td>  

                    </tr>";
        }
        $res .= "</tbody></table>";
        return $res;
    }


    public function close()
    {
        $this->db = null;
    }

}