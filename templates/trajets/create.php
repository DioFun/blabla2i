<form action="controleur.php" method="POST">
    <label for="destination">Où allez vous ?</label> <input type="checkbox" id="destination" name="destination"/> <br>
    <label for="departure">D'où partez vous ?</label> <input type="text" id="departure" name="departure" placeholder="Point de rencontre"><br>
    <label for="date">Quel jour partez vous ?</label> <input type="date" id="date" name="date" min=date.now()><br>
    <label for="time">A quelle heure ?</label> <input type="time" id="time" name="time"><br>
    <label for="passengers">Combien de passagers prenez-vous ?</label> <input type="number" min=1 name="passengers" id="passengers"><br>
    <label for="driver">Je conduis</label> <input type="checkbox" name="driver" id="driverd">
    <label for="car-sel">Voiture</label><select name="car" id="car-sel">
        <option>Aucune</option>
    </select>
    <input type="hidden" name="action" value="trajets.create">
    <input type="submit" value="Créer" />
</form>


<script>
    $("#date").change(() => {
        $.ajax({
            type: "POST",
            url: "/controleur.php",
            data: {"date":$("#date").val(), "action":"ajax.getAvailableCars"},
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