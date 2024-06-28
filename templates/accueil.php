<?php
include_once ('libs/modele.php');
//C'est la propriété php_self qui nous l'indique : 
// Quand on vient de index : 
// [PHP_SELF] => /chatISIG/index.php 
// Quand on vient directement par le répertoire templates
// [PHP_SELF] => /chatISIG/templates/accueil.php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
// Pas de soucis de bufferisation, puisque c'est dans le cas où on appelle directement la page sans son contexte
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=accueil");
	die("");
}

?>
<script>
    function changeIconColor(){
        $(".icons:eq(2)").css("fill", "orange");
    }
</script>

<div id="corps">

<h1>Accueil</h1>
    <a href="?view=trajets.create"><button class="primary">Créer un trajet</button></a>
    <h2>Mes trajets</h2>
    <div class="my-trips">
        <?php if ($trips = getUserTrips(valider("idUser", "SESSION"))): ?>
        <?php foreach ($trips as $trip): ?>
                <div class="trip">De <?= $trip['departure'] ?> à <?= $trip['arrival'] ?> le <?= date_format(date_create($trip['date']), "j/n/Y")?> à <?= $trip['hour'] ?><a href="?view=trajets.view&id=<?= $trip['id'] ?>">view</a></div>
        <?php endforeach; ?>
        <?php else: ?>
            <h3>Vous n'avez aucun trajet de prévu !</h3>
        <?php endif; ?>
    </div>

</div>
