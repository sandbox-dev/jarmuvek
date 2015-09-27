<?php

/*
 * Html osztály 
 * 
 * Különboző html elemek kezeléséhez
 * 
 */

class Html {

/*
 * Html fejléc.
 */
public function header($title) {
if (!isset($title)) {
  $title='Járművek';
}
echo <<<EOL
<!doctype html>
<html lang=hu>
<head>
<meta charset='utf-8' />
<link rel='stylesheet' href='css/jarmuvek.css' media='screen, print'/>
<link rel='shortcut icon' href='icon/favicon.ico' type='image/x-icon'>
<link rel='icon' href='icon/favicon.ico' type='image/x-icon'>
<meta name=viewport content='width=device-width, initial-scale=1'>
<title>$title</title>
<script>
// várakozás (millisesonds) ideig
function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}



</script>
</head>
<body>
<header>
<h1>BKV Vasúti Járműjavító Kft.</h1>
<h2>Járműjavítás információs rendszere</h2>
</header>
EOL;
}

/*
 * Html lábléc.
 */
public function footer() {
  Html::login();
echo <<<EOL
<footer>
<p>&copy;szikoragy</p>
</footer>
</body>
</html>
EOL;
}

public function abortFooter() {
echo <<<EOL
<footer>
<p>&copy;szikoragy</p>
</footer>
</body>
</html>
EOL;
exit;
}

/*
 * Üzenetek
 * - hiba
 * - figyelmeztetés
 * - ok, rendben
 */  
public function msgErr($msg) {
  echo "<p class='err'>$msg</p>";
}
public function msgWarn($msg) {
  echo "<p class='warn'>$msg</p>"; 
}
public function msgOk($msg) {
  echo "<p class='ok'>$msg</p>";
}

/*
 * Gombok, gombképek
 */
// Vissza gomb
public function btnBack() {
  echo "<p class='btn'><a class='btn-back' href='index.php'>Vissza</a></p>";
}
// Kilép gomb
public function btnExit() {
    $ip = $_SERVER['SERVER_ADDR'];
    if ($ip == '::1' || $ip == '192.168.1.100') {
      echo "<p class='btn'><a class='btn-exit' href='http://localhost/jarmuvek/index.php'>Kilépés</a></p>";
    }
    else {
      echo "<p class='btn'><a class='btn-exit' href='http://192.168.100.155/index.php'>Kilépés</a></p>";
    }
}
// Nyomtatás gomb
public function btnPrint() {
  echo "<p class='btn'><a class='btn-print' href='javascript:window.print();'>Nyomtatás</a></p>";  
}  
// Főmenü gombjai
public function btnMenu($menu) {
  echo "<p>";
  foreach ($menu as $title => $href) {
    echo "<a class='btn-menu' href='$href'>$title</a>";
  }
  echo "</p>";
}  
// Ki és bejelentkezés gombja
private function login() {
  if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    echo "<p class='btn'><b>Felhasználó:&nbsp;</b>$_SESSION[userFullName]&nbsp;&mdash;&nbsp;$_SESSION[userAuth]&nbsp;</p>";
    echo "<p class='btn'><a class='btn-logout' href='logout.php'>Kijelentkezés</a></p>";
  }
  else {
    echo "<p class='btn'><a class='btn-login' href='login.php'>Bejelentkezés</a></p>";
  }
}

// Menü és tartalom közötti elválasztó vonal
public function separator() {
  echo "<p class='separator'></p>";
}  


  
  
  
  
  
/*
 * class Html vége
 */  
}

?>
