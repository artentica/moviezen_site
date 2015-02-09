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



    	 <!--TEST!!!!!!!!!!!!!!!!!!!!!!-->
		<script src="js/new_design/skel.min.js"></script>
		<script src="js/new_design/skel-layers.min.js"></script>
		<script src="js/new_design/init_index.js"></script>
		<noscript>
			<link rel="stylesheet" href="CSS/new_design/skel.css" />
			<link rel="stylesheet" href="CSS/new_design/style.css" />
			<link rel="stylesheet" href="CSS/new_design/style-desktop.css" />
		</noscript>
<!--FIN TEST-->
        <link rel="stylesheet" type="text/css" href="CSS/global.css">
        <link rel="stylesheet" type="text/css" href="CSS/menu.css">


</head>
<body>
    <div id="banniere" style="background-image: url('CSS/new_design/images/header.jpg');
background-size: cover;">
        <h1>
            Moviezen
        </h1>
    </div>
    <header>
       <?php
        include 'includes/panel-index.php';?>


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
<!--	 Footer Wrapper
			<div id="footer-wrapper">
				<footer id="footer" class="">
					hgvhvhvhjcvhgvhv
				</footer>
			</div>-->


</body>
</html>
