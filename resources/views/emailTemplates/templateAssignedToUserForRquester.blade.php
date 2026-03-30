<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $emailSubject ?? 'Notificación en Aceler Project' }}</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; color:#333; line-height:1.6;">

    <!-- ===== ESPAÑOL ===== -->
    <div
        style="background-color:#f8f8f8; padding:15px; border-left:4px solid #AA182C; margin-bottom:20px; border-radius:4px;">
        <p style="margin:0; font-size:14px; color:#666;">
            <strong>ℹ️ Notificación:</strong> Se ha asignado una hoja de encargo a un miembro de tu equipo
        </p>
    </div>

    <h1 style="color:#AA182C; font-size:22px;">
        📢 Novedades en Aceler Project
    </h1>

    <p style="font-size:16px; font-weight:bold;">
        Se ha asignado uno de tus encargos.
    </p>

    <p style="font-size:15px;">
        📝 <strong>Encargo:</strong> {{ ucfirst($encargo) }}
    </p>

    <p style="font-size:15px;">
        👤 <strong>Asignado a:</strong> {{ ucfirst($empleado) }}
    </p>

    <p style="font-size:15px;">
        🔼 <strong>Prioridad:</strong>
        <span style="padding:4px 8px; background:#f2f2f2; border-radius:4px;">
            @if ($priority == null || $priority == '')
                Sin prioridad.
            @else
                {{ ucfirst($priority) }}
            @endif
        </span>
    </p>

    <p style="font-size:15px;">
        📁 <strong>Proyecto:</strong> {{ ucfirst($proyecto) }}
    </p>

    <p style="font-size:15px;">
        🗂️ <strong>Espacio de trabajo:</strong> {{ ucfirst($workspace) }}
    </p>

    <p style="font-size:15px;">
        📌 <strong>Estado:</strong>
        @if ($status == 1)
            Por hacer.
        @elseif ($status == 2)
            En curso.
        @else
            {{ ucfirst($status) }}
        @endif
    </p>


    <p style="font-size:15px;">
        📅 <strong>Entrega estimada:</strong> {{ ucfirst($fecha) }}
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
    <div
        style="background-color:#f8f8f8; padding:15px; border-left:4px solid #AA182C; margin-bottom:20px; border-radius:4px;">
        <p style="margin:0; font-size:14px; color:#666;">
            <strong>ℹ️ Notification:</strong> A work order has been assigned to a member of your team
        </p>
    </div>

    <h1 style="color:#AA182C; font-size:22px;">
        📢 Updates in Aceler Project
    </h1>

    <p style="font-size:16px; font-weight:bold;">
        One of your orders has been assigned.
    </p>

    <p style="font-size:15px;">
        📝 <strong>Order:</strong> {{ ucfirst($encargo) }}
    </p>

    <p style="font-size:15px;">
        👤 <strong>Assigned to:</strong> {{ ucfirst($empleado) }}
    </p>

    <p style="font-size:15px;">
        🔼 <strong>Priority:</strong>
        <span style="padding:4px 8px; background:#f2f2f2; border-radius:4px;">
            @if ($priority == null || $priority == '')
                No priority.
            @else
                {{ ucfirst($priority) }}
            @endif
        </span>
    </p>

    <p style="font-size:15px;">
        📁 <strong>Project:</strong> {{ ucfirst($proyecto) }}
    </p>

    <p style="font-size:15px;">
        🗂️ <strong>Workspace:</strong> {{ ucfirst($workspace) }}
    </p>

    <p style="font-size:15px;">
        📌 <strong>Status:</strong>
        @if ($status == 1)
            To do.
        @elseif ($status == 2)
            In progress.
        @else
            {{ ucfirst($status) }}
        @endif
    </p>

    <p style="font-size:15px;">
        📅 <strong>Estimated delivery:</strong> {{ ucfirst($fecha) }}
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
