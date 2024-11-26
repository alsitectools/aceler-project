<div class="nested-carousel-container">
    <div class="nested-carousel">
        <div class="nested-carousel-slide active">
            <img src="{{ asset('assets/img/tutorial/project/first.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">This is a stunning view of the mountains at sunrise.</p>
        </div>
        <div class="nested-carousel-slide">
            <img src="{{ asset('assets/img/tutorial/project/second.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">Explore the bustling streets of a modern city.</p>
        </div>
        <div class="nested-carousel-slide">
            <img src="{{ asset('assets/img/tutorial/project/third.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">Relax on a tranquil beach with crystal-clear waters.</p>
        </div>
    </div>
    <div class="nested-carousel-controls">
        <button class="nested-carousel-btn prev-btn">&lt;</button>
        <button class="nested-carousel-btn-right next-btn">&gt;</button>
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
    text-align: left !important;
    padding-left: 15% !important;
    padding-top: 5px !important;
}
/* Horizontal carousel styles */
.nested-carousel-container {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
    display: none; /* Hide the carousel initially */
    justify-content: center;
    align-items: center;
}

.nested-carousel-container.active {
    display: flex; /* Show carousel only when parent slide is active */
}
.nested-carousel {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.nested-carousel-slide {
    flex: 0 0 100%;
    opacity: 0; /* Hidden by default */
    transition: opacity 0.5s ease-in-out;
}

.nested-carousel-slide.active {
    opacity: 1; /* Only active slide is visible */
}


/* Buttons for horizontal carousel */
.nested-carousel-controls {
    position: absolute;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    height: 50px;
    top: 50%;
    transform: translateY(-50%);
}

.nested-carousel-btn {
    background-color: #aa182c !important;
    margin-left: 3% !important;
    font-size: 22px !important;
    width: 4% !important;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 50%;
    height: 100% !important;
}
.nested-carousel-btn-right {
    background-color: #aa182c !important;
    margin-right: 3% !important;
    font-size: 22px !important;
    width: 4% !important;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 50%;
    height: 100% !important;
}
.nested-carousel-btn:hover {
    background-color: #b9515f !important;
}
.nested-carousel-btn-right:hover {
    background-color: #b9515f !important;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const nestedCarousels = document.querySelectorAll(".nested-carousel");
    const parentSlides = document.querySelectorAll(".carousel-slide");

    // Attach MutationObserver to toggle nested carousel visibility
    parentSlides.forEach((slide) => {
        const nestedCarouselContainer = slide.querySelector(".nested-carousel-container");

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
        const nestedSlides = carousel.querySelectorAll(".nested-carousel-slide");
        const prevButton = carousel.closest(".nested-carousel-container").querySelector(".nested-carousel-btn.prev-btn");
        const nextButton = carousel.closest(".nested-carousel-container").querySelector(".nested-carousel-btn-right.next-btn");

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
