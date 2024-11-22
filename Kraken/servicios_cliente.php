<?php
include 'template/header.php';
include 'template/navbar.php';
?>

<main class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4">Nuestros Servicios</h2>
            <p class="lead">En nuestro estudio, ofrecemos una amplia variedad de servicios diseñados para cumplir con todas tus expectativas y necesidades de tatuajes. ¡Descubre todo lo que podemos hacer por ti!</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title text-center">Tatuajes Personalizados</h3>
                        <img src="recursos/servicio1.jpg" alt="Tatuajes Personalizados" class="imgUbicacion img-fluid">
                        <p class="card-text mt-3">Realizamos diseños únicos adaptados a tus ideas, con diferentes estilos que van desde el realismo hasta el minimalismo.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title text-center">Tatuajes de Cobertura</h3>
                        <img src="recursos/servicio2.jpg" alt="Tatuajes de Cobertura" class="imgUbicacion img-fluid">
                        <p class="card-text mt-3">Ofrecemos servicios de cobertura de tatuajes antiguos o no deseados con nuevas y creativas propuestas.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title text-center">Consultoría y Diseño</h3>
                        <img src="recursos/servicio3.jpg" alt="Consultoría y Diseño" class="imgUbicacion img-fluid">
                        <p class="card-text mt-3">Nuestros tatuadores te asesorarán en la creación de un diseño único y en el proceso de tatuaje.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include 'template/footer.php';
?>
