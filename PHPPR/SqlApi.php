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
            id INTEGER PRIMARY KEY not null,
            ref varchar(255) not null,
            title varchar(255) not null,
            public_price float not null,
            paid_price float not null,
            description text,
            image varchar(255),
            pages int,
            publisher varchar(255),
            out_date varchar(255),
            author varchar(255),
            language varchar(255),
            format varchar(255),
            dimensions varchar(255),   
            category varchar(255)
        );
        create table client(
            id INTEGER PRIMARY KEY,
            name varchar(255) not null,
            firstname varchar(255) not null,
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
    public function connectAdmin(string $email, string $ashPassword): bool
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

    public function insertProduct(string $name, string $ref, float $public_price, float $paid_price, string $description, string $image, int $quantity, int $pages, string $publisher, string $out_date, string $author, string $language, string $format, string $dimensions, string $category)
    {
        $sqlQuery = "INSERT INTO products (id,title,ref, public_price,paid_price, description, image,pages,publisher,out_date,author,language,format,dimensions,category) VALUES (?,:title,:ref,:public_price,:paid_price,:description,:image,:pages,:publisher,:out_date,:author,:language,:format,:dimensions,:category)";
        $stmt = $this->db->prepare($sqlQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(':title' => $name, ':ref' => $ref, ':public_price' => $public_price, ':paid_price' => $paid_price, ':description' => $description, ':image' => $image, ':pages' => $pages, ':publisher' => $publisher, ':out_date' => $out_date, ':author' => $author, ':language' => $language, ':format' => $format, ':dimensions' => $dimensions, ':category' => $category));
        $sqlQuery = "SELECT id FROM products WHERE ref=:ref";
        $stmt = $this->db->prepare($sqlQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array('ref' => $ref));
        $id = $stmt->fetch()["id"];
        $sqlQuery = "INSERT INTO gestionStock (product_id,quantity) VALUES (:product_id,:quantity)";
        $stmt = $this->db->prepare($sqlQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array('product_id' => $id, 'quantity' => $quantity));
    }

    public function insertFournisseur(string $name, string $email, string $address, string $city, string $zip_code, string $country)
    {
        $sqlQuery = "INSERT INTO fournisseur (id,name,email, address,city, zip_code, country) VALUES (NULL,:name,:email, :address,:city, :zip_code, :country)";
        $stmt = $this->db->prepare($sqlQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array('name' => $name, 'email' => $email, 'address' => $address, 'city' => $city, 'zip_code' => $zip_code, 'country' => $country));
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
        $result2 = $qty->fetch();
        $result["quantity"] = $result2["quantity"] ?? 0;
        return $result;
    }

    public function searchProduct(string $search)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE title LIKE :search OR ref LIKE :search", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('search' => "%$search%"));
        $stmt = $stmt->fetchAll();
        $qty = $this->db->query("SELECT products.id,quantity FROM gestionStock,products WHERE gestionStock.product_id=products.id");
        $qty = $qty->fetchAll();
        $stmt = array_map(function ($product) use ($qty) {
            foreach ($qty as $q) {
                if ($product['id'] == $q['id']) {
                    $product['quantity'] = $q['quantity'];
                }
            }
            return $product;
        }, $stmt);
        return $stmt;
    }

    public function deleteProduct(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('id' => $id));
    }

    public function getClients()
    {
        $result = $this->db->query("SELECT id,name,firstName,email,address,city,zip_code,country FROM client");
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
        $result = $this->db->query("SELECT id,client_id,fournisseur_id,product_id,quantity,total,date,products FROM facturation");
        $result = $result->fetchAll();
        $clients = [];
        foreach ($result as $client) {
            $clients[] = $client;
        }
        return $clients;
    }

    public function deleteCommands(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM facturation WHERE id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
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
        $result = $this->db->query("SELECT products.title,products.id,products.paid_price,gestionStock.quantity FROM products,gestionStock WHERE products.id = gestionStock.product_id");
        $result = $result->fetchAll();

        $result = array_map(function ($product) {
            $product["montant"] = $product["paid_price"] * $product["quantity"];
            return $product;
        }, $result);
        $result2 = $this->db->query("SELECT facturation.product_id,facturation.quantity FROM facturation INNER JOIN products ON facturation.product_id = products.id WHERE strftime('%Y',facturation.date) = strftime('%Y','now') GROUP BY facturation.product_id;");
        $result2 = $result2->fetchAll();
        $result = array_map(function ($product) use ($result2) {
            foreach ($result2 as $product2) {
                if ($product["id"] == $product2["product_id"]) {
                    $product["quantity"] += $product2["quantity"];
                    $product["montant"] = $product["paid_price"] * $product["quantity"];
                }
            }
            return $product;
        }, $result);
        $benefice = ($totalVente["sum(total)"] == null ? 0 : $totalVente["sum(total)"]) - array_reduce($result, function ($carry, $item) {
                return $carry + ($item["paid_price"] * $item["quantity"]);
            }, 0);
        return $benefice;
    }

    public function getMA(){
        $result = $this->db->query("SELECT products.title,products.id,products.paid_price,gestionStock.quantity FROM products,gestionStock WHERE products.id = gestionStock.product_id");
        $result = $result->fetchAll();

        $result = array_map(function ($product) {
            $product["montant"] = $product["paid_price"] * $product["quantity"];
            return $product;
        }, $result);
        $result2 = $this->db->query("SELECT facturation.product_id,facturation.quantity FROM facturation INNER JOIN products ON facturation.product_id = products.id WHERE strftime('%Y',facturation.date) = strftime('%Y','now') GROUP BY facturation.product_id;");
        $result2 = $result2->fetchAll();
        $result = array_map(function ($product) use ($result2) {
            foreach ($result2 as $product2) {
                if ($product["id"] == $product2["product_id"]) {
                    $product["quantity"] += $product2["quantity"];
                    $product["montant"] = $product["paid_price"] * $product["quantity"];
                }
            }
            return $product;
        }, $result);
        $MA = array_reduce($result, function ($carry, $item) {
                return $carry + ($item["paid_price"] * $item["quantity"]);
            }, 0);
        return $MA;
    }

    public function getAchatsEtMontant()
    {

        $result = $this->db->query("SELECT products.title,products.id,products.paid_price,gestionStock.quantity FROM products,gestionStock WHERE products.id = gestionStock.product_id");
        $result = $result->fetchAll();

        $result = array_map(function ($product) {
            $product["montant"] = $product["paid_price"] * $product["quantity"];
            return $product;
        }, $result);
        $result2 = $this->db->query("SELECT facturation.product_id,facturation.quantity FROM facturation INNER JOIN products ON facturation.product_id = products.id WHERE strftime('%Y',facturation.date) = strftime('%Y','now') GROUP BY facturation.product_id;");
        $result2 = $result2->fetchAll();
        $result = array_map(function ($product) use ($result2) {
            foreach ($result2 as $product2) {
                if ($product["id"] == $product2["product_id"]) {
                    $product["quantity"] += $product2["quantity"];
                    $product["montant"] = $product["paid_price"] * $product["quantity"];
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
                        <td>" . $product["id"] . "</td>
                        <td>" . $product["title"] . "</td>
                        <td>" . $product["quantity"] . "</td>
                        <td class='priceFOnly'>" . $product["paid_price"] . "€</td>
                        <td class='priceFOnly'>" . $product["montant"] . "€</td>
                        

                    </tr>";
        }
        $res .= "</tbody></table>";
        return $res;
    }

    public function updateQuantity(int $newqty, int $id): void
    {
        $stmt = $this->db->prepare("UPDATE gestionStock SET quantity=:quantity WHERE product_id=:id", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('quantity' => $newqty, 'id' => $id));

    }


    public function close()
    {
        $this->db = null;
    }

    public function insertUser(mixed $nom, mixed $prenom, mixed $email, mixed $password, mixed $adresse, mixed $ville, mixed $codePostal, mixed $pays)
    {
        $stmt = $this->db->prepare("INSERT INTO client (name,firstName,email,password,address,city,zip_code,country) VALUES (:nom,:prenom,:email,:password,:adresse,:ville,:codePostal,:pays)", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $password = hash('sha256', $password);
        $stmt->execute(array('nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'password' => $password, 'adresse' => $adresse, 'ville' => $ville, 'codePostal' => $codePostal, 'pays' => $pays));
    }

    public function connectUser(mixed $email, mixed $ashPassword)
    {
$stmt = $this->db->prepare("SELECT count(*) FROM client WHERE email=:email AND password=:password", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('email' => $email, 'password' => $ashPassword));
        $result = $stmt->fetch();
        if ($result["count(*)"] == 0) {
            return false;
        } else {
            return true;
        }

    }

    public function getUser(mixed $email, mixed $ashPassword)
    {
        $stmt = $this->db->prepare("SELECT * FROM client WHERE email=:email AND password=:password", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('email' => $email, 'password' => $ashPassword));
        $result = $stmt->fetch();
        return $result;
    }

    public function updateUserAddress(mixed $email, mixed $address, mixed $city, mixed $zip, mixed $country)
    {
        $stmt = $this->db->prepare("UPDATE client SET address=:address,city=:city,zip_code=:zip,country=:country WHERE email=:email", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(array('address' => $address, 'city' => $city, 'zip' => $zip, 'country' => $country, 'email' => $email));
    }

    public function updateUserPassword(mixed $email, mixed $password)
    {
        $stmt = $this->db->prepare("UPDATE client SET password=:password WHERE email=:email", [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $password = hash('sha256', $password);
        $stmt->execute(array('password' => $password, 'email' => $email));
    }

}