<?php

/*
|--------------------------------------------------------------------------
| «Reportar un problema» (pie del menú)
|--------------------------------------------------------------------------
| Los informes de errores se ven en el perfil de los usuarios con users.is_support
| (sección «Informes de errores»; banderita en la foto de perfil si hay nuevos).
| Con PROBLEM_REPORT_MAIL=true también se envían por correo a esos usuarios.
| Desactivado por defecto para no mandar correos desde local.
*/

return [
    'mail' => (bool) env('PROBLEM_REPORT_MAIL', false),
];
