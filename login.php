<?php
session_start();
require_once 'autoloader.php';
new Autoloader();
$pg = Pg::getPg();

// Html példány:
$h = new Html();



// Html fejléc
$h->header("Bejelentkezés");
$h->msgErr("Bejelentkezés");

// Bejelentkezés űrlap ellenőrzése
if (isset($_POST['login']) && $_POST['login'] == 'Bejelentkezés') {
  $sql = "select bejelentkezes('$_POST[username]', '$_POST[password]')";
  $res = $pg->query($sql);
  $notSuccess = $res->fetch(PDO::FETCH_BOTH)[0];
  if ($notSuccess) {
    $h->msgErr("Sikertelen bejelentkezés!");
    $_SESSION['login'] = false;
  }
  else {
    $h->msgOk("Sikeres bejelentkezés");
    $_SESSION['login'] = true;
    $sql = "select nev,jog from felhasznalo where id='$_POST[username]'";
    $res = $pg->query($sql);
    $row = $res->fetch(PDO::FETCH_BOTH);
    $_SESSION['userFullName'] = $row[0];
    $_SESSION['userAuth'] = $row[1];
  }
  $h->redirect("index.php");
}


?>

<form method='post' action='login.php' autocomplete='off'>
<p>
<label for='username'>
<input style='font-size:100%' type='text' name='username' placeholder='felhasználónév' required/>
</label>
<label for='password'>
<input style='font-size:100%' type='password' name='password' placeholder='jelszó' required/>
</label>
</p>
<p>
<input style='font-size:100%' type='submit' name='login' value='Bejelentkezés' />
<input style='font-size:100%' type='reset' name='reset' value='Adatok törlése' />
</p>
</form>

<?php


// Html lábléc
$h->separator();
$h->btnBack();
$h->footer();

// Kimenet kiírása

?>
