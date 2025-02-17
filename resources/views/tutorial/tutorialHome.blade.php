@extends('layouts.admin')

<style>
    .tutorialPartContainer {
        display: flex;
        justify-content: space-between;
    }

    .tutorialPartCard {
        display: flex;
        align-items: center;
        flex-direction: column;
        width: 33%;
        height: 570px;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        cursor: pointer;
        transition: width 0.5s ease-in-out;
    }

    .tutorialPartCardTitle {
        width: 131px;
        height: 40px;
        background-color: white;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        display: flex;
        align-content: center;
        justify-content: center;
        align-items: center;
        font-weight: bold;
    }

    .tutorialPartCardTitle h2 {
        color: #980200;
        font-size: x-large;
        font-weight: 800;
    }

    .tutorialPartCardContent {
        background-color: #0000004d;
        height: 150px;
        width: 350px;
        position: relative;
        top: 18%;
        border: 1px solid black;
        display: flex;
        border-radius: 13px;
        justify-content: center;
        align-items: center;
        box-shadow: 0px 8px 2px rgb(0 0 0 / 22%);
    }

    .tutorialPartCardContent span {
        color: white;
        text-align: center;
        padding: 10px;
        font-size: medium;
    }

    /* Ocultar el contenido por defecto */
    .hiddenTuto {
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .projectTutorials {
        background-image: url("{{ asset('assets/img/backgroundTutorialProject.png') }}");
    }

    .milestoneTutorials {
        background-image: url("{{ asset('assets/img/backgroundTutorialTasksMilestones.png') }}");
    }

    .tasksTutorials {
        background-image: url("{{ asset('assets/img/backgroundTutorialTasks.png') }}");
    }

    .slideIsActive {
        box-shadow: 10px 10px 5px 0px rgba(0, 0, 0, 0.75);
        -webkit-box-shadow: 0px 0px 20px 2px rgb(0 0 0 / 50%);
        -moz-box-shadow: 10px 10px 5px 0px rgba(0, 0, 0, 0.75);
        height: 595px;
        margin-top: -15px;
        width: 80% !important;
    }

</style>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="tutorialPartContainer ctr">
                <div class="projectTutorials tutorialPartCard" id="projectTutorial" onclick="expandCard(this, event)">
                    <div class="tutorialPartCardTitle">
                        <h2>{{ __('Projects') }}</h2>
                    </div>
                    <div class="tutorialPartCardContent">
                        <span
                            id="defaultTextProject">{{ __('Learn how to create, delete and visualize projects of any type') }}</span>
                    </div>
                    <div class="hiddenTuto" id="hiddenTutoProject">@include('tutorial.projectTutorial')</div>
                </div>
                <div class="milestoneTutorials tutorialPartCard" id="milestoneTutorials" onclick="expandCard(this, event)">
                    <div class="tutorialPartCardTitle">
                        <h2>{{ __('Milestones') }}</h2>
                    </div>
                    <div class="tutorialPartCardContent">
                        <span
                            id="defaultTextMilestone">{{ __('Learn how to create, delete and edit job sheets, as well as view their current status') }}</span>
                    </div>
                    <div class="hiddenTuto" id="hiddenTutoMilestone">@include('tutorial.milestoneTutorial')</div>
                </div>
                <div class="tasksTutorials tutorialPartCard" id="tasksTutorials" onclick="expandCard(this, event)">
                    <div class="tutorialPartCardTitle">
                        <h2>{{ __('Tasks') }}</h2>
                    </div>
                    <div class="tutorialPartCardContent">
                        <span id="defaultTextTask">{{ __('Learn how to input the hours dedicated to each task') }}</span>
                    </div>
                    <div class="hiddenTuto" id="hiddenTutoTask">@include('tutorial.taskTutorial')</div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function expandCard(clickedCard, event) {
        event.stopPropagation(); // Evita que el clic afecte otros elementos

        const cards = document.querySelectorAll('.tutorialPartCard');
        const hiddenTutos = document.querySelectorAll('.hiddenTuto');

        cards.forEach(card => {
            const hiddenTuto = card.querySelector('.hiddenTuto');
            const defaultText = card.querySelector('.tutorialPartCardContent span');
            const defaultCardContent = card.querySelector('.tutorialPartCardContent');

            if (card === clickedCard) {
                //Modifying card class (style) when is clicked
                card.classList.add('slideIsActive');

                if (hiddenTuto) hiddenTuto.style.display = 'flex'; // Mostramos el contenido
                if (defaultText) defaultText.style.display = 'none'; // Ocultamos el texto original

            } else {
                //Modifying card class (style) when is not clicked
                card.classList.remove('slideIsActive');

                card.style.width = '10%'; // Reducimos las otras tarjetas

                if (hiddenTuto) hiddenTuto.style.display = 'none'; // Ocultamos su contenido
                if (defaultText) defaultText.style.display = 'none'; // Aseguramos que el texto no se muestre
                if (defaultCardContent) defaultCardContent.style.display = 'none';
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        const slides = document.querySelectorAll(".carousel-slide");
        const carousel = document.querySelector(".carousel");

        // Imagen de fondo por defecto
        const defaultImageUrl = "{{ asset('assets/img/backgroundTutorial/img mix large size.png') }}";
        carousel.style.backgroundImage = `url('${defaultImageUrl}')`;

        // Agregamos eventos a cada slide
        slides.forEach((slide) => {
            slide.addEventListener("click", () => {
                slides.forEach((s) => s.classList.remove("active"));
                slide.classList.add("active");

                // Cambiamos la imagen de fondo
                const imageUrl = slide.getAttribute("data-image");
                carousel.style.backgroundImage = `url('${imageUrl}')`;
            });
        });
    });
</script>
