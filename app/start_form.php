<?php
/**
 * Created by PhpStorm.
 * User: Kim
 */

require_once("util.php");

session_start();

$session_id = session_id();
$data = get($session_id);

$_POST = array_filter($_POST);

if(!$data || isset($_POST["restart"])) {
    $start_words = unserialize(file_get_contents('../resources/volumes/volume_size'));
    $word = $start_words[array_rand($start_words)];
    $data = array(
        "date" => time(),
        "field" => array(
            0 => array(),
            1 => array(),
            2 => preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY),
            3 => array(),
            4 => array()
        ),
        "status" => STATUS_WAIT_USER,
        "pool" => array(),
        "score" => array(0, 0),
        "user_list" => array(),
        "ai_list" => array()
    );
    $data["pool"][$word] = true;
    put($session_id, $data, true);
}

echo json_encode(array($data["field"], $data["score"], $data["user_list"], $data["ai_list"], $data["status"]));



