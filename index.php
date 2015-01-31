<?php
session_start();
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Site de Moviezen</title>
	<link rel="stylesheet" type="text/css" href="CSS/index.css">
	<link rel="stylesheet" type="text/css" href="CSS/menu.css">
	<link rel="stylesheet" type="text/css" href="CSS/bootstrap.css">
	<script src="js/jquery-2.1.3.min.js"></script>
	<script src="js/menu.js"></script>
</head>
<body>
    <img src="Images/moviezen2.jpg" alt="bannière" id="banniere"/>
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
