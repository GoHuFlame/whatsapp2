<?php

$urlApi = "https://morakz.com/api/text";
$token = "mkuzfO8C0CXeE68ziJ4Rm0EwvaH49Ajh"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $numero = trim($_POST["numero"]);
    $mensaje = trim($_POST["mensaje"]);

    if (!preg_match('/^[0-9]{10}$/', $numero)) {
        $resultado = "<p class='mensaje-error'>❌ El número debe tener exactamente 10 dígitos.</p>";
    } elseif (empty($mensaje)) {
        $resultado = "<p class='mensaje-error'>❌ Debes escribir un mensaje.</p>";
    } else {
        $numeroCompleto = "521" . $numero;

        $datos = [
            "to" => $numeroCompleto,
            "body" => $mensaje
        ];

        $ch = curl_init($urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);

        $respuesta = curl_exec($ch);
        $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($codigoHttp == 200 || $codigoHttp == 201) {
            $resultado = "<p class='mensaje-exito'>✅ Mensaje enviado correctamente.</p>";
        } else {
            $resultado = "<p class='mensaje-error'>❌ Error al enviar mensaje. Código HTTP: $codigoHttp</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enviar WhatsApp</title>
<link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="contenedor">
    <h2>Enviar mensaje WhatsApp</h2>
    <form method="POST">
        <label>Número</label>
        <input type="text" name="numero" maxlength="10" pattern="[0-9]{10}" required>

        <label>Mensaje</label>
        <textarea name="mensaje" rows="4" required></textarea>

        <button type="submit">Enviar mensaje</button>
    </form>

    <?php if (isset($resultado)) echo "<div class='resultado'>$resultado</div>"; ?>
</div>

</body>
</html>
