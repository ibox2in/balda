<?php
/**
 * Created by PhpStorm.
 * User: Kim
 */

require_once("util.php");

session_start();
$session_id = session_id();
$_POST = array_filter($_POST);
$data = get(session_id());
if(!$data) {
    header('HTTP/1.1 406 Not Acceptable', true, 406);
    die();
}

if(isset($_POST["status"]) && $data["status"] == STATUS_WAIT_USER) {
    $data["status"] = STATUS_FAIL;
    put($session_id, $data, true);
    return;
}

if(isset($_POST["word"]) && $data["status"] == STATUS_WAIT_USER) {

    $word_data = $_POST["word"];
    $letter = $word_data[0];
    if(!in_alphabet($letter[2])) {
        header('HTTP/1.1 406 Not Acceptable', true, 406);
        die();
    }
    if(!acceptable_position($letter[0], $letter[1]) || isset($data["field"][$letter[0]][$letter[1]])) {
        header('HTTP/1.1 406 Not Acceptable', true, 406);
        die();
    }
    $word = "";

    $count = sizeof($word_data[1]);
    $contains_new_letter = false;
    for($i = 0; $i < $count; $i++) {
        $val = $word_data[1][$i];

        // последовательность
        if($i != 0) {
            if(!(($val[0] == $word_data[1][$i - 1][0] && $val[1] == $word_data[1][$i - 1][1] + 1)
                || ($val[0] == $word_data[1][$i - 1][0] && $val[1] == $word_data[1][$i - 1][1] - 1)
                || ($val[0] == $word_data[1][$i - 1][0] + 1 && $val[1] == $word_data[1][$i - 1][1])
                || ($val[0] == $word_data[1][$i - 1][0] - 1 && $val[1] == $word_data[1][$i - 1][1]))) {
                header('HTTP/1.1 406 Not Acceptable', true, 406);
                die();
            }
        }

        // пустые клетки
        if(!acceptable_position($val[0], $val[1]) || (!isset($data["field"][$val[0]][$val[1]]) && $letter[0] != $val[0] && $letter[1] != $val[1])) {
            header('HTTP/1.1 406 Not Acceptable', true, 406);
            die();
        }

        // содержит новую букву
        if($letter[0] == $val[0] && $letter[1] == $val[1]) {
            $contains_new_letter = true;
        }

        // повторения
        $repeat = 0;
        for($j = 0; $j < $count; $j++) {
            if($word_data[1][$j][0] == $val[0] && $word_data[1][$j][1] == $val[1]) {
                $repeat++;
            }
        }
        if($repeat > 1) {
            header('HTTP/1.1 406 Not Acceptable', true, 406);
            die();
        }


        $word = ($letter[0] == $val[0] && $letter[1] == $val[1]) ? $word . $letter[2] : $word . $data["field"][$val[0]][$val[1]];
    }

    if(!$contains_new_letter) {
        header('HTTP/1.1 406 Not Acceptable', true, 406);
        die();
    }

    $words = unserialize(file_get_contents('../resources/volumes/volume'));
    if(!isset($words[$word])) {
        header('HTTP/1.1 406 Not Acceptable', true, 406);
        die();
    }
    if(isset($data["pool"][$word])) {
        header("HTTP/1.1 418 I'm a teapot", true, 418);
        die();
    }
    $data["field"][$letter[0]][$letter[1]] = $letter[2];
    $data["pool"][$word] = true;
    $data["score"][0] += getl($word);
    $data["user_list"][] = $word;
    $data["status"] = STATUS_IDLE;
    if(sizeof($data["field"][0]) == SIZE && sizeof($data["field"][1]) == SIZE && sizeof($data["field"][2]) == SIZE && sizeof($data["field"][3]) == SIZE && sizeof($data["field"][4]) == SIZE) {
        calculate($data);
    }
    put($session_id, $data, true);
    echo json_encode(array($word, $data["score"], $data["status"]));
    return;
}

if(isset($_POST["wait"]) && $data["status"] == STATUS_IDLE) {
    $data["status"] = STATUS_WAIT_AI;
    $words = unserialize(file_get_contents('../resources/volumes/volume'));
    $prefixes = unserialize(file_get_contents('../resources/volumes/volume_inverted'));
    $word = get_word($data["field"], $words, $prefixes, $data["pool"]);

    if(sizeof($word) > 0) {
        $data["field"][$word[0][0]][$word[0][1]] = $word[0][2];
        $data["pool"][$word[1][0]] = true;
        $data["score"][1] += getl($word[1][0]);
        $data["ai_list"][] = $word[1][0];
        $data["status"] = STATUS_WAIT_USER;
        if(sizeof($data["field"][0]) == SIZE && sizeof($data["field"][1]) == SIZE && sizeof($data["field"][2]) == SIZE && sizeof($data["field"][3]) == SIZE && sizeof($data["field"][4]) == SIZE) {
            calculate($data);
        }
    } else {
        calculate($data);
    }


    put($session_id, $data, true);
    echo json_encode(array($word, $data["score"], $data["status"]));
    return;
}
