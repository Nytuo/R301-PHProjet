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
    public int $pages;
    public string $publisher;
    public string $outDate;
    public string $author;
    public string $language;
    public string $format;
    public string $dimensions;
    public string $category;

    /**
     * @param $ref
     * @param $id
     * @param string $title
     * @param float $publicPrice
     * @param float $paidPrice
     * @param string $description
     * @param string $image
     * @param int $quantity
     * @param int $pages
     * @param string $publisher
     * @param string $outDate
     * @param string $author
     * @param string $language
     * @param string $format
     * @param string $dimensions
     * @param string $category
     */
    public function __construct($ref, $id, string $title, float $publicPrice, float $paidPrice, string $description, string $image, int $quantity, int $pages, string $publisher, string $outDate, string $author, string $language, string $format, string $dimensions, string $category)
    {
        $this->ref = $ref;
        $this->id = $id;
        $this->title = $title;
        $this->publicPrice = $publicPrice;
        $this->paidPrice = $paidPrice;
        $this->description = $description;
        $this->image = $image;
        $this->quantity = $quantity;
        $this->pages = $pages;
        $this->publisher = $publisher;
        $this->outDate = $outDate;
        $this->author = $author;
        $this->language = $language;
        $this->format = $format;
        $this->dimensions = $dimensions;
        $this->category = $category;
    }


    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getPages(): int
    {
        return $this->pages;
    }

    /**
     * @param int $pages
     */
    public function setPages(int $pages): void
    {
        $this->pages = $pages;
    }

    /**
     * @return string
     */
    public function getPublisher(): string
    {
        return $this->publisher;
    }

    /**
     * @param string $publisher
     */
    public function setPublisher(string $publisher): void
    {
        $this->publisher = $publisher;
    }

    /**
     * @return string
     */
    public function getOutDate(): string
    {
        return $this->outDate;
    }

    /**
     * @param string $outDate
     */
    public function setOutDate(string $outDate): void
    {
        $this->outDate = $outDate;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getDimensions(): string
    {
        return $this->dimensions;
    }

    /**
     * @param string $dimensions
     */
    public function setDimensions(string $dimensions): void
    {
        $this->dimensions = $dimensions;
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
        if ($quantity < 0) {
            $quantity = 0;
        }

        $this->quantity = $quantity;
    }

    public function isAvailable()
    {
        if ($this->quantity == 0) {
            return "<span class='OutOfStock'>Rupture de stock</span>";
        } else if ($this->quantity < 10) {
            return "<span class='NotThatMuch'>" . $this->quantity . " restants</span>";
        } else if ($this->quantity > 0) {
            return "<span class='InStock'>En stock</span>";
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

    public function getRef()
    {
        return $this->ref;
    }

    public function displayProduct()
    {
        echo "<a href='product.php?id=" . $this->id . "'>";
        echo "<div class='product'>";
        echo "<img src=" . $this->image . " alt='' width='300' height='450'>";
        echo "<h2 style='font-size:  larger; text-align: center; margin-top: 5px'>" . $this->title . "</h2>";
        echo "<p class='priceFOnly f1rem center'>" . $this->publicPrice . "€</p>";
        echo "</div>";
        echo "</a>";

    }

}

?>