<?php
/**
 * Cette page entière est destinée à utiliser la fonction deleteNotif() de modele.php
 */
include_once("modele.php");
$idNotif = $_POST['idNotif'];

deleteNotif($idNotif);

?>