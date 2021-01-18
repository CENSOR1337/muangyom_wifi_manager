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


/*********** utilities *************/
function iso55891_To_tis620_To_utf8($object)
{
    if ($object) {
        if (is_array($object)) {
            array_walk_recursive($object, function (&$item, $key) {
                if (!mb_detect_encoding($item, 'utf-8', true)) {
                    $item = iconv('tis-620', 'utf-8', utf8_decode(utf8_encode($item)));
                }
            });
        } else {
            $object = iconv('tis-620', 'utf-8', utf8_decode(utf8_encode($object)));
        }
        return $object;
    }
    return null;
}

function array_null_key_to_null_value(array $array, array $key_array)
{
    foreach ($key_array as $target_element) {
        if ($target_element == null) {
            continue;
        }
        if (!isset($array[$target_element])) {
            $array[$target_element] = null;
        }
    }
    return ($array);
}

/*********** library *************/

class routeros_api_library
{
    /**
     * add user into Mikrotik
     *
     * @param string      $username         username to be added
     *
     * @return array                Return all users informations
     */
    public function get_users()
    {
        global $API;
        global $API_Config;
        $API_connection = $API->connect($API_Config->host, $API_Config->username, $API_Config->password);
        if ($API_connection) {
            $API->write('/ip/hotspot/user/print');
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);
            $API->disconnect();
            // Thai character
            $ARRAY = iso55891_To_tis620_To_utf8($ARRAY);
            // Use create index with null value insteand of null index
            $returnArray = array();
            $target_array = array(
                "address", "comment", "email", "limit-bytes-in", "limit-bytes-out", "limit-bytes-total", "limit-uptime",
                "mac-address", "name", "password", "profile", "routes", "server", "bytes-in", "bytes-out", "packets-in", "packets-out", "uptime"
            );
            foreach ($ARRAY as $element) {
                $FilteredArray = array_null_key_to_null_value($element, $target_array);
                array_push($returnArray, $FilteredArray);
            }
            return $returnArray;
        }
        return "Can't connect to Mikrotik API";
    }
    /**
     * add user into Mikrotik
     *
     * @param string      $username         username to be added
     * @param string      $password         password of this username to be added
     * @param string      $profile          profile of this username username to be added
     * @param bool        $disabled         status of this username username to be added
     * @param string      $comment          comment of this username username to be added
     *
     */
    public function add_user(string $username, string $password, string $profile, bool $disabled = false, string $comment = null)
    {
        global $API;
        global $API_Config;
        $API_connection = $API->connect($API_Config->host, $API_Config->username, $API_Config->password);
        if ($API_connection) {

            $RESULT = $API->comm("/ip/hotspot/user/add", array(
                "name"     => $username,
                "password" => $password,
                "profile" => $profile,
                "disabled"  => ($disabled ? "yes" : "no"),
                "comment" => $comment
            ));

            if (is_array($RESULT)) {
                if (isset(($RESULT["!trap"][0]["message"]))) {
                    return array("succeed" => 0, "message" => ($RESULT["!trap"][0]["message"]));
                }
            }
            $API->disconnect();

            return array("succeed" => 1, "message" => "");
        }
        return array("succeed" => 0, "message" => "Can't connect to Mikrotik API");
    }


    /**
     * 
     * Remove user from mikrotik
     * 
     * @param string      $username         username to remove
     * 
     */

    public function remove_user($username)
    {
        global $API;
        global $API_Config;
        $API_connection = $API->connect($API_Config->host, $API_Config->username, $API_Config->password);
        if ($API_connection) {
            $API->write('/ip/hotspot/user/remove', false);
            $API->write("=.id=" . $username);
            $RESULT = $API->read();

            if (is_array($RESULT)) {
                if (isset(($RESULT["!trap"][0]["message"]))) {
                    return array("succeed" => 0, "message" => ($RESULT["!trap"][0]["message"]));
                }
            }
            $API->disconnect();

            return array("succeed" => 1, "message" => "");
        }
        return array("succeed" => 0, "message" => "Can't connect to Mikrotik API");
    }

    /**
     * add user into Mikrotik
     *
     * @param string      $username         username to be edited
     * @param string      $password         password of this username to be added
     * @param string      $profile          profile of this username username to be added
     * @param bool        $disabled         status of this username username to be added
     * @param string      $comment          comment of this username username to be added
     *
     */
    public function toggle_disabled_user(string $username, bool $Disabled)
    {

        global $API;
        global $API_Config;
        $API_connection = $API->connect($API_Config->host, $API_Config->username, $API_Config->password);
        if ($API_connection) {

            $RESULT = $API->comm("/ip/hotspot/user/set", array(
                ".id"     => $username,
                "disabled"  => (!$Disabled ? "yes" : "no"),
            ));



            if (is_array($RESULT)) {
                if (isset(($RESULT["!trap"][0]["message"]))) {
                    return array("succeed" => 0, "message" => ($RESULT["!trap"][0]["message"]));
                }
            }
            $API->disconnect();

            return array("succeed" => 1, "message" => "");
        }
        return array("succeed" => 0, "message" => "Can't connect to Mikrotik API");
    }

    /**
     * add user into Mikrotik
     *
     * @param string      $username         username to be edited
     * @param string      $password         password of this username to be added
     * @param string      $profile          profile of this username username to be added
     * @param bool        $disabled         status of this username username to be added
     * @param string      $comment          comment of this username username to be added
     *
     */
    public function edit_user(string $username, string $password, string $profile, bool $disabled = false, string $comment = null)
    {
        global $API;
        global $API_Config;
        $API_connection = $API->connect($API_Config->host, $API_Config->username, $API_Config->password);
        if ($API_connection) {
            $ArrayData = array(
                ".id"     => $username,
                "password" => $password,
                "profile" => $profile,
                "disabled"  => ($disabled ? "yes" : "no"),
            );

            if ($comment != null) {
                $ArrayData["comment"] = $comment;
            }

            $RESULT = $API->comm("/ip/hotspot/user/set", $ArrayData);

            if (is_array($RESULT)) {
                if (isset(($RESULT["!trap"][0]["message"]))) {
                    return array("succeed" => 0, "message" => ($RESULT["!trap"][0]["message"]));
                }
            }
            $API->disconnect();

            return array("succeed" => 1, "message" => "");
        }
        return array("succeed" => 0, "message" => "Can't connect to Mikrotik API");
    }

    public function get_all_user_profiles_infomations()
    {
        global $API;
        global $API_Config;
        $API_connection = $API->connect($API_Config->host, $API_Config->username, $API_Config->password);
        if ($API_connection) {
            $API->write('/ip/hotspot/user/profile/print', true);
            //$API->write('=.proplist=.id,name,default');
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);

            $API->disconnect();
            // Use create index with null value insteand of null index
            $returnArray = array();
            $target_array = array(
                "add-mac-cookie", "address-list", "address-pool", "advertise", "advertise-interval", "advertise-timeout", "advertise-url", "idle-timeout", "incoming-filter", "incoming-packet-mark",
                "keepalive-timeout", "mac-cookie-timeout", "name", "on-login", "on-logout", "open-status-page", "outgoing-filter",
                "outgoing-packet-mark", "rate-limit", "session-timeout", "shared-users", "status-autorefresh", "transparent-proxy"
            );
            foreach ($ARRAY as $element) {
                $FilteredArray = array_null_key_to_null_value($element, $target_array);
                array_push($returnArray, $FilteredArray);
            }

            return $returnArray;
        }

        return array("succeed" => 0, "message" => "Can't connect to Mikrotik API");
    }

    public function get_all_hotspot_profiles_infomations()
    {
        global $API;
        global $API_Config;
        $API_connection = $API->connect($API_Config->host, $API_Config->username, $API_Config->password);
        if ($API_connection) {
            $API->write('/ip/hotspot/profile/print', false);
            $API->write('=.proplist=.id,name,default');
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);
            $API->disconnect();
            // Use create index with null value insteand of null index
            $returnArray = array();
            $target_array = array(".id", "name", "hotspot-address", "dns-name", "html-directory", "html-directory-override", "rate-limit", "http-proxy", "smtp-server", "login-by", "http-cookie-lifetime", "split-user-domain", "use-radius", "default");
            foreach ($ARRAY as $element) {
                $FilteredArray = array_null_key_to_null_value($element, $target_array);
                array_push($returnArray, $FilteredArray);
            }

            return $returnArray;
        }

        return array("succeed" => 0, "message" => "Can't connect to Mikrotik API");
    }
}
