SteamCharts Parser
===================

General
-------
Simple parser for the website https://steamcharts.com/.
The script retrieves:
- current online of a game,
- maximum online per day,
- average players per month.

File list
---------
parser.php - main parser logic
start.php - console UI logic
urls.txt - list of game URLs for parsing (you can add your own)

Files generated after run:
cookies.txt - session file for stable parsing (optional; safe to delete)
parser.log - log file with curl/HTTP errors and optional success info
scJSON.json - JSON output (created only if you choose JSON mode)

To disable success logs, open parser.php and comment the line:
#logParse('OK', 'Curl is ok', ['url' => $url, 'details' => $curlDetails]);

How to add games
----------------
1) Open https://steamcharts.com/
2) Find a game and open its detailed page
   Example URL: https://steamcharts.com/app/730
3) Copy the URL
4) Open urls.txt and paste the URL on a new line
5) Done — script will parse it next time you run

How to run the script
---------------------
1) Open a console/terminal
2) Go to the script folder, for example:
   cd steamChartsParser
3) Run:
   php start.php
4) The script will ask how to display results:
   - console
   - JSON
   - both

Requirements
------------
PHP 8.0+ with cURL support.

Windows setup:
--------------
Open php.ini and enable (remove the semicolon):
extension=curl
extension=openssl
extension_dir="ext"

If you get: "SSL certificate problem":
Download "cacert.pem" from https://curl.se/ca/cacert.pem
Save into your PHP folder.
Then set in php.ini:
curl.cainfo="PATH\php\cacert.pem"
openssl.cafile="PATH\php\cacert.pem"

Linux setup:
------------
sudo apt update
sudo apt install php-curl
or for specific version:
sudo apt install php8.1-curl

macOS setup:
------------
If installed via Homebrew:
brew install php

