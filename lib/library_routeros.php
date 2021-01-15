<?php


require(realpath(dirname(__FILE__) . '/vendor/routeros_api.class.php'));
require(realpath(dirname(__FILE__) . '/config.php'));

$API = new RouterosAPI();
$API_Config = new RouterosAPI_Config();

/*********** setup config *************/
$API->debug = $API_Config->debug;
$API->connected = $API_Config->connected;
$API->port = $API_Config->port;
$API->ssl = $API_Config->ssl;
$API->timeout = $API_Config->timeout;
$API->attempts = $API_Config->attempts;
$API->delay = $API_Config->delay;


/*********** init API *************/
$API_connection = $API->connect($API_Config->host, $API_Config->username, $API_Config->password);

/*********** utilities *************/
function utf8_converter($object)
{
    if ($object) {
        if (is_array($object)) {
            array_walk_recursive($object, function (&$item, $key) {
                if (!mb_detect_encoding($item, 'utf-8', true)) {
                    $item = iconv(mb_detect_encoding($item, mb_detect_order(), true), "UTF-8", $item);
                }
            });
        } else {
            iconv(mb_detect_encoding($object, mb_detect_order(), true), "UTF-8", $object);
        }
        return $object;
    }
    return null;
}

/*********** library *************/

class routeros_api_library
{
    public function get_users()
    {
        global $API_connection;
        global $API;
        if ($API_connection) {
            $API->write('/ip/hotspot/user/print');
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);
            $API->disconnect();
        }
        unset($ARRAY[0]); 
        return utf8_converter($ARRAY);
    }
}
