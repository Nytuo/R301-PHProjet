class Product {
    constructor(ref, id, title, publicPrice, paidPrice, description, image, quantity, pages, publisher, outDate, author, language, format, dimensions, category) {
        this._ref = ref;
        this._id = id;
        this._title = title;
        this._publicPrice = publicPrice;
        this._paidPrice = paidPrice;
        this._description = description;
        this._image = image;
        this._quantity = quantity;
        this._pages = pages;
        this._publisher = publisher;
        this._outDate = outDate;
        this._author = author;
        this._language = language;
        this._format = format;
        this._dimensions = dimensions;
        this._category = category;
    }

    get ref() {
        return this._ref;
    }

    set ref(value) {
        this._ref = value;
    }

    get id() {
        return this._id;
    }

    set id(value) {
        this._id = value;
    }

    get title() {
        return this._title;
    }

    set title(value) {
        this._title = value;
    }

    get publicPrice() {
        return this._publicPrice;
    }

    set publicPrice(value) {
        this._publicPrice = value;
    }

    get paidPrice() {
        return this._paidPrice;
    }

    set paidPrice(value) {
        this._paidPrice = value;
    }

    get description() {
        return this._description;
    }

    set description(value) {
        this._description = value;
    }

    get image() {
        return this._image;
    }

    set image(value) {
        this._image = value;
    }

    get quantity() {
        return this._quantity;
    }

    set quantity(value) {
        this._quantity = value;
    }

    get pages() {
        return this._pages;
    }

    set pages(value) {
        this._pages = value;
    }

    get publisher() {
        return this._publisher;
    }

    set publisher(value) {
        this._publisher = value;
    }

    get outDate() {
        return this._outDate;
    }

    set outDate(value) {
        this._outDate = value;
    }

    get author() {
        return this._author;
    }

    set author(value) {
        this._author = value;
    }

    get language() {
        return this._language;
    }

    set language(value) {
        this._language = value;
    }

    get format() {
        return this._format;
    }

    set format(value) {
        this._format = value;
    }

    get dimensions() {
        return this._dimensions;
    }

    set dimensions(value) {
        this._dimensions = value;
    }

    get category() {
        return this._category;
    }

    set category(value) {
        this._category = value;
    }

    isAvailable() {
        if (this.quantity === 0) {
            return "<span class='OutOfStock'>Rupture de stock</span>";
        } else if (this.quantity < 10) {
            return "<span class='NotThatMuch'>" + this.quantity + " restants</span>";
        } else if (this.quantity > 0) {
            return "<span class='InStock'>En stock</span>";
        } else {
            return "<span class='OutOfStock'>Non disponible à la vente</span>";
        }
    }

    displayProduct() {
        let product = document.createElement("div");
        product.classList.add("product");
        let flip = document.createElement("div");
        flip.classList.add("flip");
        let front = document.createElement("div");
        front.classList.add("front");
        if (this.image !== null) {
            front.style.backgroundImage = "url(" + this.image + ")";
        } else {
            front.style.backgroundImage = "url(assets/images/no-image.webp)";
        }
        front.style.backgroundSize = "cover";
        let back = document.createElement("div");
        back.classList.add("back");
        let title = document.createElement("h2");
        title.innerHTML = this.title;
        let price = document.createElement("p");
        if (this._publicPrice !== null && this._publicPrice !== undefined && this._publicPrice !== "" && this._publicPrice !== "null") {
            price.innerHTML = this.publicPrice + "€";
        } else {
            price.innerHTML = "Prix non disponible";
        }
        let description = document.createElement("p");
        description.innerHTML = this.description !== undefined ? this.description : "";
        description.innerHTML = description.innerHTML.split(" ").splice(0, 20).join(" ") + "...";
        back.appendChild(title);
        back.appendChild(price);
        back.appendChild(description);
        flip.appendChild(front);
        flip.appendChild(back);
        product.appendChild(flip);
        product.style.cursor = "pointer";
        return product;
    }
}