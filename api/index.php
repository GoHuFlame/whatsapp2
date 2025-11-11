<?php

$apiUrl = "https://morakz.com/api/text";
$token = getenv('WHATSAPP_API_TOKEN'); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $numero = trim($_POST["numero"]);
    $mensaje = trim($_POST["mensaje"]);

    if (!preg_match('/^[0-9]{10}$/', $numero)) {
        $resultado = "<p style='color:red; text-align:center;'>❌ El número debe tener exactamente 10 dígitos (solo números).</p>";
    } elseif (empty($mensaje)) {
        $resultado = "<p style='color:red; text-align:center;'>❌ Debes escribir un mensaje.</p>";
    } else {
        $numeroCompleto = "521" . $numero;

        $payload = [
            "to" => $numeroCompleto,
            "body" => $mensaje
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curl_error) {
            $resultado = "<p style='color:red; text-align:center;'>❌ Error de conexión: " . htmlspecialchars($curl_error) . "</p>";
        } elseif ($http_code == 200 || $http_code == 201) {
            $resultado = "<p style='color:green; text-align:center;'>✅ Mensaje enviado correctamente.</p>";
        } else {
            $error_detail = "";
            if ($http_code == 403) {
                $error_detail = " (Acceso denegado - verifica el token de API)";
            } elseif ($http_code == 401) {
                $error_detail = " (No autorizado - token inválido)";
            }
            $resultado = "<p style='color:red; text-align:center;'>❌ Error al enviar mensaje. Código HTTP: $http_code$error_detail</p>";
            if ($response) {
                $resultado .= "<p style='color:red; text-align:center; font-size:12px;'>Respuesta: " . htmlspecialchars(substr($response, 0, 200)) . "</p>";
            }
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
