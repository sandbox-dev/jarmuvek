<?php
/*
 * Auto loader osztály
 * Szükséges fájlok betöltését végző osztály
 * Az osztályoknak a class/ könyvtárban kell lenniük,
 * és .php kiterjeszéssel kell rendelkezniük.
 * 
 * Az osztályokat és hibaüzeneteket a $classes tömbben kell tárolni.
 * 
 * Az adatbázis init fájl helye az inc/ könyvtárban pg-init.php néven,
 * statikusan van beállítva!
 * 
 * Hiba esetén a program üzenetet küld és megállítja a működést.
 * 
 */

class Autoloader {
  
  const DIR = "class/class.";
  const EXT = ".php";
  const PG_INIT = "inc/pg-init.php";
  
  private static $classes = array(
  "Html"=>"Hiányzik a megjelenítést vezérlő osztály!",
  "Pg"=>"Hiányzik az adatbázis kapcsoló fájl!"
  );
  
  function __construct() {
    try {
      foreach (self::$classes as $class => $errorMessage) {
        $requiredFile = self::DIR.$class.self::EXT;
        if (file_exists($requiredFile)) {
          require_once $requiredFile;
        }
        else {
          throw new Exception($errorMessage);
        }
      } //END foreach
    } // END try
    catch (Exception $e) {
      echo "
      <!doctype html>
      <html lang=hu>
        <head>
        <meta charset='utf-8' />
        <title>Hiba!</title>
        </head>
        <body>
          <h1 style='background-color:#FF928D;color:red;width:50%;
          text-align:center;margin:5em auto;'>";
            echo $e->getMessage();
      echo "
          </h1>
        </body>
      </html>
      ";
      exit;
    } // END catch
  } // END __construct
} // END class
?>
