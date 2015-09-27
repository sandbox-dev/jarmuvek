<?php
// Kimenet tárazása
require_once 'inc/session-timeout.php';
session_start();

// Csatlakozás az adatbázishoz.
require_once 'inc/pg-init.php';
require_once 'class/class.Html.php';

// Html példány:
$h = new Html();



// Html fejléc
$h->header("Járművek - Bejelentkezés");

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
    echo '<script>sleep(1000);window.location.assign("index.php");</script>';
    $sql = "select nev,jog from felhasznalo where id='$_POST[username]'";
    $res = $pg->query($sql);
    $row = $res->fetch(PDO::FETCH_BOTH);
    $_SESSION['userFullName'] = $row[0];
    $_SESSION['userAuth'] = $row[1];
  }
}


?>
<form method='post' action='login.php'>
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
