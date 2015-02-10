<?php
    session_start();
    include_once("../includes/fonctions.php");
include_once("../includes/function_global.php");
    connect();


    if(!empty($_POST["lot_rendu"])){
        renduLot($_POST["lot_rendu"]);
    }
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Calendrier des emprunts</title>
	<link rel="stylesheet" type="text/css" href="../CSS/index.css">

	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">
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
            <?php
            echo('<table class="table table-striped"><thead><th>Image du lot</th><th>Composition du lot</th><th>Disponible</th><th style="display:none">Identifiant de lot</th><th>Indisponible jusqu\'au</th><th>Caution du lot</th></thead>');
            $result = recupLot();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $id = $row["id"];
                    $composition = $row["composition"];
                    $caution = $row["caution"];
                    $indisponible = "/";
                    $disponible = '0';


                    $date_ajd = date("z");
                    $query = "SELECT ".$id." FROM dispo WHERE jour=".($date_ajd+1);
                    $result_dispo = $GLOBALS["bdd"]->query($query);
                    while ($row_dispo = $result_dispo->fetch_array(MYSQLI_ASSOC))
                    {
                        $disponible = $row_dispo[$id];
                    }



                    if($disponible){
                        $disponible='<button type="button" class="button dark_grey button-large">
  <span class="glyphicon glyphicon-ok" style="color:green"></span></button>';
                        $class="success";
                    }else{
                        $disponible='<button type="button" class="button dark_grey button-large">
  <span class="glyphicon glyphicon-remove" style="color:red"></span></button>';
                        $query=" SELECT * FROM inscrits_lots WHERE lots='".$id."' ORDER BY `date_retour` DESC LIMIT 1";
                        $result2 = $GLOBALS["bdd"]->query($query);
                        while ($row2 = $result2->fetch_array(MYSQLI_ASSOC)){
                            setlocale (LC_TIME, 'fr_FR','fra');
                            $indisponible = $row2["date_retour"];
                            $indisponible = utf8_encode(strftime("%d %B %Y, %H:%M",strtotime($indisponible)));
                        }
                        $class="danger";
                    }
                    $image = $row["image"];
                    echo('<tr><td><img src="'.$image.'" alt="image" style=""/><td>'.$composition.'</td></td><td >'.$disponible.'</td><td style="display:none">'.$id.'</td><td>'.$indisponible.'</td><td>'.$caution.'&euro;</td>');
                    if(!empty($_SESSION["authentifie"])){
                        echo('<td><form action="calendrier.php" method="post">
                            <input type="hidden" value="'.$id.'" name="lot_rendu" id="lot_rendu">
                            <input type="submit" class="btn btn-success" value="Lot rendu"/>
                        </form></td>');
                    }
                    echo('</tr>');
                }
                $result->close();

            echo('</table>');

            ?>
        </div>
	</div>


</body>
</html>
