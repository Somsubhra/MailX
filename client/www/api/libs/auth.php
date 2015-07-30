<?php

/**
 * @param $api_key
 * @param $db mysqli
 * @return int|null
 */
function get_account_id_from_api_key($api_key, $db) {

    while(!($select_statement = $db->prepare("SELECT id FROM mailx_account WHERE api_key = ?"))) {
        header("location: ../error.php?code=DB_ERR");
        exit();
    }

    if(!$select_statement->bind_param("s", $api_key)) {
        show_db_error();
    }

    if(!$select_statement->execute()) {
        show_db_error();
    }

    $account_id = -1;
    $out_account_id = NULL;

    if(!$select_statement->bind_result($out_account_id)) {
        show_db_error();
    }

    while($select_statement->fetch()) {
        $account_id = $out_account_id;
    }

    $select_statement->close();

    if($account_id == -1) {
        show_invalid_key_error();
    }

    return $account_id;
}