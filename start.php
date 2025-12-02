<?php
    include_once 'parser.php';

    //array of games' url for parsing
    $games_url = array_filter(array_map('trim', file('urls.txt')));

    //final array with game info
    $result = [];

    //call parser for each url
    if(sizeof($games_url) > 0) {
        foreach($games_url as $g_url) {
            $game_res = parseUrl($g_url);
            //if null go to next url
            if(!$game_res) {
                echo "Curl failed. Check: parser.log\n";
                continue;
            }

            //add game res to common array
            array_push($result, $game_res);
        }
    } else {
        echo "Incorrect array";
        exit;
    }

    /*Work with result(by user's choice):
        1 => only console;
        2 => only json file saved;
        3 => both;
        7 => exit */
    $view = "------------------\n";
    echo $view;
    echo "Script is done. Result:\n";
    echo $view;
    echo "1 - display in console \n2 - save as json file \n3 - both \n7 - stop script(exit)\n";
    echo $view;

    //user interface
    while(true) {
        echo "Your choice(write number): \n";
        $choice = readline();

        $suc_msg = "#####DONE#####\n";
        switch($choice) {
            case 1:
                saveInConsole($result);
                echo $suc_msg;
                exit;
            case 2:
                saveAsJson($result);
                echo $suc_msg;
                exit;
            case 3:
                saveInConsole($result);
                saveAsJson($result);
                echo $suc_msg;
                exit;
            case 7:
                echo "Script is stopped.";
                exit;
            default:
                echo "You make wrong choice! Try again:\n";
        }
    }
?>