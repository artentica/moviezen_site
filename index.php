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
			<h1>Qu'est-ce que Moviezen ?</h1>
            <p>Depuis quelques années, Moviezen fait partie intégrante du domaine Isénien en étant le club cinéma de l'école. Passionnés de vidéo et d'image, nous participons à la plupart des événements requérant des caméras ou une présence télévisuelle. Ceci se caractérise par de nombreuses actions et missions telles que :</p>

            <p>
                </p><ul>
                    <li>Partenariat avec le Multiplexe Liberté sous le nom du "CINE DE L'ISEN" qui offre aux étudiants de regarder des films à prix réduit ainsi que la projection des courts-métrages iséniens avant ces films !</li>
                    <li>Aide aux étudiants de première et deuxième année à la réalisation de courts-métrages dans le cadre de la FHS.</li>
                    <li>Prêt de matériel cinématographique durant toute l'année.</li>
                    <li>Montage des événements ainsi que des soirées ISEN.</li>
		    <li>Réalisation d'émissions et de vidéos originales.</li>
                </ul>
            <p></p>
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
