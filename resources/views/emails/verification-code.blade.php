<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Verification Code</title>
</head>

<body style="font-family: Arial, sans-serif;">
    <h2>Hi!</h2>
    <p>Your verification code is:</p>

    <h1 style="font-size: 32px; letter-spacing: 4px; color: #2c3e50;">{{ $code }}</h1>

    <p>This code will expire in 10 minutes.</p>
    <p>Thank you, <br>{{ config('app.name') }}</p>
</body>

</html>
