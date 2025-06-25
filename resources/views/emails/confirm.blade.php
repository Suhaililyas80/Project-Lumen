<!DOCTYPE html>
<html>
<head>
    <title>Test Mail</title>
</head>
<body>
    <h1>Hello, {{ $name }}</h1>
    <p>Please click the link below to verify your email:</p>
    <p><a href="{{ $verification_link }}">Verify Email</a></p>
    <p>Thank you!</p>
</body>
</html>