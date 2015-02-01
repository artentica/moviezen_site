<?php
session_start();
include_once("includes/function_global.php");
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Site de Moviezen</title>

	<!-- Set Viewport Options -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />


	<link rel="stylesheet" type="text/css" href="CSS/index.css">
       <script src="js/menu.js"></script>
       	<link rel="stylesheet" type="text/css" href="CSS/bootstrap.css">

        <link rel="stylesheet" type="text/css" href="CSS/menu.css">
	<link rel="stylesheet" type="text/css" href="CSS/global.css">
	<script src="js/jquery-2.1.3.min.js"></script>
</head>
<body>
    <div id="banniere">
        <h1>
            Moviezen
        </h1>
    </div>
    <header>
       <?php
        include 'includes/panel-index.php';
        include 'includes/menu-mobile-index.php';?>


    </header>
    <div class="panel panel-default">
		<div class="panel-body">
			<h1>Moviezen, qu'est-ce que c'est ?</h1>
            <p>Nous sommes un club vidéo affilié à l'ISEN Brest. Passionés de vidéo et d'image, nous participons à la plupart des événements requérant des caméras ou une présence télévisuelle.</p>

            <p>Nos missions sont diverses et variées :
                <ul>
                    <li>Organisation du Ciné de l'ISEN, des projections de film aux tarifs avantageux en partenariat avec le Multiplexe Liberté de Brest</li>
                    <li>Montage de vidéos de soirées pour promouvoir les soirées Iséniennes</li>
                    <li>Organisation de soirées de projections de courts-métrages</li>
                    <li>Prêt de matériel audiovisuel pour les étudiants de l'ISEN Brest afin de réaliser les courts-métrages demandés en 1ère et 2ème année</li>
                </ul>
            </p>
		</div>
	</div>


</body>
</html>
