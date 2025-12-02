<?php
    //small steamcharts parser

    //result in console
    function saveInConsole($result) {
        //see result in console
        echo "#####RESULT#####\n";
        foreach($result as $r) {
            echo "#Parsed at: " . $r['time'] . "\n";
            echo "Game title: " . $r['title'] . "\n";
            echo "Current online: " . $r['now'] . "\n";
            echo "Max per day: " . $r['day'] . "\n";
            echo "Average per month: " . $r['month'] . "\n";
            echo "----------------------\n";
        }
    }

    //save result as json file
    function saveAsJson($array) {
        $file_path = __DIR__ . '/scJSON.json'; #save to place where starting script
        
        //save json file
        file_put_contents($file_path, PHP_EOL . json_encode($array, JSON_PRETTY_PRINT));
    }

    //main parsing function(start for each url):
    function parseUrl($url) {
        libxml_use_internal_errors(true);  //html warning in buffer
    
        $html = fetch($url);

        //if curl error -> fetch return null => exit for next url
        if(!$html) return null;

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        libxml_clear_errors(); //clean buffer with html warnings

        $xpath = new DOMXPath($dom);

        //get game title
        $g_title = xpathContent($xpath, "//h1//a", false, 'no have title');

        //get current game online
        $g_online = xpathContent($xpath, "//div[@class='app-stat'][.//abbr[contains(@class, 'timeago')]]", true, 'no have current online');

        //get max online for 24 hours
        $g_online_day = xpathContent($xpath, "//div[@class='app-stat'][contains(., 'peak')]//span[@class='num']", true, 'no have online per day');

        //get average online for 30 days
        $g_online_month = xpathContent($xpath, "//td[@class='right num-f italic']", true, 'no have average online per month');

        //time of parsing
        $time_parse = date('Y-m-d H:i:s');

        //make array of game result
        $game_res = [
            'time' => $time_parse,
            'title' => $g_title,
            'now' => $g_online,
            'day' => $g_online_day,
            'month' => $g_online_month
        ];

        //send result
        return $game_res;
    }

    //function to get content from xpath
    function xpathContent($xpath, $query_path, $isInt, $flag) {
            $node = $xpath->query($query_path);
            $node_result = $node->length > 0
                ? ($isInt //check if need int result
                    ? (int)trim($node->item(0)->textContent)
                    : trim($node->item(0)->textContent))
                : $flag;

            //return result
            return $node_result;
        }
    
    //safe parsing
    function fetch($url) {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => __DIR__.'/cookies.txt',
        CURLOPT_COOKIEFILE => __DIR__.'/cookies.txt',
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)",
            "Accept: text/html",
            "Accept-Language: en-US,en;q=0.9",
        ]
    ]);

    $html = curl_exec($ch);
    $errNumber = curl_errno($ch);
    $errMsg = curl_error($ch);
    $curlDetails = curl_getinfo($ch);
    curl_close($ch);

    //if has any curl error
    if($errNumber) {
        logParse('ERROR', 'Curl failed', ['url' => $url, 'errNumber' => $errNumber, $errMsg => $errMsg]);

        return null;
    }

    //if has http error
    $httpCode = $curlDetails['http_code'] ?? 0;
    if($httpCode >= 400) {
        logParse('Warning', 'Http error', ['url' => $url, 'code' => $httpCode]);

        return null;
    }

    //delay
    usleep(rand(500000, 1500000)); // 0.5 - 1.5 sec

    //write result of curl in parser.log 
    logParse('OK', 'Curl is ok', ['url' => $url, 'details' => $curlDetails]);

    return $html;
    }

    //log function
    function logParse($tag, $msg, $array = []) {
        $file = 'parser.log';
        $time = date('Y-m-d H:i:s');
        $arrayToStr = $array ? ' ' . json_encode($array, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) : '';
        $str = "[$time] [$tag] $msg $arrayToStr" . PHP_EOL;

        //write to file
        file_put_contents($file, $str, FILE_APPEND);
    }
?>