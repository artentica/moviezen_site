<?php
    session_start();
    include_once("../includes/fonctions.php");
    $projection="";

    connect();

    $SESSION["inscrit"]=0;

    if(!empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["classe"]) && !empty($_POST["mail"])){
        if(ajoutInscrit($_POST["nom"],$_POST["prenom"],$_POST["mail"],$_POST["classe"],$projection)){
            $SESSION["inscrit"]=1;
            $mail = protect($_POST["mail"]);
            $SESSION["mail"]=$mail;
        }
    }

    if(!empty($_POST["del_mail"])){
        if(supprInscrit($_POST["del_mail"],$projection)){
            $SESSION["inscrit"]=0;
        }
    }
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Du côté du CINE de l'ISEN</title>
	<link rel="stylesheet" type="text/css" href="../CSS/index.css">
	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">
</head>
<body>
    <img src="../Images/moviezen2.jpg" alt="bannière" id="banniere"/>
    <header>
        <nav class="navbar navbar-default">
  			<div class="container-fluid">
  				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li><a href="../index.php">Présentation du club</a></li>
						<li class="active"><a href="cine.php">Coté Ciné de l'ISEN</a></li>
                        <li><a href="emprunt.php">Emprunt de matériel</a></li>
                        <li><a href="admin.php">Espace Administrateur</a></li>
					</ul>
				</div>
			</div>
		</nav>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">
            <!-- 

            ESPACE réservé au film projeté, et aux diverses informations le concernant (date, durée, bref résumé ? Auteur, acteurs, etc....



            -->
            
            <?php
            if(!$SESSION["inscrit"] && empty($SESSION["mail"])){
                
                echo('<h1>S\'inscrire pour la projection</h1>
            <p>Merci de renseigner tout les champs</p>
			<form method="post" action="cine.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="nom">Nom : </label></span><input name="nom" id="nom" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1"/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="prenom">Prénom : </label></span><input type="text" name="prenom" id="prenom" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1"/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="prenom">Classe : </label></span><input type="text" name="classe" id="classe" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1"/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="mail">@ ISEN : </label></span><input type="email" name="mail" id="mail" placeholder="Essai.tarte@orange.fr" class="form-control" aria-describedby="basic-addon1"/></div>
                
                <input type="submit" class="btn btn-success" value="S\'inscrire pour le film"/>
            </form>');
                
            }

            else{
                echo('<h1>Se désinscrire de la projection</h1>
            <form method="post" action="cine.php" id="form-register">
                <input type="hidden" name="del_mail" id="del_mail" value="'.$SESSION["mail"].'"/>
                <input type="submit" class="btn btn-danger" value="Se désinscrire"/>
            </form>');
                
            }

            ?>
            
		</div>
	</div>
    
    
</body>
</html>