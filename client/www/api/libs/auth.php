<?php

/**
 * @param $api_key
 * @param $db mysqli
 * @return int|null
 */
function get_account_from_api_key($api_key, $db) {

    while(!($select_statement = $db->prepare("SELECT id, email_address, namespace_id FROM account WHERE api_key = ?"))) {
        header("location: ../error.php?code=DB_ERR");
        exit();
    }

    if(!$select_statement->bind_param("s", $api_key)) {
        show_db_error();
    }

    if(!$select_statement->execute()) {
        show_db_error();
    }

    $account = NULL;

    $out_account_id = NULL;
    $out_account_email_address = NULL;
    $out_account_namespace_id = NULL;

    if(!$select_statement->bind_result($out_account_id, $out_account_email_address, $out_account_namespace_id)) {
        show_db_error();
    }

    while($select_statement->fetch()) {
        $account = array(
            "id" => $out_account_id,
            "email_address" => $out_account_email_address,
            "namespace_id" => $out_account_namespace_id
        );
    }

    $select_statement->close();

    if($account == NULL) {
        show_invalid_key_error();
    }

    return $account;
}