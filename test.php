<?php


include './lib/library_routeros.php';

$api_lib = new routeros_api_library();

function generateRandomString($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}


//print_r($api_lib->get_users());
print_r($api_lib->remove_user("user1"));