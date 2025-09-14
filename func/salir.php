<?PHP
include("loginfunciones.php");
session_start();
session_destroy();
 redireccionar("../index.php");
header("Location: ../index.php"); 
exit; 
?>
