<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    Hola {{ $name }},
    <br>
    Bienvenido al sistema Operwork, por favor verifique su cuenta de correo electronico para finalizar el proceso de creacion de cuenta.
    <br>
    Por favor haga click en el siguiente link, o copie el link en la barra de su explorador web:
    <br>

    <a href="{{ url('user/verify', $verification_code)}}">Confirmar cuenta de correo electronico</a>

    <br/>
</div>

</body>
</html>
