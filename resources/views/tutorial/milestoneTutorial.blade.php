<div class="nested-carousel-milestone-container-milestone">
    <div class="nested-carousel-milestone">
        <div class="nested-carousel-milestone-slide active">
            <img src="{{ asset('assets/img/tutorial/milestone/1.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('This board is divided into four phases, each representing a key stage in the workflow:') }}
            <p class="pStyleSlide">
                {{ __("To Do: This is where you will find a list of tasks that have been created but don't have any assignments yet. It's the starting point for organizing your priorities.") }}
            </p>
            <p class="pStyleSlide">{{ __("In Progress: This column shows the tasks you're actively working on.") }}</p>
            <p class="pStyleSlide">{{ __('Review: Completed tasks that need to be checked or approved are here.') }}</p>
            <p class="pStyleSlide">{{ __('Done: This column contains all completed and reviewed tasks.') }}</p>
            <p class="pStyleSlide">
                {{ __('Please note that for an order form to be reviewed, all of its tasks (regardless of who created them) must have hours allocated to them.') }}
            </p>
            </p>
        </div>
        <div class="nested-carousel-milestone-slide">
            <img src="{{ asset('assets/img/tutorial/milestone/8.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('When you click Create Task, this menu will open, allowing you to create a task. ') }}</p>
        </div>
        <div class="nested-carousel-milestone-slide">
            <img src="{{ asset('assets/img/tutorial/milestone/2.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('If you want to view information about an order form, you can click on the name to display a menu showing all the information related to that assignment.') }}
            </p>
        </div>
        <div class="nested-carousel-milestone-slide">
            <img src="{{ asset('assets/img/tutorial/milestone/3.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('Clicking on the three dots will also display all the order options, which we will now go into in detail, except for delete, which, as its name suggests, deletes the order and all its tasks') }}
            </p>
        </div>
        <div class="nested-carousel-milestone-slide">
            <img src="{{ asset('assets/img/tutorial/milestone/4.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('When you select Assign Assignment, a window will appear where you can assign an assignment to anyone. You can also click the Unassign button if you want the assignment to be available to all participants again.') }}
            </p>
        </div>
        <div class="nested-carousel-milestone-slide">
            <img src="{{ asset('assets/img/tutorial/milestone/5.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('When you click Add Task, you can create a task according to the type of project selected.') }}
            </p>
        </div>
        <div class="nested-carousel-milestone-slide">
            <img src="{{ asset('assets/img/tutorial/milestone/6.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('Clicking Edit Assignment opens the edit menu, where you can upload or delete files, modify the desired delivery date, or edit the description.') }}
            </p>
        </div>
        <div class="nested-carousel-milestone-slide">
            <img src="{{ asset('assets/img/tutorial/milestone/7.png') }}" class="imgStyleSlide" />
            <p class="pStyleSlide">
                {{ __('After clicking on a task you have previously created, you can also allocate hours to it or, if you prefer, delete it. Keep in mind that deleting a task will also delete all the hours allocated to it.') }}
            </p>
        </div>
    </div>
    <div class="nested-carousel-milestone-controls">
        <button class="nested-carousel-milestone-btn prev-btn">&lt;</button>
        <button class="nested-carousel-milestone-btn-right next-btn">&gt;</button>
    </div>
</div>

<style>
    .imgStyleSlide {
        width: 65% !important;
        margin-top: 10% !important;
        border-radius: 15px !important;
    }

    .pStyleSlide {
        font-size: 14px !important;
        text-align: center !important;
        /* padding-left: 15% !important; */
        /* padding: 5px !important; */
    }

    /* Horizontal carousel styles */
    .nested-carousel-milestone-container-milestone {
        position: relative;
        width: 100%;
        height: 700px;
        overflow: hidden;
        display: flex;
        /* Hide the carousel initially */
        justify-content: center;
        align-items: center;
    }

    .nested-carousel-milestone-container-milestone.active {
        display: flex;
        /* Show carousel only when parent slide is active */
    }

    .nested-carousel-milestone {
        display: flex;
        transition: transform 0.5s ease-in-out;
    }

    .nested-carousel-milestone-slide {
        flex: 0 0 100%;
        opacity: 0;
        /* Hidden by default */
        transition: opacity 0.5s ease-in-out;
    }

    .nested-carousel-milestone-slide.active {
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

    .nested-carousel-milestone-btn-right {
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
            const nestedCarouselContainer = slide.querySelector(
                ".nested-carousel-milestone-container-milestone");

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
            const nestedSlides = carousel.querySelectorAll(".nested-carousel-milestone-slide");
            const prevButton = carousel.closest(".nested-carousel-milestone-container-milestone")
                .querySelector(".nested-carousel-milestone-btn.prev-btn");
            const nextButton = carousel.closest(".nested-carousel-milestone-container-milestone")
                .querySelector(".nested-carousel-milestone-btn-right.next-btn");

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
