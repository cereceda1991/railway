<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

    <link href="https://emoji-css.afeld.me/emoji.css" rel="stylesheet">
</head>
<body>
    <div class="card text-center">
        <div class="card-header">
            <h3 class="text-secondary">CertifyMe
                <span class="fs-5"><i class="em em-globe_with_meridians mb-2" aria-role="presentation" aria-label="GLOBE WITH MERIDIANS"></i></span>
            </h3>
        </div>
        <div class="card-body">
            <h5 class="card-title">¡Hola!</h5>
            <p class="card-text">Hola, se solicitó un restablecimiento de contraseña para tu cuenta 
                <strong>{{$email}}</strong>, 
                haz clic en el botón que aparece a continuación para cambiar tu contraseña.
            </p>
            <a class="btn btn-primary" href='http://localhost:8000/response-password-reset?token={{$token}}'>Cambiar contraseña</a>
            <br><br>
            <p>
                Si tu no realizaste la solicitud de cambio de contraseña, solo ignora este mensaje.
            </p>
        </div>
        <div class="card-footer text-body-secondary">
            Saludos, CertifyMe
        </div>
    </div>
    
</body>
</html>