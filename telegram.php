<?php

# Принимаем запрос
$data = json_decode(file_get_contents('php://input'), TRUE);
//file_put_contents('file.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND);


//https://api.telegram.org/bot*Токен бота*/setwebhook?url=*ссылка на бота*


# Обрабатываем ручной ввод или нажатие на кнопку
$data = $data['callback_query'] ? $data['callback_query'] : $data['message'];

# Важные константы
define('TOKEN', '1310530208:AAHAKEpv_W9k3rWcO9G7ZOrJVcKHGEBqo6c');

# Записываем сообщение пользователя
$message = mb_strtolower(($data['text'] ? $data['text'] : $data['data']),'utf-8');


# Обрабатываем сообщение
switch ($message)
{
    case 'текст':
        $method = 'sendMessage';
        $send_data = [
            'text'   => 'Вот мой ответ'
        ];
        break;

    case 'кнопки':
        $method = 'sendMessage';
        $send_data = [
            'text'   => 'Вот мои кнопки',
            'reply_markup' => [
                'resize_keyboard' => true,
                'keyboard' => [
                    [
                        ['text' => 'Видео'],
                        ['text' => 'Кнопка 2'],
                    ],
                    [
                        ['text' => 'Кнопка 3'],
                        ['text' => 'Кнопка 4'],
                    ]
                ]
            ]
        ];
        break;
        
    case 'инлайн':
        $method = 'sendMessage';
        $send_data = [
            'text'   => 'Вот мои инлайн кнопки',
            'reply_markup' => [
                'inline_keyboard' => [
                    [
                        [ 'text' => 'Первая в ряду', 'callback_data' => 'видео'],
                        [ 'text' => 'Вторая в ряду', 'callback_data' => 'кнопки']
                    ],
                    [
                        [ 'text' => 'Второй ряд', 'callback_data' => 'любое значение']
                    ]
                ]
            ]
        ];
        break;
 
    case 'видео':
        $method = 'sendVideo';
        $send_data = [
            'video'   => 'https://chastoedov.ru/video/amo.mp4',
            'caption' => 'Вот мое видео',
            'reply_markup' => [
                'resize_keyboard' => true,
                'keyboard' => [
                    [
                        ['text' => 'Кнопка 1'],
                        ['text' => 'Кнопка 2'],
                    ],
                    [
                        ['text' => 'Кнопка 3'],
                        ['text' => 'Кнопка 4'],
                    ]
                ]
            ]
        ];
        break;

    default:
        $method = 'sendMessage';
        $send_data = [
            'text' => 'Не понимаю о чем вы :('
        ];
}

# Добавляем данные пользователя
$send_data['chat_id'] = $data['chat']['id'];

$res = sendTelegram($method, $send_data);

function sendTelegram($method, $data, $headers = [])
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.telegram.org/bot' . TOKEN . '/' . $method,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers)
    ]);   
    
    $result = curl_exec($curl);
    //file_put_contents('file.txt', '$result: '.print_r($result, 1)."\n", FILE_APPEND);
    curl_close($curl);
    return (json_decode($result, 1) ? json_decode($result, 1) : $result);
}
