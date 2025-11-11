<?php

$urlApi = "https://morakz.com/api/text";
$token = getenv('WHATSAPP_API_TOKEN'); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $numero = trim($_POST["numero"]);
    $mensaje = trim($_POST["mensaje"]);

    if (!preg_match('/^[0-9]{10}$/', $numero)) {
        $resultado = "<p style='color:red; text-align:center;'>❌ El número debe tener exactamente 10 dígitos.</p>";
    } elseif (empty($mensaje)) {
        $resultado = "<p style='color:red; text-align:center;'>❌ Debes escribir un mensaje.</p>";
    } else {
        $numeroCompleto = "521" . $numero;

        $datos = [
            "to" => $numeroCompleto,
            "body" => $mensaje
        ];

        $curl = curl_init($urlApi);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);

        $respuesta = curl_exec($curl);
        $codigoHttp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($codigoHttp == 200 || $codigoHttp == 201) {
            $resultado = "<p style='color:green; text-align:center;'>✅ Mensaje enviado correctamente.</p>";
        } else {
            $resultado = "<p style='color:red; text-align:center;'>❌ Error al enviar mensaje. Código HTTP: $codigoHttp</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Enviar WhatsApp</title>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background: #f3f4f6;
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container {
        background: #ffffff;
        padding: 30px 35px;
        border-radius: 10px;
        width: 380px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        text-align: center;
    }

    h2 {
        color: #333;
        margin-bottom: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

    label {
        font-weight: bold;
        text-align: left;
        margin-top: 12px;
    }

    input, textarea {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
        resize: none;
        transition: border-color 0.3s;
    }

    input:focus, textarea:focus {
        border-color: #25D366;
        outline: none;
    }

    button {
        background: #25D366;
        color: white;
        border: none;
        padding: 12px;
        cursor: pointer;
        border-radius: 6px;
        font-size: 16px;
        margin-top: 18px;
        transition: background 0.3s ease;
    }

    button:hover {
        background: #1ebe5d;
    }
</style>
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
