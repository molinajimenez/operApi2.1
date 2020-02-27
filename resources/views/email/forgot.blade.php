<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    Hola {{ $name }},
    <br>
    Este es el servicio de reinicio de contraseña, si usted no solicito este servicio, haga caso omiso a este correo.
    <br>
    Por favor haga click en el siguiente link, o copie el link en la barra de su explorador web:
    <br>
    <!-- Aqui va la ruta de front end, carga componente que manda post a server para reseteo de password -->
    <a href="{{ url('http://localhost:4200/auth/forgotPassword') ."?". http_build_query(["token" => $verification_code])}}">Reiniciar contraseña</a>

    <br/>
</div>

</body>
</html>
