<?php
/*
 * A session tulajdonságainak módosítása
 * 
 */
 
class Session {
  
    function __construct() {
      $sessionTimeOut = 9*60*60;
      ini_set("session.gc_maxlifetime", $sessionTimeOut );
      session_set_cookie_params($sessionTimeOut);
      session_start();
  } // END __construct
    
} // END Session

?>
