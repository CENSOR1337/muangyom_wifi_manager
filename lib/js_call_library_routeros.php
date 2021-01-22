<?php
header("Content-Type: application/json");

require(realpath(dirname(__FILE__) . '/library_routeros.php'));
$api_lib = new routeros_api_library();
$Autha

// this is for security check
if (false) {
    return  print json_encode("Auth needed");;
}

if (isset($_POST['action']) && method_exists($api_lib, $_POST['action']) && $auth_token) {

    $action = $_POST['action'];
    $parameter = ($_POST['parameter'] != null) ? $_POST['parameter'] : null;

    if (is_array($parameter)) {
        $getData = call_user_func_array(array($api_lib, $action), $parameter);
    } else {
        $getData = $api_lib->$action($parameter);
    }
    print json_encode($getData);
}
