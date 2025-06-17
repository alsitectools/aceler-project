<div class="nested-carousel-container">
    <div class="nested-carousel">
        <div class="nested-carousel-slide active">
            <img src="{{ asset('assets/img/tutorial/project/1.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('In this section you will learn how to create, visualize and delete projects.') }}</p>
        </div>
        <div class="nested-carousel-slide">
            <img src="{{ asset('assets/img/tutorial/project/2.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('Clicking on the Create new project button will open a tab in which you can assign project data, such as the name and type of the project.') }}
            </p>
        </div>
        <div class="nested-carousel-slide">
            <img src="{{ asset('assets/img/tutorial/project/3.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('If you are the creator of the project, you can click the three dots on the project to delete it. Note that this action cannot be reversed and will delete the project, as well as the order forms and hours assigned to it.') }}
            </p>
        </div>
        <div class="nested-carousel-slide">
            <img src="{{ asset('assets/img/tutorial/project/4.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('By clicking on a project, you will access the project details view, where you will see in table form, all the information related to the order forms and you will be able to delete a order form, edit it or sort the table by clicking on the header of the field you want to sort by. Relevant information is also displayed.') }}
            </p>
        </div>
        <div class="nested-carousel-slide">
            <img src="{{ asset('assets/img/tutorial/project/5.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('At the bottom, you can see the participants, the activity history of the project and you can also upload relevant files for the other participants.') }}
            </p>
        </div>
        <div class="nested-carousel-slide">
            <img src="{{ asset('assets/img/tutorial/project/6.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('From this view you can also access both the timesheet and the ordeb forms board.') }}</p>
        </div>
        <div class="nested-carousel-slide">
            <img src="{{ asset('assets/img/tutorial/project/7.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('In the order forms board, only those related to the project will be shown, you can view their status or modify them among others. In the next section (order forms) you will find more information about what you can do.') }}
            </p>
        </div>
    </div>
    <div class="nested-carousel-controls">
        <button class="nested-carousel-btn prev-btn">&lt;</button>
        <button class="nested-carousel-btn-right next-btn">&gt;</button>
    </div>
</div>

<style>
    .imgStyleSlide {
        width: 65% !important;
        margin-top: 2% !important;
        border-radius: 15px !important;
    }

    .pStyleSlide {
        font-size: 14px !important;
        text-align: center !important;
        /*padding-left: 15% !important; */
        /* padding-top: 5px !important; */
    }

    /* Horizontal carousel styles */
    .nested-carousel-container {
        position: relative;
        width: 100%;
        height: 500px;
        overflow: hidden;
        display: flex;
        /* Hide the carousel initially */
        justify-content: center;
        align-items: center;
    }

    .nested-carousel-container.active {
        display: flex;
        /* Show carousel only when parent slide is active */
    }

    .nested-carousel {
        display: flex;
        transition: transform 0.5s ease-in-out;
    }

    .nested-carousel-slide {
        flex: 0 0 100%;
        opacity: 0;
        /* Hidden by default */
        transition: opacity 0.5s ease-in-out;
    }

    .nested-carousel-slide.active {
        opacity: 1;
        /* Only active slide is visible */
        display: flex;
        color: white;
        justify-content: center;
        align-content: center;
        flex-direction: column;
        align-items: center;
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
        margin-left: 7% !important;
        font-size: 22px !important;
        width: 45px !important;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 25%;
        height: 40px !important;
        box-shadow: 0px 0px 20px 0px rgb(0 0 0 / 30%);
        -webkit-box-shadow: 0px 0px 20px 0px rgb(0 0 0 / 30%);
        -moz-box-shadow: 0px 0px 20px 0px rgb(0 0 0 / 30%);
    }

    .nested-carousel-btn-right {
        background-color: #aa182c !important;
        margin-right: 7% !important;
        font-size: 22px !important;
        width: 45px !important;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 25%;
        height: 40px !important;
        box-shadow: 0px 0px 20px 0px rgb(0 0 0 / 30%);
        -webkit-box-shadow: 0px 0px 20px 0px rgb(0 0 0 / 30%);
        -moz-box-shadow: 0px 0px 20px 0px rgb(0 0 0 / 30%);
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

            observer.observe(slide, {
                attributes: true,
                attributeFilter: ["class"]
            });
        });

        // Initialize nested carousels
        nestedCarousels.forEach((carousel) => {
            const nestedSlides = carousel.querySelectorAll(".nested-carousel-slide");
            const prevButton = carousel.closest(".nested-carousel-container").querySelector(
                ".nested-carousel-btn.prev-btn");
            const nextButton = carousel.closest(".nested-carousel-container").querySelector(
                ".nested-carousel-btn-right.next-btn");

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
