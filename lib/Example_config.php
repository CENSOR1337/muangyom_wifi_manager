<?php
class RouterosAPI_Config
{
    var $host      = ""; // Mikrotik host ip
    var $username  = "admin"; // Mikrotik login username
    var $password  = "123456"; // Mikrotik login password
    var $debug     = true; //  Show debug information
    var $connected = false; //  Connection state
    var $port      = 8728;  //  Port to connect to (default 8729 for ssl)
    var $ssl       = false; //  Connect using SSL (must enable api-ssl in IP/Services)
    var $timeout   = 3;     //  Connection attempt timeout and data read timeout
    var $attempts  = 5;     //  Connection attempt count
    var $delay     = 3;     //  Delay between connection attempts in seconds

}

?>