<?php 
    include __DIR__ . '/layouts/header.php';
?>

<main class="container-fluid">
    <section class="row align-items-center py-5 bg-light">
        <div class="col-md-6 order-md-1 p-5">
            <h2 class="text-custom-green mb-4">Conócenos</h2>
            <p class="lead">En NexoLearn, nos dedicamos a crear un impacto positivo en la educación y el desarrollo de habilidades. Nuestra misión es proporcionar recursos y oportunidades para que cada estudiante pueda alcanzar su máximo potencial.</p>
            <p>Los cursos ofrecidos están siempre vigilados por un maestro, quien se encarga de guiar a los estudiantes en su proceso de aprendizaje. Los materiales siempre estarán disponibles para consultar en cualquier momento y lugar.</p>
        </div>
        <div class="col-md-6 order-md-2">
            <img src="/PROYECTOFINALTECWEB/public/assets/images/ODS4-escuela-en-Peru.jpg" 
                 alt="Equipo educativo" 
                 class="img-fluid rounded-3 shadow">
        </div>
    </section>

    <section class="row align-items-center py-5">
        <div class="col-md-6 order-md-2 p-5">
            <h2 class="text-custom-green mb-4">Más Información</h2>
            <p>NexoLearn es una plataforma educativa que busca transformar la forma en que aprendemos y enseñamos. Nuestra visión es crear un entorno de aprendizaje inclusivo y accesible para todos.</p>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Cursos interactivos y atractivos</li>
                <li class="list-group-item">Disponible 24/7 desde cualquier dispositivo</li>
                <li class="list-group-item">Seguimiento personalizado por maestros</li>
            </ul>
        </div>
        <div class="col-md-6 order-md-1">
            <img src="/PROYECTOFINALTECWEB/public/assets/images/pexels-daniel-dang-2152289714-32119569.jpg" 
                 alt="Plataforma educativa" 
                 class="img-fluid rounded-3 shadow">
        </div>
    </section>

    <section class="py-5 bg-light">
        <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="/PROYECTOFINALTECWEB/public/assets/images/pexels-137666-710743.jpg" 
                         class="d-block w-100" 
                         alt="Estudiantes aprendiendo">
                </div>
                <div class="carousel-item">
                    <img src="/PROYECTOFINALTECWEB/public/assets/images/pexels-anastasiya-gepp-654466-1462630.jpg" 
                         class="d-block w-100" 
                         alt="Material didactico">
                </div>
                <div class="carousel-item">
                    <img src="/PROYECTOFINALTECWEB/public/assets/images/pexels-fauxels-3184163.jpg" 
                         class="d-block w-100" 
                         alt="Material educativo">
                </div>
                <div class="carousel-item">
                    <img src="/PROYECTOFINALTECWEB/public/assets/images/pexels-panditwiguna-3401403.jpg" 
                         class="d-block w-100" 
                         alt="Material educativo">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </section>
</main>


<?php 
    include __DIR__ . '/layouts/footer.php'; 
?>