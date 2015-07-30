<?php

function show_error($message) {
    die(json_encode(array(
        "success" => false,
        "body" => array(
            "message" => $message
        )
    )));
}

function show_db_error() {
    show_error("Database error");
}

function show_invalid_key_error() {
    show_error("Invalid API key");
}