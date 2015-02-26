<?php
    session_start();
    include_once("../includes/fonctions.php");
include_once("../includes/function_global.php");
    connect();

    foreach( $_POST as $cle=>$value )
        {
            $_POST[$cle] = strip_tags(htmlentities($value, ENT_QUOTES, 'UTF-8'));
        }


    if(!empty($_POST["rendu_lot_id"]) && !empty($_POST["rendu_lot_lots"]) && !empty($_POST["rendu_lot_date_emprunt"]) && !empty($_POST["rendu_lot_date_retour"])){
        renduLot($_POST["rendu_lot_id"],$_POST["rendu_lot_lots"],$_POST["rendu_lot_date_emprunt"],$_POST["rendu_lot_date_retour"]);
    }
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Calendrier des emprunts</title>
	<link rel="stylesheet" type="text/css" href="../CSS/index.css">
	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">
    <link rel="stylesheet" href="../CSS/calendar.min.css">

	<?php
        include '../includes/include_on_all_page.php';
    ?>
</head>
<body>
    <div id="banniere"  style="background-image: url('../CSS/new_design/images/header.jpg');
background-size: cover;">
        <h1>
            Moviezen
        </h1>
    </div>
    <header>
       <?php
       include '../includes/panel-global.php';
?>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">
            <legend id="calendrier">Calendrier des emprunts</legend>
            <div class="page-header">
                <div class="pull-right form-inline">
                    <div class="btn-group">
                        <button class="btn btn-primary" data-calendar-nav="prev"><< Prev</button>
                        <button class="btn" data-calendar-nav="today">Today</button>
                        <button class="btn btn-primary" data-calendar-nav="next">Next >></button>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-warning" data-calendar-view="year">Year</button>
                        <button class="btn btn-warning active" data-calendar-view="month">Month</button>
                        <button class="btn btn-warning" data-calendar-view="week">Week</button>
                    </div>
                </div>
		     </div>
            <div id="calendar"></div>
            <div class="modal hide fade" id="events-modal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Event</h3>
                </div>
                <div class="modal-body" style="height: 400px">
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                </div>
	       </div>
            <?php


                if(!empty($_SESSION["authentifie"])){
                    echo('
                    <legend id="table_emprunt">Gestion de la rendu des lots</legend>
                    <table class="table table-striped table-bordered"><thead><th>Empretant</th><th>Lots empruntés</th><th>Date d\'emprunt</th><th>Date de retour</th><th>Marquer l\'emprunt comme rendu</th></thead>');
                    $result = recupEmpruntLot();
                    while ($row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $identifiant = $row["inscrit_mail"];
                        $lots = $row["concat_lots"];
                        $date_emprunt = $row["date_emprunt"];
                        $date_retour = $row["date_retour"];
                        setlocale (LC_TIME, 'fr_FR','fra');
                        $date_emprunt_formatée = utf8_encode(strftime("%d %b %Y",strtotime($date_emprunt)));
                        $date_retour_formatée = utf8_encode(strftime("%d %b %Y",strtotime($date_retour)));
                        echo('<tr><td>'.$identifiant.'</td><td>'.$lots.'</td><td>'.$date_emprunt_formatée.'</td><td>'.$date_retour_formatée.'</td><td><form method="post" action="calendrier.php#table_emprunt" id="form-register">
                            <input type="hidden" name="rendu_lot_id" id="rendu_lot_id" value="'.$identifiant.'" required/>
                            <input type="hidden" name="rendu_lot_lots" id="rendu_lot_lots" value="'.$lots.'" required/>
                            <input type="hidden" name="rendu_lot_date_emprunt" id="rendu_lot_date_emprunt" value="'.$date_emprunt.'" required/>
                            <input type="hidden" name="rendu_lot_date_retour" id="rendu_lot_date_retour" value="'.$date_retour.'" required/>
                            <input type="submit" class="button dark_grey" value="Cet emprunt a bien été rendu"/>

                        </form></td></tr>');
                    }
                    $result->close();
                }
            ?>

        </div>
	</div>

<script type="text/javascript" src="../js/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="../js/underscore-min.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/jstz.min.js"></script>
<script type="text/javascript" src="../js/language/fr-FR.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/app.js"></script>
<script type="text/javascript">
    var calendar = $("#calendar").calendar(
        {
            language: 'fr-FR',
            tmpl_path: "./tmpls/",
            events_source: './events.json.php'
        });
</script>
</body>
</html>
