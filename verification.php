<?php
session_start();

if(!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)){

echo '<script type= "text/javascript">
          window.location.href = "login1.php"
      </script>';
}

 ?>
