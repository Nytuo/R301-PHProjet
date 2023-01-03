<?php





//echo phpinfo();
//create db connection with sqlLite

try {

        $db = new PDO("sqlite:database.db");

        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "Database connection established <br>";

    } catch (PDOException $e) {

        echo $e->getMessage();

}

//create db if not exists
$dbInit = "CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    price INTEGER,
    description TEXT,
    image TEXT
)";
try {

        $db->exec($dbInit);

        echo "Database created <br>";

    } catch (PDOException $e) {

        echo $e->getMessage();

}

//db close

$db = null;