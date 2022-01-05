<?php

function sendSuccessResponse($message = null, $data = null, $status_code = 200)
{
    $response = [
        "status" => 1,
    ];

    if($message) {
        $response["message"] = $message;
    } elseif($data) {
        $response["data"] = $data;
    }
    return response($response, $status_code)->header('Content-Type', 'application/json');
}

function sendFailResponse($data, $status_code = 500)
{
    $response = [
        "status" => 0,
        "error" => $data
    ];
    return response($response, $status_code)->header('Content-Type', 'application/json');
}