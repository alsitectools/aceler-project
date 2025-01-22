<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .calendar-container {
            background: white;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 1px 10px rgb(0 0 0 / 20%);
            position: absolute;
            bottom: 11%;
            left: 40%;
            z-index: 1000;
            width: 22%;
        }

        .calendar-header {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            box-shadow: 0px 2px 5px 0px rgb(0 0 0 / 40%);
            -webkit-box-shadow: 0px 2px 5px 0px rgb(0 0 0 / 40%);
            -moz-box-shadow: 0px 2px 5px 0px rgb(0 0 0 / 40%);
            margin-bottom: 4%;
            height: 60px;
            border-radius: 5px;
        }

        .calendar-header button {
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            color: white;
            background-color: #aa182c;
            border-radius: 6px;
            height: 30px;
            width: 30px;
        }

        .calendar-header button:hover {
            color: #ffffff;
            text-decoration: none;
            background-color: #b9515f;
            border-color: #b9515f;
        }

        .calendar-header h3 {
            margin: 0;
            font-size: 16px;
        }

        .calendar-header span {
            color: #aa182c;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }

        .calendar-header span:hover {
            text-decoration: underline;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-day,
        .calendar-header-day {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            cursor: pointer;
        }

        .calendar-header-day {
            background: #aa182c;
            font-weight: bold;
            color: white;
        }
        .calendar-header-day:hover {
            color: #ffffff;
            text-decoration: none;
            background-color: #b9515f;
            border-color: #b9515f;
        }
        .calendar-day {
            background: #fff;
            border: 1px solid #ddd;
            color: #333;
        }

        .calendar-day:hover {
            background: #f0f0f0;
        }

        .calendar-day.selected {
            background: #aa182c;
            color: white;
        }

        .calendar-day.disabled {
            background: #f9f9f9;
            color: #ccc;
            pointer-events: none;
        }
        .titleDivDisplay{
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .month-dropdown {
            display: none;
            position: absolute;
            background: white;
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.2);
            -webkit-box-shadow: 0 1px 10px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 1px 10px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            list-style: none;
            padding: 5px 0;
            margin-top: 5px;
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
            width: 120px;
        }

        .month-dropdown li {
            padding: 8px 15px;
            cursor: pointer;
            font-size: 14px;
        }

        .month-dropdown li:hover {
            background-color: #aa182c;
            color: white;
        }
    </style>
</head>
<body>
    <div id="customCalendarParent" class="calendar-container" style="display: none;">
        <div class="calendar-header">
            <button id="prev-month">&lt;</button>
            <div class="titleDivDisplay">
                <h3 id="month-year" style="font-size: 20px;">Enero 2023</h3>
                <ul id="month-dropdown" class="month-dropdown"></ul>
                <span id="clear-selection">Borrar</span>
            </div>
            <button id="next-month">&gt;</button>
        </div>
        <div class="calendar-grid" id="calendar-grid"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const calendarGrid = document.getElementById("calendar-grid");
            const monthYearDisplay = document.getElementById("month-year");
            const prevMonthButton = document.getElementById("prev-month");
            const nextMonthButton = document.getElementById("next-month");
            const clearSelectionButton = document.getElementById("clear-selection");
            const monthDropdown = document.getElementById("month-dropdown");

            const today = new Date();
            let currentYear = today.getFullYear();
            let currentMonth = today.getMonth();
            let startDate = null;
            let endDate = null;

            const daysOfWeek = ["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];
            const monthNames = [
                "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ];

            let selectedDates = [];

            let range = [];

            // Crear el desplegable de meses
            monthNames.forEach((month, index) => {
                const li = document.createElement("li");
                li.textContent = month;
                li.dataset.month = index;
                li.addEventListener("click", function () {
                    currentMonth = parseInt(this.dataset.month);
                    generateCalendar(currentYear, currentMonth);
                    monthDropdown.style.display = "none";
                });
                monthDropdown.appendChild(li);
            });

            function formatDateToISO(date) {
                let year = date.getFullYear();
                let month = (date.getMonth() + 1).toString().padStart(2, '0'); // Ensure 2 digits
                let day = date.getDate().toString().padStart(2, '0'); // Ensure 2 digits
                return `${year}-${month}-${day}`;
            }

            function generateCalendar(year, month) {
                calendarGrid.innerHTML = "";

                monthYearDisplay.textContent = `${monthNames[month]} ${year}`;

                daysOfWeek.forEach((day, index) => {
                    const headerDay = document.createElement("div");
                    headerDay.classList.add("calendar-header-day");
                    headerDay.textContent = day;
                    headerDay.dataset.dayIndex = index;
                    headerDay.addEventListener("click", function () {
                        selectAllDaysOfWeek(index);
                    });
                    calendarGrid.appendChild(headerDay);
                });

                const firstDayOfMonth = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                const emptyDays = (firstDayOfMonth + 6) % 7;
                for (let i = 0; i < emptyDays; i++) {
                    const emptyDiv = document.createElement("div");
                    emptyDiv.classList.add("calendar-day", "disabled");
                    calendarGrid.appendChild(emptyDiv);
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const date = new Date(year, month, day);
                    const dayElement = document.createElement("div");
                    dayElement.classList.add("calendar-day");
                    dayElement.textContent = day;
                    dayElement.dataset.date = formatDateToISO(date);
                    dayElement.dataset.dayIndex = (date.getDay() + 6) % 7; // Ajustar inicio a lunes

                    if (date < today) {
                        dayElement.classList.add("disabled");
                    }

                    if (selectedDates.includes(dayElement.dataset.date)) {
                        dayElement.classList.add("selected");
                    }

                    dayElement.addEventListener("click", function () {
                        handleDayClick(this);
                    });

                    calendarGrid.appendChild(dayElement);
                }
            }

            function handleDayClick(dayElement) {
                console.log("dayElement", dayElement);
                const selectedDate = dayElement.dataset.date;

                console.log("selectedDate", selectedDate);

                if (!startDate) {
                    startDate = selectedDate;
                    dayElement.classList.add("selected");
                    selectedDates = [startDate];
                } else if (!endDate) {
                    endDate = selectedDate;

                    // Asegurar el orden correcto de fechas
                    if (new Date(startDate) > new Date(endDate)) {
                        [startDate, endDate] = [endDate, startDate];
                    }

                    console.log("startDate", startDate);
                    range = getDateRange(new Date(startDate), new Date(endDate));
                    console.log("range", range);
                    highlightRange(range);

                    //save range to a localstorage
                    localStorage.setItem('DateSelectedRange', JSON.stringify(range));
                } else {
                    clearSelection();
                    startDate = selectedDate;
                    endDate = null;
                    dayElement.classList.add("selected");
                    selectedDates = [startDate];
                }
            }

            function selectAllDaysOfWeek(dayIndex) {
                let newSelectedDates = [];

                document.querySelectorAll(".calendar-day").forEach(day => {
                    if (parseInt(day.dataset.dayIndex) === dayIndex) {
                        if (!day.classList.contains("selected")) {
                            day.classList.add("selected");
                            selectedDates.push(day.dataset.date);
                            newSelectedDates.push(day.dataset.date);
                        } else {
                            day.classList.remove("selected");
                            selectedDates = selectedDates.filter(date => date !== day.dataset.date);
                        }
                    }
                });

                // Add the new selected dates to the range array and remove duplicates
                range = [...new Set([...range, ...newSelectedDates])];

                // Save updated range to local storage
                localStorage.setItem('DateSelectedRange', JSON.stringify(range));

                console.log("Updated range:", range);
            }


            function getDateRange(start, end) {
                let range = [];
                let currentDate = new Date(start);

                while (currentDate <= end) {
                    range.push(currentDate.toISOString().split("T")[0]);
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                return range;
            }

            function highlightRange(range) {
                selectedDates = range;
                document.querySelectorAll(".calendar-day").forEach(day => {
                    if (selectedDates.includes(day.dataset.date)) {
                        day.classList.add("selected");
                    }
                });
            }

            function clearSelection() {
                selectedDates = [];
                range = [];
                startDate = null;
                endDate = null;
                document.querySelectorAll(".calendar-day").forEach(day => {
                    day.classList.remove("selected");
                });
                localStorage.removeItem('DateSelectedRange');
                console.log("Selections cleared.");
            }


            prevMonthButton.addEventListener("click", function () {
                if (currentMonth === 0) {
                    currentMonth = 11;
                    currentYear--;
                } else {
                    currentMonth--;
                }
                generateCalendar(currentYear, currentMonth);
            });

            nextMonthButton.addEventListener("click", function () {
                if (currentMonth === 11) {
                    currentMonth = 0;
                    currentYear++;
                } else {
                    currentMonth++;
                }
                generateCalendar(currentYear, currentMonth);
            });

            clearSelectionButton.addEventListener("click", clearSelection);

            generateCalendar(currentYear, currentMonth);

            // Mostrar u ocultar el dropdown al hacer clic en el nombre del mes
            monthYearDisplay.addEventListener("click", function () {
                monthDropdown.style.display = monthDropdown.style.display === "block" ? "none" : "block";
            });

            // Cerrar el dropdown si se hace clic fuera de él
            document.addEventListener("click", function (e) {
                if (!monthYearDisplay.contains(e.target) && !monthDropdown.contains(e.target)) {
                    monthDropdown.style.display = "none";
                }
            });
    });
</script>


</body>
</html>
