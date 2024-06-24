<?php

include_once("libs/modele.php");
include_once("libs/maLibUtils.php");
include_once("libs/maLibForms.php");

?>

<div>
    <?php
        $voitures = getUserCar($_SESSION["idUser"]);
        foreach($voitures as $voiture){
            ?>
                <div class="voiture">
                    <img src="../ressources/ec-lille.png" alt="Logo Voiture">
                    <p><?=$voiture["registration"]?></p>
                </div>
            
            <?php
        }
    ?>
</div>