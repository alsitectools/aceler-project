<!DOCTYPE html>
<html>
<head>
    <style>
        /* Estilización del icono de chat */
        #chat-icon {
            z-index: 1000;
            position: fixed;
            bottom: 20px;
            right: 4%;
            cursor: pointer;
            width: 50px;
            height: 50px;
            background-color: #AA182C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Estilización del widget de chat */
        #copilot-chat-widget {
            z-index: 10001;
            position: fixed;
            bottom: 80px;
            right: 5%;
            width: 25%;
            height: 60%;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgb(0 0 0 / 48%);
            display: none;
        }

        /* Estilización adicional del iframe */
        #copilot-chat-widget iframe {
            border: none; /* Eliminar el borde del iframe */
            border-radius: 10px; /* Bordes redondeados del iframe */
        }
    </style>
</head>
<body>
    <!-- Icono de Chat -->
    <div id="chat-icon" onclick="toggleChat()">
        <img src="{{ asset('assets/images/chatbot/croki.png') }}"/>
    </div>

    <!-- Widget de Chat (iframe) -->
    <div id="copilot-chat-widget">
        <iframe src="https://copilotstudio.microsoft.com/environments/Default-0c589ca1-4ef4-4632-a97f-69301c649eda/bots/cref0_emperorOfHumankind/webchat?__version__=2"
                style="width: 100%; height: 100%; border: none; border-radius: 10px;">
        </iframe>
    </div>

    <script>
        // Función para mostrar/ocultar el widget de chat
        function toggleChat() {
            var chatWidget = document.getElementById("copilot-chat-widget");
            if (chatWidget.style.display === "none" || chatWidget.style.display === "") {
                chatWidget.style.display = "block"; // Mostrar el chat
            } else {
                chatWidget.style.display = "none"; // Ocultar el chat
            }
        }
    </script>
</body>
</html>
