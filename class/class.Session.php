<?php
/*
 * A session tulajdonságainak módosítása
 * 
 */
 
class Session {
  
    public static function setSession() {
      if(empty($_SESSION)) {
        $sessionTimeOut = 32400;
        ini_set("session.gc_maxlifetime", $sessionTimeOut );
        session_set_cookie_params($sessionTimeOut);
        session_start();
       }
    }

/*    
*    function __construct() {
*      if(empty($_SESSION)) {
*        $sessionTimeOut = 32400;
*        ini_set("session.gc_maxlifetime", $sessionTimeOut );
*        session_set_cookie_params($sessionTimeOut);
*        session_start();
*       }
*  }  END __construct
*/   
} // END Session

?>
