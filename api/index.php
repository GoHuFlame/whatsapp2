<?php

$urlApi = "https://morakz.com/api/text";
$tokenApi = getenv('WHATSAPP_API_TOKEN');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $numero = trim($_POST["numero"]);
    $mensaje = trim($_POST["mensaje"]);
    $numeroCompleto = "521" . $numero;

    $datosEnvio = [
        "to" => $numeroCompleto,
        "body" => $mensaje
    ];

    $solicitudCurl = curl_init($urlApi);
    curl_setopt($solicitudCurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($solicitudCurl, CURLOPT_POST, true);
    curl_setopt($solicitudCurl, CURLOPT_POSTFIELDS, json_encode($datosEnvio));
    curl_setopt($solicitudCurl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $tokenApi",
        "Content-Type: application/json"
    ]);

    curl_exec($solicitudCurl);
    curl_close($solicitudCurl);

    $resultado = "<p style='color:green; text-align:center;'>✅ Mensaje enviado.</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Enviar WhatsApp</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h2>Enviar mensaje WhatsApp</h2>
    <form method="POST">
        <label>Número</label>
        <input type="text" name="numero" maxlength="10" pattern="[0-9]{10}" required>

        <label>Mensaje</label>
        <textarea name="mensaje" rows="4" required></textarea>

        <button type="submit">Enviar mensaje</button>
    </form>

    <?php if (isset($resultado)) echo "<div style='margin-top:20px;'>$resultado</div>"; ?>
</div>

</body>
</html>
