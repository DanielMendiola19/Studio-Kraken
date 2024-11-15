<?php
$bot_token = "7651645109:AAEXKT7ZKlQPBoSra9NGDqH7eC4aKstK0rs";

// URL de la API de Telegram
$url = "https://api.telegram.org/bot$bot_token/getUpdates";

// Llamada a la API
$response = file_get_contents($url);
$responseArray = json_decode($response, true);

// Obtener el chat_id del último mensaje recibido
if (isset($responseArray['result'][0]['message']['chat']['id'])) {
    $chat_id = $responseArray['result'][0]['message']['chat']['id'];
    echo "Tu chat_id es: " . $chat_id;
} else {
    echo "No se pudo obtener el chat_id. Intenta enviar un mensaje al bot.";
}
?>
