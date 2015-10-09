<?php
session_start();
require_once 'autoloader.php';
new Autoloader();
$pg = Pg::getPg();





// Html példány:
$h = new Html();
// vissza link
$h->setBackRef();

// Html fejléc
$h->header("Alapjelszó beállítása");

// Jogosultság ellenőrzése
if (!isset($_SESSION['userAuth']) || $_SESSION['userAuth'] < 8) {
  $h->msgErr("A funkció eléréséhez be kell jelentkeznie, vagy a jogosultsága nem elegendő a használathoz!");
  $h->btnBack();
  echo "<p class='btn'><a class='btn-login' href='login.php'>Bejelentkezés</a></p>";
  $h->abortFooter();
}

$h->msgOk("A kiválasztott felhasználó jelszavának alaphelyzetbe állítása");


$sql="select id,nev from felhasznalo where jog < 9 order by id";
$res = $pg->query($sql);
echo "<datalist id='users'>";
echo "<option value='Válasszon felhasználót!'></option>";
while ($row = $res->fetch(PDO::FETCH_BOTH)) {
  echo "<option value='$row[0]'>$row[1]</option>";
}
echo "</datalist>";

$backButtons = array (
"Vissza"=>"index.php",
"Másik felhasználó"=>"alapjelszo.php"
);

if(isset($_POST['defaultPassword']) && $_POST['defaultPassword'] != '' 
   && isset($_POST['users']) && $_POST['users'] != '' ) {
  $user = htmlspecialchars($_POST['users']);
  $sql = "select alapjelszo('$user')";
  $row = $pg->query($sql);
  $res = $row->fetch(PDO::FETCH_BOTH)[0];
  if($res) {
    $h->msgOk("<em>$user</em> felhasználó alapértelmezett jelszavának beállítása sikeres volt!");
    $h->btnMenu($backButtons);
  }
  else {
    $h->msgErr("<em>$user</em> felhasználó alapértelmezett jelszavának beállítása nem sikerült!");
    $h->btnMenu($backButtons);
  }
}
else {
  echo "<form method='post' action='alapjelszo.php'>
  <p style='display:inline;'>
  <input style='font-size:1em;' list='users' name='users' autofocus >&nbsp;
  <input style='font-size:1em;' type='submit' name='defaultPassword' 
    value='Alapjelszó beállítása' />
  <input style='font-size:1em;' type='button' name='back' 
    value='Mégsem' onclick='toIndex();' />
  </p>
  </form>";
}




// Oldal alja gombok:
$h->separator();
$h->btnBack();
$h->btnExit();
$h->btnPrint();
$h->footer();    
  

?>
