<script>
    window.embeddedChatbotConfig = {
        chatbotId: "W0AzRFvV2ANYgPmMkLL5c",
        domain: "www.chatbase.co"
    }
</script>

<script src="https://www.chatbase.co/embed.min.js" chatbotId="W0AzRFvV2ANYgPmMkLL5c" domain="www.chatbase.co" defer>
</script>

<style>
    #chatbase-bubble-button {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 30px;
        right: 40px;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
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
