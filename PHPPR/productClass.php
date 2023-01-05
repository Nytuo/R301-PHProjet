<?php
require_once "SqlApi.php";

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
    public SqlApi $sql;

    public function __construct($id)
    {
        $this->sql = new SqlApi();
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

    public function isAvailable()
    {
        if ($this->quantity > 0) {
            return "<span class='InStock'>En stock</span>";
        } else if ($this->quantity == 0) {
            return "<span class='OutOfStock'>Rupture de stock</span>";
        } else if ($this->quantity < 10) {
            return "<span class='NotThatMuch'>Plus que " . $this->quantity . " exemplaire en stock</span>";
        } else {
            return "<span class='OutOfStock'>Non disponible à la vente</span>";
        }
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    private function getProduct(): void
    {
        $result = $this->sql->getProduct($this->ref);
        $this->title = $result['title'];
        $this->publicPrice = $result['public_price'];
        $this->description = $result['description'];
        $this->image = $result['image'];
        $this->quantity = $result['quantity'];
    }

    public function getRef()
    {
        return $this->ref;
    }

    public function displayProduct()
    {
        echo "<a href='product.php?id=" . $this->ref . "'>";
        echo "<div class='product'>";
        echo "<div class='flip'>";
        echo "<div class='front' style='background-image: url(" . $this->image . ");background-size: cover;'>";
        echo "</div>";
        echo "<div class='back'>";
        echo "<h2>" . $this->title . "</h2>";
        echo "<p>" . $this->publicPrice . "€</p>";
        echo "<p>" . $this->description . "</p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</a>";

    }

}

?>