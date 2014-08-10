<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<body>


<?php
/**
 * Created by PhpStorm.
 * User: Kim
 */

require_once("../app/util.php");
return;
set_time_limit(0);

$words = array();
$start_words = array();


$fp = fopen('words.txt', 'r');
while (!feof($fp)) {
    $line = fgets($fp);
    $words[] = trim($line);
    echo $line . "<br/>";
}
fclose($fp);

$prefixes = array();
foreach($words as $key => &$val) {
    if(getl($val) == SIZE) {
        $start_words[] = $val;
    }
    for($i = 0; $i < getl($val); $i++) {
        $prefixes[] = gets($val, $i + 1);
    }
}
$prefixes[] = "";
$prefixes = array_flip($prefixes);
$words = array_flip($words);

$ptr = fopen("volumes/volume", 'wb');
fwrite($ptr, serialize($words));
fclose($ptr);
$ptr = fopen("volumes/volume_inverted", 'wb');
fwrite($ptr, serialize($prefixes));
fclose($ptr);
$ptr = fopen("volumes/volume_size", 'wb');
fwrite($ptr, serialize($start_words));
fclose($ptr);

echo "OK";


//$words = array('РОГ', 'ГО', 'ГОР');


//if(in_vocabulary("фыв")) {
//    echo "IN!!<br/>";
//}

//$old = memory_get_usage();

//get_status();
//$arr = array_slice($words, 0, 1000);
//$arr = array("бар", "балда", "банка", "манка", "барсук", "раб", "рак");
// =========================== start
//$words = array_slice($words, 18000, 8000);
////$words = array("бар", "балда", "банка", "манка", "барсук", "раб", "рак");

//$flipped_words = array_flip($words);
//
//$root = array();
//
//add_node($root, "", $words, $flipped_words, false);
//
//$root_inv = array();
//$reverted_words = array();
//foreach($words as $key => &$val) {
//    for($i = 0; $i < getl($val); $i++) {
//        $reverted_words[] = gets($val, $i + 1);
//    }
//}
//$reverted_words[] = "";
//$reverted_words = array_flip($reverted_words);
//$words = array_flip($words);
//$time_start = round(microtime(true) * 1000);
//$words = unserialize(file_get_contents('volumes/volume'));
//$reverted_words = unserialize(file_get_contents('volumes/volume_inverted'));
//$time_end = round(microtime(true) * 1000);
//echo $time_end - $time_start . " time<br/>";
//add_node($root_inv, "", $reverted_words, $flipped_reverted_words, true);
//
//$ptr = fopen("volumes/volume", 'wb');
//fwrite($ptr, serialize($words));
//fclose($ptr);
//$ptr = fopen("volumes/volume_inverted", 'wb');
//fwrite($ptr, serialize($reverted_words));
//fclose($ptr);
// =========================== end
//$field = array(
//    0 => array(),
//    1 => array('А'),
//    2 => array('Б', 'А', 'Н', 'К', 'А'),
//    3 => array(),
//    4 => array()
//);
//$founded_words = array();
//
//
//for($i = 1; $i < 6; $i++) {
//    $fp = fopen('volumes/volume_9', 'rb');
//    $root = "";
//    while (!feof($fp)) {
//        $root = $root . fgets($fp);
//    }
//
//    fclose($fp);
//    $fp = fopen('volumes/volume_inverted_10', 'rb');
//    $root_inv = "";
//    while (!feof($fp)) {
//        $root_inv = $root_inv . fgets($fp);
//    }
//    fclose($fp);

//    $root = igbinary_unserialize(file_get_contents('volumes/volume_10'));
//
//    $root_inv = igbinary_unserialize(file_get_contents('volumes/volume_inverted_10'));
//
//    check_word($field, $root, $root_inv, $founded_words);
//}

// ============ DFS
//$count = array();
//$founded = array();
//$trace = array();
//$trace = array("", array());
//$string = "";
////dfs($field, 2, 0, $words, $count, $string);
//$pool = array();
//$founded = get_word($field, $words, $reverted_words, $pool);
//echo "============== " . $founded[1][0] . "<br/>";
//echo "==============<br/>";
////dfs_outer($field, 1, 0, $words, $reverted_words, $count, $trace, array(1, 0, 'А'), $founded);
//
//
////$data = get(get_id());
////$word = get_word($data["field"]);
////echo json_encode($word);/$word[1][0];
//
//// ================
//
//foreach($founded_words as $key => &$val) {
//    echo $val[0] . " " . $val[1] . " " . $val[2] . " ". $val[3] . "<br/>";
//}
//
//
//
//$mem = memory_get_usage();
//$size = abs($mem - $old);
//echo "==" . (($size / 1024) / 1024) . "===<br/>";


//put(ROOT, $root, false);
//put(ROOT_INVERTED, $root_inv, false);






//function check_word1(&$field, &$words, &$prefixes) {
//    $available_cells = array();
//    for($i = 0; $i < SIZE; $i++) {
//        for($j = 0; $j < SIZE; $j++) {
//            if(isset($field[$i][$j])) {
//                $adjacent_cells = array(array($i - 1, $j), array($i + 1, $j), array($i, $j - 1), array($i, $j + 1));
//                foreach($adjacent_cells as $key => &$val) {
//                    if(!isset($field[$val[0]][$val[1]]) && acceptable_position($val[0], $val[1], $field)) {
//                        $available_cells[] = $val;
//                    }
//                }
//            }
//        }
//    }
//    for($i = 0; $i < sizeof($available_cells); $i++) {
//        $alphabet = get_alphabet();
//
//        foreach($alphabet as $key => &$val) {
//
//            $field[$available_cells[$i][0]][$available_cells[$i][1]] = $val;
//            $count = array();
//            $string = "";
//            dfs_outer($field, $available_cells[$i][0], $available_cells[$i][1], $words, $prefixes, $count, $string, $available_cells[$i][0], $available_cells[$i][1], array($val, $available_cells[$i][0], $available_cells[$i][1]));
//        }
//        unset($field[$available_cells[$i][0]][$available_cells[$i][1]]);
//    }
//}
//
//// =========================================
//
//function dfs_outer($field, $i, $j, &$words, &$prefixes, &$count, &$string, $x, $y, $letter) {
//
//    dfs_inner($field, $x, $y, $words, $prefixes, $count, getr($string), $letter);
//    $count[$i][$j] = true;
//
//    //echo getr($string) . "<br/>";
//    if(isset($prefixes[$string])) {
//        $cells = array(array($i - 1, $j), array($i + 1, $j), array($i, $j - 1), array($i, $j + 1));
//        foreach($cells as $key => &$val) {
//            if($val[0] >= 0 && $val[1] >= 0 && $val[0] < SIZE && $val[1] < SIZE && isset($field[$val[0]][$val[1]]) && !isset($count[$val[0]][$val[1]])) {
//                $string = $string . $field[$val[0]][$val[1]];
//                dfs_outer($field , $val[0], $val[1], $words, $prefixes, $count, $string, $x, $y, $letter);
//                $string = gets($string, -1);
//            }
//        }
//    }
//    unset($count[$i][$j]);
//}
//
//function dfs_inner($field, $i, $j, &$words, &$prefixes, &$count, &$string, $letter) {
//    $count[$i][$j] = true;
//    $string = $string . $field[$i][$j];
//    //echo $string . "<br/>";
//    if(isset($words[$string])) {
//        echo "x = " . $letter[1] . ", y = " . $letter[2] . ", letter = " . $letter[0] . ", word = " . $string . "<br/>";
//    }
//    if(isset($prefixes[$string])) {
//        $cells = array(array($i - 1, $j), array($i + 1, $j), array($i, $j - 1), array($i, $j + 1));
//        foreach($cells as $key => &$val) {
//            if($val[0] >= 0 && $val[1] >= 0 && $val[0] < SIZE && $val[1] < SIZE && isset($field[$val[0]][$val[1]]) && !isset($count[$val[0]][$val[1]])) {
//                dfs_inner($field , $val[0], $val[1], $words, $prefixes, $count, $string, $letter);
//            }
//        }
//    }
//
//    $string = gets($string, -1);
//    unset($count[$i][$j]);
//}

//function in_array_r($element, $array) {
//    foreach($array as $key => &$val) {
//        if($element[0] == $val[0] && $element[1] == $val[1]) {
//            return true;
//        }
//    }
//    return false;
//}
//
//// ===========
//
//function check_word(&$field, &$root, &$root_inv, &$founded_words) {
//    $available_cells = array();
//    for($i = 0; $i < SIZE; $i++) {
//        for($j = 0; $j < SIZE; $j++) {
//            if(isset($field[$i][$j])) {
//                $adjacent_cells = array(array($i - 1, $j), array($i + 1, $j), array($i, $j - 1), array($i, $j + 1));
//                foreach($adjacent_cells as $key => &$val) {
//                    if(!isset($field[$val[0]][$val[1]]) && acceptable_position($val[0], $val[1], $field)) {
//                        $available_cells[] = $val;
//                        //echo $val[0] . " " . $val[1] . "<br/>";
//                    }
//                }
//            }
//        }
//    }
//    for($i = 0; $i < sizeof($available_cells); $i++) {
//        $alphabet = array("а", "б", "д", "к", "л", "м", "н", "р", "с", "у");
//
//        foreach($alphabet as $key => &$val) {
//
//            $field[$available_cells[$i][0]][$available_cells[$i][1]] = $val;
//            if(isset($root_inv[1][$val])) {
//                $node = &$root_inv[1][$val];
//                $counter[$available_cells[$i][0]][$available_cells[$i][1]] = 0;
//                find($field, $node, $available_cells[$i][0], $available_cells[$i][1], $founded_words, $root, $available_cells[$i][0], $available_cells[$i][1]);
//            }
//        }
//        unset($field[$available_cells[$i][0]][$available_cells[$i][1]]);
//    }
//}
//
//function find(&$field, &$node, $i, $j, &$founded_words, &$root, $x, $y) {
//    if(isset($node[2])) {
//        if(!$node[3]) {
//            $founded_words[] = array($node[2], $x, $y, $field[$x][$y]);
//        } else {
//            $node2 = find_node($root, getr($node[2])); //?
//            find($field, $node2, $x, $y, $founded_words, $root, $x, $y);
//        }
//    }
//    $adjacent_cells = array(array($i - 1, $j), array($i + 1, $j), array($i, $j - 1), array($i, $j + 1));
//    foreach($adjacent_cells as $key => &$val) {
//        if(!isset($field[$val[0]][$val[1]])) {
//            continue;
//        }
//        if(!isset($node[1][$field[$val[0]][$val[1]]])) {
//            continue;
//        }
//        if(isset($node[1][$field[$val[0]][$val[1]]])) {
//            find($field, $node[1][$field[$val[0]][$val[1]]], $val[0], $val[1], $founded_words, $root, $x, $y);
//        }
//    }
//}
//
//function &find_node(&$root, $string) {
//    $node = $root;
//    for($i = 0; $i < getl($string); $i++) {
//        $node = &$node[1][getc($string, $i)];
//    }
//    return $node;
//}
//
//function add_node(&$node, $prefix, &$words, &$flipped_words, $inverted) {
//    $level = getl($prefix);
//    $string = "";
//    foreach($words as $key => &$val) {
//        if(!contains($string, getc($val, $level)) && starts_with($val, $prefix)) {
//            $string = $string . getc($val, $level);
//        }
//    }
//    if(is_vocabulary($prefix, $flipped_words)) {
//        $node[2] = $prefix;
//    }
//    $node[3] = $inverted;
//    if($string === "") {
//        return;
//    }
//    $node[0] = $string;
//    $node[1] = array();
//
//    for($j = 0; $j < getl($string); $j++) {
//        $node[1][getc($string, $j)] = array();
//        add_node($node[1][getc($string, $j)], $prefix . getc($string, $j), $words, $flipped_words, $inverted);
//    }
//}
//
//function is_vocabulary($string, $flipped_words) {
//    if(isset($flipped_words[$string])) {
//        return true;
//    }
//    return false;
//}
?>
</body>
</html>