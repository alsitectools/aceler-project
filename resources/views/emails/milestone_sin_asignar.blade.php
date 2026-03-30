<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hay un encargo sin asignar en uno de tus proyectos en Aceler Project</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; color:#333; line-height:1.6;">

    <!-- ===== ESPAÑOL ===== -->
    <div
        style="background-color:#f8f8f8; padding:15px; border-left:4px solid #AA182C; margin-bottom:20px; border-radius:4px;">
        <p style="margin:0; font-size:14px; color:#666;">
            <strong>⚠️ Aviso:</strong> Hay una hoja de encargo sin asignar en un proyecto en el que participas
        </p>
    </div>

    <h1 style="color:#AA182C; font-size:22px;">
        📢 Novedades en Aceler Project
    </h1>

    <p style="font-size:16px; font-weight:bold;">
        Hola {{ $user->name }}, una hoja de encargo no ha sido asignada.
    </p>

    <p style="font-size:15px;">
        📝 <strong>Encargo:</strong> {{ ucfirst($milestone->title) }}
    </p>

    <p style="font-size:15px;">
        🔼 <strong>Prioridad:</strong>
        <span style="padding:4px 8px; background:#f2f2f2; border-radius:4px;">
            @if ($milestone->priority == null || $milestone->priority == '')
                Sin prioridad.
            @else
                {{ ucfirst($milestone->priority) }}
            @endif
        </span>
    </p>

    <p style="font-size:15px;">
        📁 <strong>Proyecto:</strong> {{ ucfirst($milestone->project->name) }}
    </p>

    <p style="font-size:15px;">
        🗂️ <strong>Espacio de trabajo:</strong> {{ ucfirst($milestone->project->workspaceData->name ?? '') }}
    </p>



    <p style="margin-top:20px;">
        🔗 <strong>Acceso al tablero:</strong>
        <a href="https://acelerproject.alsina.com/{{ $milestone->project->workspaceData->slug ?? '' }}/milestone-board/-1"
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
            <strong>⚠️ Notice:</strong> There is an unassigned work order in a project you participate in
        </p>
    </div>

    <h1 style="color:#AA182C; font-size:22px;">
        📢 Updates in Aceler Project
    </h1>

    <p style="font-size:16px; font-weight:bold;">
        Hello {{ $user->name }}, a work order has not been assigned.
    </p>

    <p style="font-size:15px;">
        📝 <strong>Order:</strong> {{ ucfirst($milestone->title) }}
    </p>

    <p style="font-size:15px;">
        🔼 <strong>Priority:</strong>
        <span style="padding:4px 8px; background:#f2f2f2; border-radius:4px;">
            @if ($milestone->priority == null || $milestone->priority == '')
                No priority.
            @else
                {{ ucfirst($milestone->priority) }}
            @endif
        </span>
    </p>

    <p style="font-size:15px;">
        📁 <strong>Project:</strong> {{ ucfirst($milestone->project->name) }}
    </p>

    <p style="font-size:15px;">
        🗂️ <strong>Workspace:</strong> {{ ucfirst($milestone->project->workspaceData->name ?? '') }}
    </p>



    <p style="margin-top:20px;">
        🔗 <strong>Board access:</strong>
        <a href="https://acelerproject.alsina.com/{{ $milestone->project->workspaceData->slug ?? '' }}/milestone-board/-1"
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
