<?php
header('Content-Type: application/json');

$urlApi = "https://morakz.com/api/text";
$token = "mkuzfO8C0CXeE68ziJ4Rm0EwvaH49Ajh"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
    http_response_code(405);
    echo json_encode([
        "exito" => false,
        "mensaje" => "Método no permitido."
    ]);
}
?>

