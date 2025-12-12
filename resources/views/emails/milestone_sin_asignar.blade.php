<p style="font-family: Arial, Helvetica, sans-serif; font-size:15px;">
    👋 Hola <strong>{{ $user->name }}</strong>,
</p>

<p style="font-family: Arial, Helvetica, sans-serif; font-size:15px;">
    ⚠️ La hoja de encargo
    <strong style="color:#AA182C;">{{ $milestone->title }}</strong>
    del proyecto
    <strong>{{ $milestone->project->name }}</strong>
    no ha sido asignada.
</p>

<p style="font-family: Arial, Helvetica, sans-serif; font-size:15px;">
    🔍 Por favor, revisa <strong>aCeler Project</strong>.
</p>

<hr style="margin:30px 0; border:none; border-top:1px dashed #999;">

<p style="font-family: Arial, Helvetica, sans-serif; font-size:15px;">
    👋 Hello <strong>{{ $user->name }}</strong>,
</p>

<p style="font-family: Arial, Helvetica, sans-serif; font-size:15px;">
    ⚠️ The milestone
    <strong style="color:#AA182C;">{{ $milestone->title }}</strong>
    of the project
    <strong>{{ $milestone->project->name }}</strong>
    has not been assigned.
</p>

<p style="font-family: Arial, Helvetica, sans-serif; font-size:15px;">
    🔍 Please check <strong>aCeler Project</strong>.
</p>
