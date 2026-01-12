<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Aceler Project Notification</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; color:#333; line-height:1.6">

    <!-- ===== ESPAÑOL ===== -->
    <div style="background-color:#f8f8f8; padding:15px; border-left:4px solid #AA182C; margin-bottom:20px; border-radius:4px;">
        <p style="margin:0; font-size:14px; color:#666;">
            <strong>ℹ️ Notificación:</strong> Te han asignado una nueva hoja de encargo
        </p>
    </div>

    <h1 style="color:#AA182C; font-size:22px;">
        📢 Novedades en Aceler Project
    </h1>

    <p style="font-size:15px;">
        📝 <strong>Encargo:</strong> {{ ucfirst($encargo) }}
    </p>

    <p style="font-size:15px;">
        🔼 <strong>Prioridad:</strong>
        <span style="padding:4px 8px; background:#f2f2f2; border-radius:4px;">
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
        📅 <strong>Entrega estimada:</strong> {{ $fecha }}
    </p>

    <p style="margin-top:20px;">
        🔗 <strong>Acceso al tablero:</strong>
        <a href="https://acelerproject.alsina.com/{{ $slug }}/milestone-board/-1" style="color:#AA182C;">
            haz clic aquí
        </a>
        <br>
        <small>(requiere conexión a la red Alsina)</small>
    </p>

    <hr style="margin:30px 0;">

    <!-- ===== ENGLISH ===== -->
    <div style="background-color:#f8f8f8; padding:15px; border-left:4px solid #AA182C; margin-bottom:20px; border-radius:4px;">
        <p style="margin:0; font-size:14px; color:#666;">
            <strong>ℹ️ Notification:</strong> You have been assigned a new work order
        </p>
    </div>

    <h1 style="color:#AA182C; font-size:22px;">
        📢 Updates in Aceler Project
    </h1>

    <p style="font-size:15px;">
        📝 <strong>Order:</strong> {{ ucfirst($encargo) }}
    </p>

    <p style="font-size:15px;">
        🔼 <strong>Priority:</strong>
        <span style="padding:4px 8px; background:#f2f2f2; border-radius:4px;">
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
        📅 <strong>Estimated delivery:</strong> {{ $fecha }}
    </p>

    <p style="margin-top:20px;">
        🔗 <strong>Board access:</strong>
        <a href="https://acelerproject.alsina.com/{{ $slug }}/milestone-board/-1" style="color:#AA182C;">
            haz clic aquí
        </a>
        <br>
        <small>(Alsina network connection required)</small>
    </p>

    <p style="margin-top:30px;">
        Regards<br>
        <strong>Aceler Project</strong>
    </p>

</body>

</html>
