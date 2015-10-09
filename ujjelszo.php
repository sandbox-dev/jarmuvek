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
$h->header("Új jelszó megadása");

$h->msgWarn("Az alapértelmezett jelszóvel lépett be.<br>Változtassa meg a jelszavát!");

if(isset($_POST['save']) && $_POST['save'] != '') {
  $sql = "select jelszocsere('$_POST[id]','$_POST[defaultPassword]','$_POST[pass1]','$_POST[pass2]')";
  $res = $pg->query($sql);
  $row = $res->fetch(PDO::FETCH_BOTH)[0];
  switch ($row) {
    case 0:
      $h->msgOk("A jelszó megváltoztatása sikerült!");
      $_SESSION['userDefaultPassword'] = 0;
        $h->btnMenu(array("Vissza"=>"index.php"));
      break;
    case 1:
      $h->msgErr("A felhasználó azonosítása nem sikerült!");
      $h->btnMenu(array("Újra"=>"ujjelszo.php"));
      break;
    case 2:
      $h->msgErr("A megadott jelszavak nem egyeznek meg!");
      $h->btnMenu(array("Újra"=>"ujjelszo.php"));
      break;
  }
  
}
else {
  echo "<form method='post' action='ujjelszo.php'>
  <p style='display:inline;'>
    <input style='font-size:1em;' type='password' name='pass1' maxlength='50' 
      placeholder='Adja meg az új jelszavát!' required autofocus />
    <input style='font-size:1em;' type='password' name='pass2' maxlenght='50'
      placeholder='Ismételje meg az új jelszavát!' required /> <br>
    <input style='font-size:1em;' type='submit' name='save' value='Mentés' />
    <input style='font-size:1em;' type='reset' name='reset' value='Jelszómezők törlése' />
  </p>
    <input type='hidden' name='id' value='$_SESSION[userId]' />
    <input type='hidden' name='defaultPassword' value='init' />
  </form>";
}


// Oldal alja gombok:
$h->separator();
$h->btnBack();
$h->btnExit();
$h->btnPrint();
$h->footer();    
  

?>
