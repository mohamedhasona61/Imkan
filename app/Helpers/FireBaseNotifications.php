<?php

function notifybyfirebase($content, $content_en, $token, $data = [])
{
    $title = "Imkan";
    $SERVER_API_KEY = "AAAAIgc53vk:APA91bG-b2eB7RaUcBe0YBjs9WEkzJzZqjsr9pSTiXcB1_sUebmOVakLEk_brHQFVwvbXW4eookrbExEFNLOIdJfzs1G7lrYCXbow-EOEtehPjJO10z6Qvq79NllBeIBMrPBBcd2bWwD";
    $data = [
        "registration_ids" => [
            $token
        ],
        "notification" => [
            "title" => $title,
            "content" => $content,
            "content_en" => $content_en,
            "sound" => "default"
        ],
    ];


    $dataString = json_encode($data);
    $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'Content-Type: application/json',
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    $response = curl_exec($ch);
    return $response;
}
