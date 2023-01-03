<?php

class product
{
    public $id;
    public $name;
    public $price;
    public $description;
    public $image;
    public $quantity;
    public $category;
    public $db;

    public function __construct($id)
    {
        $this->db = new mysqli('localhost', 'root', '', 'comics');
        if ($this->db->connect_errno) {
            echo "Failed to connect to MySQL: " . $this->db->connect_error;
            exit();
        }

        $this->id = $id;
        $this->getProduct();

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    private function getProduct()
    {

        $result = $this->db->query("SELECT * FROM products WHERE id = $this->id");
        $row = $result->fetch_assoc();
        $this->name = $row['name'];
        $this->price = $row['price'];
        $this->description = $row['description'];
        $this->image = $row['image'];
        $this->quantity = $row['quantity'];
        $this->category = $row['category'];

    }


}

?>