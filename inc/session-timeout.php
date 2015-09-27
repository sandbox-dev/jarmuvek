<?php

// 9 óra SESSION idő 
// Ha nem lenne elég :-)
ini_set("session.gc_maxlifetime", 32400 );
session_set_cookie_params(32400);

?>
