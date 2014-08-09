<?php
/**
 * Created by PhpStorm.
 * User: Kim
 */

define("SIZE", 5);

define("EXPIRE_TIMEOUT", 60 * 30);

define("STATUS_WAIT_USER", 0);
define("STATUS_IDLE", 1);
define("STATUS_WAIT_AI", 2);
define("STATUS_WIN", 3);
define("STATUS_FAIL", 4);
define("STATUS_DRAW", 5);

//define("ROOT", "root");
//define("ROOT_INVERTED", "root_inverted");
//define("ALPHABET", "alphabet");

// ================================================== vocabulary util ==================================================

function in_alphabet($letter) {
    $alphabet = get_alphabet();
    return in_array($letter, $alphabet);
}

//function in_vocabulary($word) {
//    $in = false;
//    $fp = fopen('words.txt', 'r');
//    while (!feof($fp)) {
//        $line = fgets($fp);
//        if($word === trim($line)) {
//            $in = true;
//            break;
//        }
//    }
//    fclose($fp);
//    //TODO:
//    return $in;
//}

function acceptable_position($i, $j) {
    return $i >= 0 && $j >= 0 && $i < SIZE && $j < SIZE;
}

function get_alphabet() {
    $alphabet = array("А", "Б", "В", "Г", "Д", "Е", "Ж", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ы", "Ь", "Э", "Ю", "Я");
    return $alphabet;
}

//function get_word(&$field, &$words, &$prefixes) {
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
//    $founded = array();
//    for($i = 0; $i < sizeof($available_cells); $i++) {
//        $alphabet = get_alphabet();
//        foreach($alphabet as $key => &$val) {
//            $field[$available_cells[$i][0]][$available_cells[$i][1]] = $val;
//            $count = array();
//            $string = "";
//            dfs_outer($field, $available_cells[$i][0], $available_cells[$i][1], $words, $prefixes, $count, $string, array($available_cells[$i][0], $available_cells[$i][1], $val), $founded);
//        }
//        unset($field[$available_cells[$i][0]][$available_cells[$i][1]]);
//    }
//
//
//}
//
//function dfs_outer($field, $i, $j, &$words, &$prefixes, &$count, &$string, $letter, &$founded) {
//    dfs_inner($field, $letter[0], $letter[1], $words, $prefixes, $count, getr($string), $letter, $founded);
//    $count[$i][$j] = true;
//
//    //echo getr($string) . "<br/>";
//    if(isset($prefixes[$string])) {
//        $cells = array(array($i - 1, $j), array($i + 1, $j), array($i, $j - 1), array($i, $j + 1));
//        foreach($cells as $key => &$val) {
//            if($val[0] >= 0 && $val[1] >= 0 && $val[0] < SIZE && $val[1] < SIZE && isset($field[$val[0]][$val[1]]) && !isset($count[$val[0]][$val[1]])) {
//                $string = $string . $field[$val[0]][$val[1]];
//                dfs_outer($field , $val[0], $val[1], $words, $prefixes, $count, $string, $letter, $founded);
//                $string = gets($string, -1);
//            }
//        }
//    }
//    unset($count[$i][$j]);
//}
//
//function dfs_inner($field, $i, $j, &$words, &$prefixes, &$count, &$string, $letter, &$founded) {
//    $count[$i][$j] = true;
//    $string = $string . $field[$i][$j];
//    //echo $string . "<br/>";
//    if(isset($words[$string])) {
//        echo "x = " . $letter[0] . ", y = " . $letter[1] . ", letter = " . $letter[2] . ", word = " . $string . "<br/>";
//
//    }
//    if(isset($prefixes[$string])) {
//        $cells = array(array($i - 1, $j), array($i + 1, $j), array($i, $j - 1), array($i, $j + 1));
//        foreach($cells as $key => &$val) {
//            if($val[0] >= 0 && $val[1] >= 0 && $val[0] < SIZE && $val[1] < SIZE && isset($field[$val[0]][$val[1]]) && !isset($count[$val[0]][$val[1]])) {
//                dfs_inner($field , $val[0], $val[1], $words, $prefixes, $count, $string, $letter, $founded);
//            }
//        }
//    }
//
//    $string = gets($string, -1);
//    unset($count[$i][$j]);
//}

function get_word(&$field, &$words, &$prefixes, &$pool) {
    $available_cells = array();
    for($i = 0; $i < SIZE; $i++) {
        for($j = 0; $j < SIZE; $j++) {
            if(isset($field[$i][$j])) {
                $adjacent_cells = array(array($i - 1, $j), array($i + 1, $j), array($i, $j - 1), array($i, $j + 1));
                foreach($adjacent_cells as $key => &$val) {
                    if(!isset($field[$val[0]][$val[1]]) && acceptable_position($val[0], $val[1]) && !isset($field[$val[0]][$val[1]])) {
                        $available_cells[] = $val;
                    }
                }
            }
        }
    }
    $founded = array();
    for($i = 0; $i < sizeof($available_cells); $i++) {
        $alphabet = get_alphabet();
        foreach($alphabet as $key => &$val) {
            $field[$available_cells[$i][0]][$available_cells[$i][1]] = $val;
            $count = array();
            $trace = array("", array());
            dfs_outer($field, $available_cells[$i][0], $available_cells[$i][1], $words, $prefixes, $count, $trace, array($available_cells[$i][0], $available_cells[$i][1], $val), $founded, $pool);
        }
        unset($field[$available_cells[$i][0]][$available_cells[$i][1]]);
    }

    return $founded;
//    foreach($founded as $key => &$val) {
//        echo "============== " . $val[1][0] . "<br/>";
//        echo "x = " . $val[0][0] . ", y = " . $val[0][1] . ", letter = " . $val[0][2] . "<br/>";
//        foreach($val[1][1] as $key => &$val) {
//            echo "x = " . $val[0] . ", y = " . $val[1] . ", letter = <br/>";
//        }
//        echo "==============<br/>";
//    }



}

function dfs_outer($field, $i, $j, &$words, &$prefixes, &$count, &$trace, $letter, &$founded, &$pool) {
    //echo "i = " . $i . ", j = " . $j . "<br/>";
    $trace[0] = getr($trace[0]);
    $trace[1] = array_reverse($trace[1]);
    $count[$i][$j] = true;
    dfs_inner($field, $letter[0], $letter[1], $words, $prefixes, $count, $trace, $letter, $founded, $pool);

    $trace[0] = getr($trace[0]);
    $trace[1] = array_reverse($trace[1]);


    //echo getr($string) . "<br/>";
    if(isset($prefixes[$trace[0]])) {
        $cells = array(array($i - 1, $j), array($i + 1, $j), array($i, $j - 1), array($i, $j + 1));
        foreach($cells as $key => &$val) {
            if($val[0] >= 0 && $val[1] >= 0 && $val[0] < SIZE && $val[1] < SIZE && isset($field[$val[0]][$val[1]]) && !isset($count[$val[0]][$val[1]])) {
                $trace[0] = $trace[0] . $field[$val[0]][$val[1]];
                $trace[1][] = array($val[0], $val[1]);
                dfs_outer($field , $val[0], $val[1], $words, $prefixes, $count, $trace, $letter, $founded, $pool);
                array_pop($trace[1]);
                $trace[0] = gets($trace[0], -1);
            }
        }
    }
    unset($count[$i][$j]);
}

function dfs_inner($field, $i, $j, &$words, &$prefixes, &$count, &$trace, $letter, &$founded, &$pool) {
    $change = isset($count[$i][$j]);
    if(!$change) {
        $count[$i][$j] = true;
    }

    $trace[0] = $trace[0] . $field[$i][$j];
    $trace[1][] = array($i, $j);
    if(isset($words[$trace[0]])) {
        //echo "x = " . $letter[0] . ", y = " . $letter[1] . ", letter = " . $letter[2] . ", word = " . $trace[0] . "<br/>";
        //echo "sizeof = " . sizeof($founded[1]) . " " . sizeof($trace[1]) . "<br/>";
        if(sizeof($founded[1][1]) < sizeof($trace[1]) && !isset($pool[$trace[0]])) {
            $founded = array($letter, $trace);
        }
        //$founded[] = array($letter, $trace);
    }
    if(isset($prefixes[$trace[0]])) {
        $cells = array(array($i - 1, $j), array($i + 1, $j), array($i, $j - 1), array($i, $j + 1));
        foreach($cells as $key => &$val) {
            if($val[0] >= 0 && $val[1] >= 0 && $val[0] < SIZE && $val[1] < SIZE && isset($field[$val[0]][$val[1]]) && !isset($count[$val[0]][$val[1]])) {
                dfs_inner($field , $val[0], $val[1], $words, $prefixes, $count, $trace, $letter, $founded, $pool);
            }
        }
    }
    array_pop($trace[1]);
    $trace[0] = gets($trace[0], -1);

    if(!$change) {
        unset($count[$i][$j]);
    }
}

// ================================================== user util ==================================================

//function get_id() {
//    return 0;
//}

function game_exists($session_id) {
    $exists = get($session_id);
    return $exists;
}

function calculate(&$data) {
    if($data["score"][0] > $data["score"][1]) {
        $data["status"] = STATUS_WIN;
    } else if($data["score"][0] < $data["score"][1]) {
        $data["status"] = STATUS_FAIL;
    } else {
        $data["status"] = STATUS_DRAW;
    }
}

// ================================================== cache util ==================================================

function put($key, $value, $expirable) {
    $memcache = get_connection();
    memcache_set($memcache, $key, $value);
    return;
    if($expirable) {
        memcache_set($memcache, $key, $value, 0, EXPIRE_TIMEOUT);
    } else {
        memcache_set($memcache, $key, $value, 0, 0);
    }
}

function get($key) {
    $memcache = get_connection();
    return memcache_get($memcache, $key);
}

function get_connection() {
    $memcache =  memcache_connect("localhost", 11211) or die ("Could not connect memcache");
    return $memcache;
}

// ================================================== string util ==================================================

//function starts_with($string, $prefix)
//{
//    return $prefix === "" || strpos($string, $prefix) === 0;
//}

//function getc($string, $index) {
//    return substr($string, $index * 2, 2);
//}

function gets($string, $index) {
    return substr($string, 0, $index * 2);
}

function getr($string) {
    preg_match_all('/./us', $string, $ar);
    return join('', array_reverse($ar[0]));
}

function getl($string) {
    return strlen($string) / 2;
}

//function contains($string, $char) {
//    for($i = 0; $i < getl($string); $i++) {
//        if($char === getc($string, $i)) {
//            return true;
//        }
//    }
//    return false;
//}

// ================================================== tree util ==================================================

