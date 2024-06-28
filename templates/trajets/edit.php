<?php
include_once 'libs/modele.php';
$trip = getTrip(valider("id"));
$car  = getCar($trip['vehicle_id']);
?>
<script>
    function changeIconColor(){
        $(".icons:eq(1)").css("fill", "orange");
    }
</script>
<form action="controleur.php?id=<?= $trip['id'] ?>" method="POST">
    <label for="destination">Où allez vous ?</label> <input type="text" name="destination" id="destination" placeholder="Départ" value=<?= $trip['arrival'] ?>> <br>
    <label for="departure">D'où partez vous ?</label> <input type="text" id="departure" name="departure" placeholder="Point de rencontre" value="<?= $trip['departure'] ?>"><br>
    <label for="date">Quel jour partez vous ?</label> <input type="date" id="date" name="date" min=date.now() value="<?= $trip['date'] ?>" ><br>
    <label for="time">A quelle heure ?</label> <input type="time" id="time" name="time" <?= $trip['hour'] ?>><br>
    <label for="passengers">Combien de passagers prenez-vous ?</label> <input type="number" min=1 name="passengers" id="passengers" value="<?= $trip['passenger'] ?>"><br>
    <label for="driver">Je conduis</label> <input type="checkbox" name="driver" id="driverd" <?= $trip['creator_id'] == $trip['driver_id'] ? "checked" : "" ?>>
    <label for="car-sel">Voiture</label><select name="car" id="car-sel">
        <option>Aucune</option>
        <?php if ($car) : ?> <option value="<?= $car['id'] ?>" selected><?= $car['registration'] ?></option> <?php endif; ?>
    </select>
    <input type="hidden" name="action" value="trajets.edit">
    <input type="submit" value="Éditer" />
</form>

<script>
    $("#date").change(() => {
        $.ajax({
            type: "POST",
            url: "/controleur.php",
            data: {"date":$("#date").val(), "action":"ajax.getAvailableCars", "edit":<?= $trip['id'] ?>},
            dataType: "json",
            // headers: {"debug-data":true},
            error : function () {
                console.log("error");
            },
            success: function(oRep){
                console.log(oRep);
                let carsel = $("#car-sel");
                carsel.html("<option>Aucune</option>");
                for (const car of oRep['cars']) {
                    carsel.append($("<option></option>").html(car.registration).val(car.id))
                }
            }
        });
    })

</script>