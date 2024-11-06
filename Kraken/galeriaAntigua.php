<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería - Estudio de Tatuajes Kraken</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header>
    <div id="banner"> 
        <img class="logoBanner" src="recursos/logo.jpg" alt="logoKraken">
        <h1>Estudio de Tatuajes Kraken</h1>
    </div>
</header>

<nav>
    <ul>
        <li><a href="index.php">Inicio</a></li>
        <li><a href="galeria.php">Galería</a></li>
        <li><a href="#">Tatuadores</a></li>
        <li><a href="#">Servicios</a></li>
        <li><a href="contacto.php">Contacto</a></li>
        <li><a href="login.php">Acceso Admin</a></li>
    </ul>
</nav>

<div class="carousel-wrap carousel">
    <button type="button" class="prev" data-prev>Prev</button>
    <div class="slides-wrap">
        <ul class="slides">
            <li class="slide"><img src="recursos/d1.jpg" alt="slide"  style="width: 300px; height: 300px;"></li>
            <li class="slide"><img src="recursos/d2.jpg" alt="slide" style="width: 300px; height: 300px;"></li>
            <li class="slide"><img src="recursos/d3.jpg" alt="slide" style="width: 300px; height: 300px;"></li>
            <li class="slide"><img src="recursos/d4.jpg" alt="slide" style="width: 300px; height: 300px;"></li>
            <li class="slide"><img src="recursos/d1.jpg" alt="slide" style="width: 300px; height: 300px;"></li>
        </ul>
    </div>
    <button type="button" data-next>Next</button>
</div>
<footer>
    <p>&copy; 2024 Estudio de Tatuajes Kraken. Todos los derechos reservados.</p>
</footer>
<script>
class Carousel {
    constructor(el) {
        this.el = el;
        this.currentIndex = 0;
        this.slidesMargin = 0;
        this.initElements();
        this.initCarousel();
        this.listenEvents();
    }

    initElements() {
        this.elements = {
            prev: this.el.querySelector('[data-prev]'),
            next: this.el.querySelector('[data-next]'),
            slides: this.el.querySelector('.slides'),
        };
    }

    initCarousel() {
        this.initSlides();
    }

    initSlides() {
        this.slides = this.el.querySelectorAll('.slide');
    }

    listenEvents() {
        this.elements.prev.addEventListener('click', () => {
            if (this.currentIndex <= 0) {
                if (this.el.querySelectorAll('.slide').length === 7) {
                    this.elements.slides.appendChild(this.slides[0].cloneNode(true));
                    this.elements.slides.appendChild(this.slides[1].cloneNode(true));
                    this.elements.slides.appendChild(this.slides[2].cloneNode(true));
                }
                this.elements.slides.style.transition = 'none';
                this.slidesMargin += -(this.getSlideWidth(this.currentIndex)*(this.slides.length) + 50);
                this.elements.slides.style.marginLeft = `${this.slidesMargin}px`;
                this.currentIndex = this.slides.length;
            }

            this.slidesMargin += this.getSlideWidth(this.currentIndex - 1);
            this.elements.slides.style.marginLeft = `${this.slidesMargin}px`;
            this.currentIndex--;
            this.elements.slides.style.transition = 'all 300ms linear 0s';
        });

        this.elements.next.addEventListener('click', () => {
            if (this.currentIndex >= this.slides.length - 3) {
                if (this.el.querySelectorAll('.slide').length === 7) {
                    this.elements.slides.appendChild(this.slides[0].cloneNode(true));
                    this.elements.slides.appendChild(this.slides[1].cloneNode(true));
                    this.elements.slides.appendChild(this.slides[2].cloneNode(true));
                }
            }
                        
            this.slidesMargin -= this.getSlideWidth(this.currentIndex);
            this.elements.slides.style.marginLeft = `${this.slidesMargin}px`;
            this.currentIndex++;
            this.elements.slides.style.transition = 'all 300ms linear 0s';

            this.elements.slides.addEventListener("transitionend", () => {
                if (this.currentIndex >= this.slides.length) {
                    this.elements.slides.style.transition = 'none';
                    this.slidesMargin = 0;
                    this.elements.slides.style.marginLeft = `${this.slidesMargin}px`;
                    this.currentIndex = 0;
                }
            })
        });
    }

    getSlideWidth(index) {
        const slide = this.slides[index];
        const style = window.getComputedStyle(slide);
        const slideInnerSize = slide.getBoundingClientRect();
        return slideInnerSize.width
            + parseInt(style.marginLeft, 10)
            + parseInt(style.marginRight, 10);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const carousel = new Carousel(document.querySelector('.carousel'));
    console.dir(carousel);
});
</script>
</body>


<!--<main>
 <div class="carousel-wrap carousel">
    <button type="button" class="prev" data-prev>Prev</button>
    <div class="slides-wrap">
        <ul class="slides">
            <li class="slide"><img src="recursos/d1.jpg" alt="slide"></li>
            <li class="slide"><img src="recursos/d2.jpg" alt="slide"></li>
            <li class="slide"><img src="recursos/d3.jpg" alt="slide"></li>
            <li class="slide"><img src="recursos/d4.jpg" alt="slide"></li>
            <li class="slide"><img src="recursos/d1.jpg" alt="slide"></li>
            <li class="slide"><img src="recursos/d2.jpg" alt="slide"></li>
            <li class="slide"><img src="recursos/d3.jpg" alt="slide"></li>

        </ul>
    </div>
    <button type="button" data-next>Next</button>
</div>
</main> -->
    
</html>
