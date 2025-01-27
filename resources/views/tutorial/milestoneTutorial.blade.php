<div class="nested-carousel-milestone-container-milestone">
    <div class="nested-carousel-milestone">
        <div class="nested-carousel-milestone-slide active">
            <img src="{{ asset('assets/img/tutorial/milestone/first.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">Este tablón está dividido en <b>cuatro fases</b>, cada una representando una etapa clave del flujo de trabajo:</br>
            <b>Por Hacer:</b> Aquí se listan los encargos creados que aún no tienen tareas asignadas. Es el punto de partida para organizar tus prioridades.</br>
            <b>En curso:</b> En esta columna están los encargos en los que se está trabajando activamente.</br>
            <b>Revisar</b> Los encargos completados que necesitan verificación o aprobación se encuentran aquí.</br>
            <b>Hecho:</b> ¡La meta final! Aquí se muestran los encargos con todas sus tareas completadas. Cuando un encargo esta en Hecho, se marca como finalizado.</p>
        </div>
        <div class="nested-carousel-milestone-slide">
            <img src="{{ asset('assets/img/tutorial/milestone/second.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide"><b>Creación de Encargos</b></br>
            Para crear un encargo, es importante que el proyecto correspondiente esté creado previamente.</br>
            Si tienes acceso al <b>Master Obras (MO)</b>, el proyecto se creará automáticamente, siempre que el<b>Master Obras</b> ya exista, sino se creara un campo vacío. </p>
        </div>
        <div class="nested-carousel-milestone-slide">
            <img src="{{ asset('assets/img/tutorial/milestone/third.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">Este tablón te muestra <b>encargos</b> que aun no tienen <b>tareas asignadas</b> y/o encargos en los que estas haciendo una tarea.</br>
            Aunque principalmente sea informativo también se puede, <b>ver, editar y borrar encargos</b> siempre que no haya tareas existentes.</p>
        </div>
    </div>
    <div class="nested-carousel-milestone-controls">
        <button class="nested-carousel-milestone-btn prev-btn">&lt;</button>
        <button class="nested-carousel-milestone-btn-right next-btn">&gt;</button>
    </div>
</div>

<style>
.imgStyleSlide{
    width: 65% !important;
    margin-top: 2% !important;
    border-radius: 15px !important;
}
.pStyleSlide{
    font-size: 14px !important;
    text-align: center !important;
   /* padding-left: 15% !important; */
    padding-top: 5px !important;
}
/* Horizontal carousel styles */
.nested-carousel-milestone-container-milestone {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
    display: flex; /* Hide the carousel initially */
    justify-content: center;
    align-items: center;
}

.nested-carousel-milestone-container-milestone.active {
    display: flex; /* Show carousel only when parent slide is active */
}
.nested-carousel-milestone {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.nested-carousel-milestone-slide {
    flex: 0 0 100%;
    opacity: 0; /* Hidden by default */
    transition: opacity 0.5s ease-in-out;
}

.nested-carousel-milestone-slide.active {
    opacity: 1; /* Only active slide is visible */
    display: flex;
    color: white;
    justify-content: center;
    align-content: center;
    flex-direction: column;
    align-items: center;
}


/* Buttons for horizontal carousel */
.nested-carousel-milestone-controls {
    position: absolute;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    height: 50px;
    top: 50%;
    transform: translateY(-50%);
}

.nested-carousel-milestone-btn {
    background-color: #aa182c !important;
    margin-left: 3% !important;
    font-size: 22px !important;
    width: 5% !important;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 50%;
    height: 100% !important;
}
.nested-carousel-milestone-btn-right {
    background-color: #aa182c !important;
    margin-right: 3% !important;
    font-size: 22px !important;
    width: 5% !important;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 50%;
    height: 100% !important;
}
.nested-carousel-milestone-btn:hover {
    background-color: #b9515f !important;
}
.nested-carousel-milestone-btn-right:hover {
    background-color: #b9515f !important;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const nestedCarousels = document.querySelectorAll(".nested-carousel-milestone");
    const parentSlides = document.querySelectorAll(".carousel-slide");

    // Attach MutationObserver to toggle nested carousel visibility
    parentSlides.forEach((slide) => {
        const nestedCarouselContainer = slide.querySelector(".nested-carousel-milestone-container-milestone");

        const observer = new MutationObserver(() => {
            if (slide.classList.contains("active")) {
                nestedCarouselContainer?.classList.add("active");
            } else {
                nestedCarouselContainer?.classList.remove("active");
            }
        });

        observer.observe(slide, { attributes: true, attributeFilter: ["class"] });
    });

    // Initialize nested carousels
    nestedCarousels.forEach((carousel) => {
        const nestedSlides = carousel.querySelectorAll(".nested-carousel-milestone-slide");
        const prevButton = carousel.closest(".nested-carousel-milestone-container-milestone").querySelector(".nested-carousel-milestone-btn.prev-btn");
        const nextButton = carousel.closest(".nested-carousel-milestone-container-milestone").querySelector(".nested-carousel-milestone-btn-right.next-btn");

        let nestedIndex = 0;

        function updateNestedCarousel() {
            nestedSlides.forEach((slide, index) => {
                if (index === nestedIndex) {
                    slide.classList.add("active");
                    slide.style.opacity = "1"; // Show the active slide
                } else {
                    slide.classList.remove("active");
                    slide.style.opacity = "0"; // Hide inactive slides
                }
            });

            // Adjust the transform property for horizontal scrolling
            const offset = nestedIndex * -100; // Move slides horizontally
            carousel.style.transform = `translateX(${offset}%)`;
        }

        prevButton.addEventListener("click", () => {
            nestedIndex = (nestedIndex - 1 + nestedSlides.length) % nestedSlides.length;
            updateNestedCarousel();
        });

        nextButton.addEventListener("click", () => {
            nestedIndex = (nestedIndex + 1) % nestedSlides.length;
            updateNestedCarousel();
        });

        updateNestedCarousel(); // Initialize the nested carousel state
    });
});
</script>
