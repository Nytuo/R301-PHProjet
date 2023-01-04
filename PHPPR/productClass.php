<?php

class product
{
    public $ref;
    public $id;
    public string $title;
    public float $publicPrice;
    public float $paidPrice;
    public string $description;
    public string $image;
    public int $quantity;
    public string $category;
    public mysqli $db;

    public function __construct($id)
    {
        $this->db = new mysqli('localhost', 'root', '', 'comics');
        if ($this->db->connect_errno) {
            echo "Failed to connect to MySQL: " . $this->db->connect_error;
            exit();
        }

        $this->ref = $id;
        $this->getProduct();

    }
    /**
     * @return float
     */
    public function getPaidPrice(): float
    {
        return $this->paidPrice;
    }

    /**
     * @param float $paidPrice
     */
    public function setPaidPrice(float $paidPrice): void
    {
        $this->paidPrice = $paidPrice;
    }
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return float
     */
    public function getPublicPrice(): float
    {
        return $this->publicPrice;
    }

    /**
     * @param float $publicPrice
     */
    public function setPublicPrice(float $publicPrice): void
    {
        $this->publicPrice = $publicPrice;
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

        $result = $this->db->query("SELECT * FROM products WHERE id = $this->ref");
        $row = $result->fetch_assoc();
        $this->title = $row['name'];
        $this->publicPrice = $row['price'];
        $this->description = $row['description'];
        $this->image = $row['image'];
        $this->quantity = $row['quantity'];
        $this->category = $row['category'];

    }

    public function getRef()
    {
        return $this->ref;
    }


}

?>