<?php
session_start();
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Site de Moviezen</title>
	<link rel="stylesheet" type="text/css" href="CSS/index.css">
	<link rel="stylesheet" type="text/css" href="CSS/bootstrap.css">
</head>
<body>
    <img src="Images/moviezen2.jpg" alt="bannière" id="banniere"/>
    <header>
        <nav class="navbar navbar-default">
  			<div class="container-fluid">
  				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active"><a href="index.php">Présentation du club</a></li>
						<li><a href="views/cine.php">Coté Ciné de l'ISEN</a></li>
                        <li><a href="views/emprunt.php">Emprunt de matériel</a></li>
                        <li><a href="views/admin.php">Espace Administrateur</a></li>
					</ul>
				</div>
			</div>
		</nav>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">
			<h1>Moviezen, qu'est ce que c'est ?</h1>
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