<?php
    session_start();
    include_once("../includes/fonctions.php");
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
	<script src="../js/jquery-2.1.3.min.js"></script>
	<?php
        include '../includes/include_on_all_page.php';
    ?>
</head>
<body>
    <div id="banniere">
        <h1>
            Moviezen
        </h1>
    </div>
    <header>
       <?php
       include '../includes/panel-global.php';

        include '../includes/menu-mobile.php';?>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">
            <?php
            echo('<table class="table table-bordered table-hover"><thead><th>Image du lot</th><th>Identifiant de lot</th><th>Composition du lot</th><th>Disponible</th><th>Indisponible jusqu\'au</th><th>Caution du lot (en euros)</th>');
            if(!empty($_SESSION["authentifie"])){
                    if($_SESSION["authentifie"]){
                        echo('<th>Gestion</th>');
                    }
                }
            echo('</thead>');
                
            $result = recupLot();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $id = $row["id"];
                    $composition = $row["composition"];
                    $disponible = $row["disponible"];
                    $caution = $row["caution"];
                    $indisponible = "/";
                    if($disponible){
                        $disponible="Oui";
                        $class="success";
                    }else{
                        $disponible="Non";
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
                    echo('<tr><td><img src="'.$image.'" alt="image" style="width:150px;height:150px"/></td><td>'.$id.'</td><td>'.$composition.'</td><td class="'.$class.'">'.$disponible.'</td><td>'.$indisponible.'</td><td>'.$caution.'</td>');
                    if(!empty($_SESSION["authentifie"])){
                        if($_SESSION["authentifie"]){
                            echo('<td><form action="calendrier.php" method="post">
                                <input type="hidden" value="'.$id.'" name="lot_rendu" id="lot_rendu">
                                <input type="submit" class="btn btn-success" value="Lot rendu"/>
                            </form></td>');
                        }
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
