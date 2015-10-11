<?php
session_start();
require_once 'autoloader.php';
new Autoloader();
$pg = Pg::getPg();


$backButtons = array (
"Vissza"=>"index.php"
);


// Html példány:
$h = new Html();
// vissza link
$h->setBackRef();

// Html fejléc
$h->header("Új járműtípus");

$h->msgOk("Új járműtípus rögzítése");

// van elegendő joga?
  if (!isset($_SESSION['userAuth']) || $_SESSION['userAuth'] < 8) {
  $h->msgErr("A funkció eléréséhez be kell jelentkeznie, vagy a jogosultsága nem elegendő a használathoz!");
  $h->btnBack();
  echo "<p class='btn'><a class='btn-login' href='login.php'>Bejelentkezés</a></p>";
  $h->abortFooter();
}

$frmId = null;

if(isset($_POST['save']) && $_POST['save'] != '' ) {
  $frmId = "save";
}
if(isset($_POST['delete']) && $_POST['delete'] != '' ) {
  $frmId = "delete";
}
switch ($frmId) {
  case "save":
    $newType = trim(htmlspecialchars($_POST['newType']));
    $sql = "select ujtipus('$newType')";
    $res = $pg->query($sql);
    $row = $res->fetch(PDO::FETCH_BOTH)[0];
    switch ($row) {
      case 0:
        $h->msgErr("<em>$newType</em> hozzáadása nem sikerült!");
          $h->btnMenu(array("Újra"=>"ujtipus.php"));
        break;
      case 1:
        $h->msgOk("<em>$newType</em> hozzáadása sikerült!");
        $h->btnMenu(array("Vissza"=>"index.php"));
        break;
    }
    break;
  case "delete":
    $delType = trim(htmlspecialchars($_POST['delType']));
    $sql = "select tipus_torlese('$delType')";
    $res = $pg->query($sql);
    $row = $res->fetch(PDO::FETCH_BOTH)[0];
    switch ($row) {
      case 0:
        $h->msgErr("<em>$delType</em> tölése nem lehetséges!");
          $h->btnMenu(array("Újra"=>"ujtipus.php"));
        break;
      case 1:
        $h->msgOk("<em>$delType</em> törlése sikerült!");
        $h->btnMenu(array("Vissza"=>"index.php"));
        break;
    }
    break;
    
  default: 
    $sql = "select * from tipus order by 1";
    $res = $pg->query($sql);
    echo "<table border='1' style='border-collapse:collapse;'>
    <caption style='font-weight:bold;width:10em;'>Létező típusok</caption>";
    while ($row = $res->fetch(PDO::FETCH_BOTH)) {
      echo "<tr><td>$row[0]</td></tr>";
    }
    echo "</table>";
    echo "<form method='post' action='ujtipus.php'>
    <p style='display:inline;'>
      <input style='font-size:1em;' type='text' name='newType' maxlength='20' 
        placeholder='Adja meg az új típust!' required autofocus autocomplete='off' />
      <input style='font-size:1em;' type='submit' name='save' value='Mentés' />
      <input style='font-size:1em;' type='reset' name='reset' value='Bevitel törlése' />
      <input style='font-size:1em;background-color:yellow;' type='button' name='back' 
        value='Mégsem' onclick='toIndex();' />
    </p>
    </form>";
    
    // típus törlése
    $h->separator();
    $h->msgOk("Törölhető járműtípusok");
    $sql = "select distinct jarmutipus,tipus.id from jarmu_alap right join tipus 
      on(jarmutipus=tipus.id) where jarmutipus is null";
    $res = $pg->query($sql);
    echo "<datalist id='delTypeList'>";
    while ($row = $res->fetch(PDO::FETCH_BOTH)) {
      echo "<option value='$row[1]'></option>";
    }
    echo "</datalist>";
    echo "<form method='post' action='ujtipus.php'>
    <p style='display:inline;'>
      <input list='delTypeList' name='delType' style='font-size:1em;' required placeholder='Válasszon törölhető típust!'/>
      <input style='font-size:1em;background-color:#FF6C3F' type='submit' name='delete' value='Törlés' />
      <input style='font-size:1em;background-color:yellow;' type='button' name='back' 
        value='Mégsem' onclick='toIndex();' />
    </p>
    </form>";
    break;
}

// Oldal alja gombok:
$h->separator();
$h->btnBack();
$h->btnExit();
$h->btnPrint();
$h->footer();    
  

?>
