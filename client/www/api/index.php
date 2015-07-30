<?php
header("Content-Type: application/json");

echo json_encode(array(
    "success" => true,
    "body" => array(
        "message" => "Ground zero!"
    )
));