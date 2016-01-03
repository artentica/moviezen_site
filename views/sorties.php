<?php
    session_start();
    include_once("../includes/fonctions.php");
    include_once("../includes/function_global.php");

    connect();
    $tab = recupSortieSemaine();
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Nos Sorties de la semaine</title>

	<!-- Set Viewport Options -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
	<meta name="apple-mobile-web-app-capable" content="yes" />

	<link rel="stylesheet" type="text/css" href="../CSS/index.css">
	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">

    <?php
        include '../includes/include_on_all_page.php';
    ?>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/transit.js"></script>

</head>
<body>
    <div id="banniere"  style="background-image: url('../CSS/new_design/images/header.jpg');
background-size: cover;">
        <h1>
            Moviezen
        </h1>
    </div>
        <?php
       include '../includes/panel-global.php';

?>
    <div class="">
        <?php
            if(!empty($tab["semaine"])){
                echo'<h1>Nos sorties de la semaine</h1><br><h4>(sorties du '.$tab["semaine"].')</h4>';
                echo'<p>'.$tab["description"].'<p>';
                echo'<img src="'.$tab["affiche"].'" alt="Image des films et résumé">';
            }


        ?>
    </div>
</body>
</html>
