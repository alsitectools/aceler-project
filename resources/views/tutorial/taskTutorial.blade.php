<div class="nested-carousel-task-container-task">
    <div class="nested-carousel-task">
        <div class="nested-carousel-task-slide active">
            <img src="{{ asset('assets/img/tutorial/task/first.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">This is a stunning view of the mountains at sunrise.</p>
        </div>
        <div class="nested-carousel-task-slide">
            <img src="{{ asset('assets/img/tutorial/task/second.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">Explore the bustling streets of a modern city.</p>
        </div>
        <div class="nested-carousel-task-slide">
            <img src="{{ asset('assets/img/tutorial/task/third.png') }}" class="imgStyleSlide"/>
            <p class="pStyleSlide">Relax on a tranquil beach with crystal-clear waters.</p>
        </div>
        <div class="nested-carousel-task-slide">
            <img src="{{ asset('assets/img/tutorial/task/fourth.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">Relax on a tranquil beach with crystal-clear waters.</p>
        </div>
    </div>
    <div class="nested-carousel-task-controls">
        <button class="nested-carousel-task-btn prev-btn">&lt;</button>
        <button class="nested-carousel-task-btn-right next-btn">&gt;</button>
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
    /*padding-left: 15% !important; */
    padding-top: 5px !important;
}
/* Horizontal carousel styles */
.nested-carousel-task-container-task {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
    display: flex; /* Hide the carousel initially */
    justify-content: center;
    align-items: center;
}

.nested-carousel-task-container-task.active {
    display: flex; /* Show carousel only when parent slide is active */
}
.nested-carousel-task {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.nested-carousel-task-slide {
    flex: 0 0 100%;
    opacity: 0; /* Hidden by default */
    transition: opacity 0.5s ease-in-out;
}

.nested-carousel-task-slide.active {
    opacity: 1; /* Only active slide is visible */
    display: flex;
    color: white;
    justify-content: center;
    align-content: center;
    flex-direction: column;
    align-items: center;
}


/* Buttons for horizontal carousel */
.nested-carousel-task-controls {
    position: absolute;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    height: 50px;
    top: 50%;
    transform: translateY(-50%);
}

.nested-carousel-task-btn {
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
.nested-carousel-task-btn-right {
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
.nested-carousel-task-btn:hover {
    background-color: #b9515f !important;
}
.nested-carousel-task-btn-right:hover {
    background-color: #b9515f !important;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const nestedCarousels = document.querySelectorAll(".nested-carousel-task");
    const parentSlides = document.querySelectorAll(".carousel-slide");

    // Attach MutationObserver to toggle nested carousel visibility
    parentSlides.forEach((slide) => {
        const nestedCarouselContainer = slide.querySelector(".nested-carousel-task-container-task");

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
        const nestedSlides = carousel.querySelectorAll(".nested-carousel-task-slide");
        const prevButton = carousel.closest(".nested-carousel-task-container-task").querySelector(".nested-carousel-task-btn.prev-btn");
        const nextButton = carousel.closest(".nested-carousel-task-container-task").querySelector(".nested-carousel-task-btn-right.next-btn");

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
