<script>
    window.embeddedChatbotConfig = {
        chatbotId: "W0AzRFvV2ANYgPmMkLL5c",
        domain: "www.chatbase.co"
    }
</script>

<script src="https://www.chatbase.co/embed.min.js" chatbotId="W0AzRFvV2ANYgPmMkLL5c" domain="www.chatbase.co" defer>
</script>

<style>
    /* CofriAI: 44px pegado a la esquina, en el margen libre bajo el contenido
       (las listas con scroll terminan 72px antes del final de la pantalla).
       !important: el script de Chatbase pone estilos en línea al botón. */
    #chatbase-bubble-button {
        position: fixed !important;
        width: 44px !important;
        height: 44px !important;
        bottom: 16px !important;
        right: 16px !important;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
    }

    #chatbase-bubble-button img,
    #chatbase-bubble-button svg {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain;
    }

    .notification {
        position: fixed;
        right: 60px;
        bottom: 50px;
        background-color: #AA182C;
        color: white;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
        white-space: nowrap;
        display: flex;
        align-items: center;
        z-index: 1000;
    }

    .notification .close-btn {
        background: none;
        border: none;
        color: #FFF;
        font-size: 20px;
        margin-left: 10px;
        cursor: pointer;
    }
</style>
<?php $currentUserLang = app()->getLocale(); ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var interval = setInterval(function() {
            var chatbaseBubbleButton = document.getElementById('chatbase-bubble-button');
            if (chatbaseBubbleButton) {
                // Check if the notification was recently closed
                var lastClosedTimestamp = localStorage.getItem('notificationClosedTimestamp');
                var currentTime = new Date().getTime();
                var twentyFourHoursInMilliseconds = 24 * 60 * 60 * 1000;

                if (lastClosedTimestamp && currentTime - lastClosedTimestamp <
                    twentyFourHoursInMilliseconds) {
                    clearInterval(interval);
                    return; // Do not show the notification if it was closed within the last 24 hours
                }

                // Create the notification container
                // var notification = document.createElement('div');
                // notification.className = 'notification';

                // // Create the text element
                // var notificationText = document.createElement('span');
                // var userLang = <?php echo $currentUserLang; ?>;
                // if (userLang == 'es') {
                //     notificationText.textContent = 'Soy CofriAI, tu asistente virtual';
                // } else if (userLang == 'en') {
                //     notificationText.textContent = "I'm CofriAI, your virtual assistant";
                // } else if (userLang == 'fr') {
                //     notificationText.textContent = "Je suis CofriAI, votre assistante virtuelle";
                // } 

                // Create the close button
                // var closeButton = document.createElement('button');
                // closeButton.className = 'close-btn';
                // closeButton.textContent = '×';

                // // Append the text and close button to the notification
                // notification.appendChild(notificationText);
                // notification.appendChild(closeButton);

                // // Append the notification to the body
                // document.body.appendChild(notification);

                // // Add event listener to the close button
                // closeButton.addEventListener('click', function() {
                //     notification.style.display = 'none';
                //     localStorage.setItem('notificationClosedTimestamp', new Date().getTime());
                // });

                // // Add event listener to the chat button to toggle position
                // chatbaseBubbleButton.addEventListener('click', function() {
                //     if (chatbaseBubbleButton.style.bottom === '30px') {
                //         chatbaseBubbleButton.style.bottom = '6px';
                //     } else {
                //         chatbaseBubbleButton.style.bottom = '30px';
                //     }
                // });

                // clearInterval(interval);
            }
        }, 100);
    });
</script>
