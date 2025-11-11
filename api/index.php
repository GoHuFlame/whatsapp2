<?php
$urlApi = "https://morakz.com/api/text";
$token = "mkuzfO8C0CXeE68ziJ4Rm0EwvaH49Ajh"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header('Content-Type: application/json');
    
    $numero = trim($_POST["numero"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    if (!preg_match('/^[0-9]{10}$/', $numero)) {
        http_response_code(400);
        echo json_encode([
            "exito" => false,
            "mensaje" => "El número debe tener exactamente 10 dígitos."
        ]);
        exit;
    }
    
    if (empty($mensaje)) {
        http_response_code(400);
        echo json_encode([
            "exito" => false,
            "mensaje" => "Debes escribir un mensaje."
        ]);
        exit;
    }

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
        echo json_encode([
            "exito" => true,
            "mensaje" => "Mensaje enviado correctamente."
        ]);
    } else {
        http_response_code($codigoHttp);
        echo json_encode([
            "exito" => false,
            "mensaje" => "Error al enviar mensaje. Código HTTP: $codigoHttp"
        ]);
    }
} else {
    // Servir HTML para GET
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enviar WhatsApp</title>
<link rel="stylesheet" href="/api/estilos.css">
</head>
<body>

<div class="contenedor">
    <h2>Enviar mensaje WhatsApp</h2>
    <form id="formularioMensaje" method="POST">
        <label>Número</label>
        <input type="text" name="numero" id="numero" maxlength="10" pattern="[0-9]{10}" required>

        <label>Mensaje</label>
        <textarea name="mensaje" id="mensaje" rows="4" required></textarea>

        <button type="submit">Enviar mensaje</button>
    </form>

    <div id="resultado" class="resultado"></div>
</div>

<script>
document.getElementById('formularioMensaje').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const resultadoDiv = document.getElementById('resultado');
    const numero = document.getElementById('numero').value.trim();
    const mensaje = document.getElementById('mensaje').value.trim();
    
    resultadoDiv.innerHTML = '<p style="text-align:center; color:#666;">⏳ Enviando...</p>';
    
    try {
        const formData = new FormData();
        formData.append('numero', numero);
        formData.append('mensaje', mensaje);
        
        const respuesta = await fetch('', {
            method: 'POST',
            body: formData
        });
        
        const datos = await respuesta.json();
        
        if (datos.exito) {
            resultadoDiv.innerHTML = `<p class="mensaje-exito">✅ ${datos.mensaje}</p>`;
            document.getElementById('formularioMensaje').reset();
        } else {
            resultadoDiv.innerHTML = `<p class="mensaje-error">❌ ${datos.mensaje}</p>`;
        }
    } catch (error) {
        resultadoDiv.innerHTML = `<p class="mensaje-error">❌ Error de conexión. Por favor, intenta nuevamente.</p>`;
        console.error('Error:', error);
    }
});
</script>

</body>
</html>
<?php
}
?>

