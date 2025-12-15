<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Aceler Project Notification</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; color:#333; line-height:1.6;">

    <!-- ===== ESPAÑOL ===== -->
    <h1 style="color:#AA182C; font-size:22px;">
        📢 Novedades en Aceler Project
    </h1>

    <p style="font-size:15px;">
        📝 <strong>Encargo:</strong> {{ ucfirst($encargo) }}
    </p>

    <p style="font-size:15px;">
        🔼 <strong>Prioridad:</strong>
        <span style="padding:4px 8px; background:#f2f2f2; border-radius:4px;">
            @if($priority == null || $priority == '')
                Sin prioridad.
            @else
                {{ ucfirst($priority) }}
        </span>
    </p>

    <p style="font-size:15px;">
        📁 <strong>Proyecto:</strong> {{ ucfirst($proyecto) }}
    </p>

    <p style="font-size:15px;">
        🗂️ <strong>Espacio de trabajo:</strong> {{ ucfirst($workspace) }}
    </p>

    <p style="font-size:15px;">
        📌 <strong>Estado:</strong> Pendiente de revisión.
    </p>

    <p style="margin-top:20px;">
        🔗 <strong>Acceso al tablero:</strong>
        <a href="https://acelerproject.alsina.com/{{ $slug }}/milestone-board/-1"
           style="color:#AA182C; text-decoration:none;">
            haz clic aquí
        </a>
        <br>
        <small style="color:#777;">(requiere conexión a la red Alsina)</small>
    </p>

    <hr style="margin:30px 0; border:none; border-top:1px solid #ddd;">

    <!-- ===== ENGLISH ===== -->
    <h1 style="color:#AA182C; font-size:22px;">
        📢 Updates in Aceler Project
    </h1>

    <p style="font-size:15px;">
        📝 <strong>Order:</strong> {{ ucfirst($encargo) }}
    </p>

    <p style="font-size:15px;">
        🔼 <strong>Priority:</strong>
        <span style="padding:4px 8px; background:#f2f2f2; border-radius:4px;">
            @if($priority == null || $priority == '')
                No priority.
            @else
                {{ ucfirst($priority) }}
        </span>
    </p>

    <p style="font-size:15px;">
        📁 <strong>Project:</strong> {{ ucfirst($proyecto) }}
    </p>

    <p style="font-size:15px;">
        🗂️ <strong>Workspace:</strong> {{ ucfirst($workspace) }}
    </p>

    <p style="font-size:15px;">
        📌 <strong>Status:</strong> Pending review.
    </p>

    <p style="margin-top:20px;">
        🔗 <strong>Board access:</strong>
        <a href="https://acelerproject.alsina.com/{{ $slug }}/milestone-board/-1"
           style="color:#AA182C; text-decoration:none;">
            click here
        </a>
        <br>
        <small style="color:#777;">(Alsina network connection required)</small>
    </p>

    <p style="margin-top:30px;">
        Regards,<br>
        <strong>Aceler Project</strong>
    </p>

</body>
</html>
