<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">-->
<html>
<head>
    <title><?= _("Балда") ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <link rel="stylesheet" type="text/css" href="../public/css/style.css" />
    <link rel="stylesheet" type="text/css" href="../public/css/popup.css" />
    <script type="text/javascript" src="../public/js/jquery.min.js"></script>
    <script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../public/js/jquery.popup.min.js"></script>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: Kim
 */
require_once("../app/util.php");
session_start();
?>
<script type="text/javascript">
    var data = [[], []];
    var size = <?= SIZE ?>;
    var statusUser = <?= STATUS_WAIT_USER ?>;
    var statusIdle = <?= STATUS_IDLE ?>;
    var statusAI = <?= STATUS_WAIT_AI ?>;
    var statusWin = <?= STATUS_WIN ?>;
    var statusFail = <?= STATUS_FAIL ?>;
    var statusDraw = <?= STATUS_DRAW ?>;
    var currentStatus = -1;

    $(document).ready(function(){

        $("#error").popup({ type : 'html', preloaderContent: '' });

        $(".drag").draggable({
            revert: true
        });

        $(".td").droppable({
            drop: function(event, ui) {
                //word = $(this).data('word');

                //console.log($(ui.draggable));
                if($(this).hasClass('available') && currentStatus == statusUser) {
                    for(var i = 0; i < data[1].length && data[0].length > 0; i++) {
                        if(data[1][i][0] == data[0][0] && data[1][i][1] == data[0][1]) {
                            clearTrace(data[1].slice(i, data[1].length), true);
                            break;
                        }
                    }
                    getCell(data[0][0], data[0][1]).html('');
                    data[0] = [getX($(this)), getY($(this)), $(ui.draggable).html()];
                    $(this).html($(ui.draggable).html());
                }

                //console.log(data);
            }
        });

        $(".td").on({
            mouseenter: function () {
                if($(this).html()) {
                    $(this).addClass('hover');
                }
            },
            mouseleave: function () {
                $(this).removeClass('hover');
            }
//            click: function () {
//            },
//            dblclick: function() {
//            }
        });

        jQuery.fn.singleDoubleClick = function(singleClickCallback, doubleClickCallback, timeout) {
            return this.each(function() {
                var clicks = 0,
                self = this;
                jQuery(this).click(function(event) {
                    clicks++;
                    if (clicks == 1) {
                        setTimeout(function() {
                            if (clicks == 1) {
                                singleClickCallback.call(self, event);
                            } else {
                                doubleClickCallback.call(self, event);
                            }
                            clicks = 0;
                        }, 170);
                    }
                });
            });
        }

        $(".td").singleDoubleClick(function() {

            if(!$.trim($(this).html())) {
                return;
            }
            if(data[0].length == 0) {
                return;
            }
            if(data[1].length != 0) {
                var i = getX($(this));
                var j = getY($(this));
                var lastI = data[1][data[1].length - 1][0];
                var lastJ = data[1][data[1].length - 1][1];

                var acceptable = false;
                if((i == lastI + 1 && j == lastJ) || (i == lastI - 1 && j == lastJ) || (i == lastI && j == lastJ + 1) || (i == lastI && j == lastJ - 1) || (i == lastI && j == lastJ)) {
                    acceptable = true;
                }

                if(getCell(i, j).hasClass('click')) {
                    acceptable = true;
                }

                if(!acceptable) {
                    return;
                }
            }
            if($(this).hasClass('click')) {
                //if(data[1][data[1].length - 1][0] == getX($(this)) && data[1][data[1].length - 1][1] == getY($(this))) {
                    $(this).removeClass('click');

                    for(var i = 0; i < data[1].length && data[0].length > 0; i++) {
                        if(data[1][i][0] == getX($(this)) && data[1][i][1] == getY($(this))) {
                            clearTrace(data[1].slice(i, data[1].length), true);
                            break;
                        }
                    }
                    //clearTrace([[, ]], true);
                //}
            } else {
                $(this).addClass('click');
                renderTrace($(this), data[1].length == 0 ? -1 : data[1][data[1].length - 1][0], data[1].length == 0 ? -1 : data[1][data[1].length - 1][1]);
                data[1].push([getX($(this)), getY($(this))]);
            }

            isValid();
        }, function() {
            var td = $(this);
            if(!td.html() || !(data[0][0] == getX(td) && data[0][1] == getY(td))) {
                return;
            }
            for(var i = 0; i < data[1].length && data[0].length > 0; i++) {
                if(data[1][i][0] == getX(td) && data[1][i][1] == getY(td)) {
                    clearTrace(data[1].slice(i, data[1].length), true);
                    break;
                }
            }
            data[0] = [];
            td.html('');
            td.effect("shake", { distance: 5, times: 2 });

            isValid();
        });

        $('#pregame').on({
            click: function () {
                $.ajax({
                    type: 'POST',
                    url: '../app/start_form.php',
                    success: function(response){
                        onStart(response);
                    },
                    error: function(response) {
                    }
                });


            }
        });

        $('#submit').on({
            click: function () {
                if(!isValid()) {
                    return;
                }
                $('#submit').hide();
                $('#spinner').show();

                $.ajax({
                    type: 'POST',
                    data: { word: data },
                    url: '../app/game_form.php',
                    success: function(response) {

                        response = jQuery.parseJSON(response);
                        //console.log(data[1]);
                        clearTrace(data[1], true);
                        data = [[], []];
                        $('#userscore').html(response[1][0]);
                        $('#userscore').hide();
                        $('#userscore').fadeIn();
                        appendWord($('#userlist'), response[0]);
                        setStatus(response[2]);
                        if(response[2] == statusIdle) {
                            setStatus(statusAI);
                            $.ajax({
                                type: 'POST',
                                data: { wait: true },
                                url: '../app/game_form.php',
                                success: function(response){
                                    $('#spinner').hide();
                                    $('#submit').show();
                                    response = jQuery.parseJSON(response);
                                    if(response[0].length > 0) {
                                        var wordData = response[0];
                                        getCell(wordData[0][0], wordData[0][1]).html(wordData[0][2]);

                                        //console.log(wordData);
                                        for(var i = 0; i < wordData[1][1].length; i++) {
                                            var cell = getCell(wordData[1][1][i][0], wordData[1][1][i][1]);
                                            if(i > 0) {
                                                renderTrace(cell, wordData[1][1][i - 1][0], wordData[1][1][i - 1][1]);
                                            }
                                        }
                                        setTimeout(function() {
                                            clearTrace(wordData[1][1], false);
                                        }, 1500);

                                        renderField();
                                        $('#aiscore').html(response[1][1]);
                                        $('#aiscore').hide();
                                        $('#aiscore').fadeIn();
                                        appendWord($('#ailist'), response[0][1][0]);
                                    }

                                    setStatus(response[2]);
                                    isValid();
                                },
                                error: function(response) {
                                    if(response.status == 418) {
                                        setStatus(statusWin);
                                    }
                                }
                            });
                        }
                    },
                    error: function(response) {
                        $('#spinner').hide();
                        $('#submit').show();
                        if(response.status == 406) {
                            $('#error').data('popup').open('<span class="minor"><?= _("Такого слова нет") ?></span>');
                        }
                        if(response.status == 418) {
                            $('#error').data('popup').open('<span class="minor"><?= _("Такое слово уже есть") ?></span>');
                        }
                    }

                });
            }
        });

    });

    function renderField() {
        for(var i = 0; i < size; i++) {
            for(var j = 0; j < size; j++) {
                getCell(i, j).removeClass();
                getCell(i, j).addClass('td');
            }
        }

        for(var i = 0; i < size; i++) {
            for(var j = 0; j < size; j++) {
                var cell = getCell(i, j);
                if(cell.html()) {
                    cell.removeClass();
                    cell.addClass('td marked');
                    var adjacentCells = [[i - 1, j], [i + 1, j], [i, j - 1], [i, j + 1]];
                    for(var k = 0; k < adjacentCells.length; k++) {
                        if(adjacentCells[k][0] < 0 || adjacentCells[k][1] < 0 || adjacentCells[k][0] >= size || adjacentCells[k][1] >= size) {
                            continue;
                        }
                        var adjacentCell = getCell(adjacentCells[k][0], adjacentCells[k][1]);
                        if(!adjacentCell.html()) {
                            adjacentCell.addClass('available');
                        }
                    }
                }
            }
        }
    }

    function isValid() {
        if(currentStatus == statusFail || currentStatus == statusWin || currentStatus == statusDraw) {
            $('#submit').addClass('minor');
            return false;
        }
        if(data[0].length != 3) {
            $('#submit').addClass('minor');
            return false;
        }
        if(data[1].length < 2) {
            $('#submit').addClass('minor');
            return false;
        }
        for(var i = 0; i < data[1].length; i++) {
            if(data[1][i][0] == data[0][0] && data[1][i][1] == data[0][1]) {
                $('#submit').removeClass('minor');
                return true;
            }
        }
        $('#submit').addClass('minor');
        return false;
    }

    function appendWord(list, word) {
        $('#wordlist').show();
        list.append('<tr><td>' + word + '</td><td style="width: 1px;">' + word.length + '</td></tr>');
    }

    function getCell(i, j) {
//        $("table")[0].rows[i].cells[j].value = "asd";
//        return $("table")[0].rows[i].cells[j];
        return $('#field tr:eq(' + i + ') td:eq(' + j + ')');
    }

    function getX(td) {
        return td.closest('tr').index();
    }

    function getY(td) {
        return td.index();
    }

    function renderTrace(td, x, y) {
        if(x == -1 || y == -1) {
            return;
        }
        if(getX(td) > x) {
            $('<img src="../public/img/arrow_down.png" style="position: absolute; top: -12px; z-index: 1; left: 24px;"/>').hide().appendTo(td).fadeIn(200);
        } else if(getX(td) < x) {
            $('<img src="../public/img/arrow_up.png" style="position: absolute; bottom: -12px; z-index: 1; left: 24px;"/>').hide().appendTo(td).fadeIn(200);
        } else if(getY(td) > y) {
            $('<img src="../public/img/arrow_right.png" style="position: absolute; left: -12px; z-index: 1; margin: auto; top: 0; bottom: 0;"/>').hide().appendTo(td).fadeIn(200);
        } else if(getY(td) < y) {
            $('<img src="../public/img/arrow_left.png" style="position: absolute; right: -12px; z-index: 1; margin: auto; top: 0; bottom: 0;"/>').hide().appendTo(td).fadeIn(200);
        }
    }

    function clearTrace(array, pop) {
        array = array.slice();
        for(var i = 0; i < array.length; i++) {
            if(pop) {
                data[1].pop();
            }
            getCell(array[i][0], array[i][1]).removeClass('click').children('img').fadeOut(200, function() {
                $(this).remove();
            });
        }
    }

    function setStatus(status) {
        currentStatus = status;
        if(status == statusUser) {
            $('#status').css("opacity", 0).html('<span class="wait">Ваш ход.</span> Всегда можно <span class="link" id="giveup">сдаться</span>').fadeTo(200, 1);
            $('#status').children('.link').click(function(){
                $.ajax({
                    type: 'POST',
                    url: '../app/game_form.php',
                    data: { giveup: true },
                    success: function(response){
                        setStatus(statusFail);
                        isValid();
                    }
                });
            });
        } else if(status == statusAI) {
            $('#status').css("opacity", 0).html('Ждем ответа противника...').fadeTo(200, 1);
        } else if(status == statusWin) {
            $('#status').css("opacity", 0).html('<span class="win">Победа!</span> Можно сыграть <span class="link">еще раз</span>').fadeTo(200, 1);
            $('#status').children('.link').click(function(){
                $.ajax({
                    type: 'POST',
                    url: '../app/start_form.php',
                    data: { restart: true },
                    success: function(response){
                        onStart(response);
                    }
                });
            });
        } else if(status == statusFail) {
            $('#status').css("opacity", 0).html('<span class="fail">Поражение.</span> Можно сыграть <span class="link">еще раз</span>').fadeTo(200, 1);
            $('#status').children('.link').click(function(){
                $.ajax({
                    type: 'POST',
                    url: '../app/start_form.php',
                    data: { restart: true },
                    success: function(response){
                        onStart(response);
                    }
                });
            });
        } else if(status == statusDraw) {
            $('#status').css("opacity", 0).html('<span class="draw">Ничья.</span> Можно сыграть <span class="link">еще раз</span>').fadeTo(200, 1);
            $('#status').children('.link').click(function(){
                $.ajax({
                    type: 'POST',
                    url: '../app/start_form.php',
                    data: { restart: true },
                    success: function(response){
                        onStart(response);
                    }
                });
            });
        }
    }

    function onStart(response) {
        response = jQuery.parseJSON(response);
        var field = response[0];
        for(var i = 0; i < size; i++) {
            for(var j = 0; j < size; j++) {
                getCell(i, j).html('').html(field[i][j]);
            }
        }
        renderField();

        setStatus(response[4]);

        var score = response[1];
        $('#userscore').html(score[0]);
        $('#aiscore').html(score[1]);

        $('#wordlist').hide();
        var userList = response[2];
        $('#userlist').html('');
        for(var i = 0; i < userList.length; i++) {
            appendWord($('#userlist'), userList[i]);
        }

        var aiList = response[3];
        $('#ailist').html('');
        for(var i = 0; i < aiList.length; i++) {
            appendWord($('#ailist'), aiList[i]);
        }

        data = [[], []];
        isValid();
        $('#pregame').hide();
        $('#game').fadeIn();
        $('#keyboard').fadeIn();
    }

</script>

<div id="error" style="display: none"></div>

<table class="ma keyboard" id="keyboard" style="display: none"><tbody><tr>
        <?php
        $alphabet = get_alphabet();
        foreach($alphabet as $key => &$val) {
            echo '<td class="drag p5">' . $val . '</td>';
        }
        ?>
    </tr></tbody></table>
<div class="ma base normal">
<div class="mt50">

    <div class="cntr link" id="pregame"><?php echo (game_exists(session_id()) ? _("Продолжить игру") : _("Начать игру")) ?></div>
    <div class="cntr" id="game" style="display: none">
        <div id="status" class="mb20 status"></div>
        <table class="ma" border="0" cellspacing="0" cellpadding="0" width="410px">
            <tbody>
            <tr>
                <td align="left" class="medium p10"><div><img src="../public/img/user.png" class="mr10 vam" /><span id="userscore" class="vam"></span></div></td>
                <td align="right" class="medium p10"><div><span id="aiscore" class="vam"></span><img src="../public/img/ai.png" class="ml10 vam" /></div></td>
            </tr>
            </tbody>
        </table>
        <table class="ma table mt20" id="field" border="0" cellspacing="0" cellpadding="0" width="410px">
            <tbody>
            <?php
                for($i = 0; $i < SIZE; $i++) {
                    echo "<tr>";
                    for($j = 0; $j < SIZE; $j++) {
//                        echo '<td class="td"><div style="position:relative; width:100%; height:100%;"><img src="../public/img/arrow.png" style="position: absolute; top: -12px; left: 28px;"/><div style="vertical-align: middle; width:100%; height:100%;"><div>А</div></div></div></td>';
                        //echo '<td class="td" style="position:relative;"><img src="../public/img/arrow.png" style="position: absolute; top: -12px; left: 28px;"/>А</td>';
                        echo '<td class="td" style="position:relative;"></td>';
                    }
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>

        <div class="mt40">
            <div id="submit" class="link minor"><?= _("Завершить ход") ?></div>
            <div id="spinner" style="display: none"><img src="../public/img/spinner.gif"/></div>
        </div>

        <table class="ma mt40" id="wordlist" border="0" cellspacing="0" cellpadding="0" width="720px" style="display: none">
            <tbody>
            <tr>
                <td width="340px" class="br"><table class="fw"><tbody id="userlist"></tbody></table></td>
                <td class="bl"><table class="fw"><tbody id="ailist"></tbody></table></td>
            </tr>
            </tbody>
        </table>
    </div>

</div>
</div>
</body>
</html>
