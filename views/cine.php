<?php
    session_start();
    include_once("../includes/fonctions.php");

    connect();
    $_SESSION["inscrit"]=0;

    if(!empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["classe"]) && !empty($_POST["mail"])){
        if(ajoutInscrit($_POST["nom"],$_POST["prenom"],$_POST["mail"],$_POST["classe"],$_POST["select_projection"])){
            $_SESSION["select_projection"]=$_POST["select_projection"];
            $_SESSION["inscrit"]=1;
            $mail = protect($_POST["mail"]);
            $_SESSION["mail"]=$mail;
        }
    }

    if(!empty($_POST["del_mail"])){
        if(supprInscrit($_POST["del_mail"],$_SESSION["select_projection"])){
            $_SESSION["inscrit"]=0;
            unset($_SESSION["mail"]);
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
    <link rel="stylesheet" type="text/css" href="../CSS/menu.css">

</head>
<body>
    <img src="../Images/moviezen2.jpg" alt="bannière" id="banniere"/>
    <header>
        <?php
        include '../include/menu-mobile.php';
       include '../include/panel-global.php';?>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">
            <!-- 

            ESPACE réservé au film projeté, et aux diverses informations le concernant (date, durée, bref résumé ? Auteur, acteurs, etc....



            -->
            
            <?php
            if(!$_SESSION["inscrit"] && empty($_SESSION["mail"])){
                
                echo('<h1>S\'inscrire pour la projection</h1>
            <p>Merci de renseigner tout les champs</p>
			<form method="post" action="cine.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="nom">Nom : </label></span><input name="nom" id="nom" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="prenom">Prénom : </label></span><input type="text" name="prenom" id="prenom" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="classe">Classe : </label></span><input type="text" name="classe" id="classe" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="mail">@ ISEN : </label></span><input type="email" name="mail" id="mail" placeholder="Essai.tarte@orange.fr" class="form-control" aria-describedby="basic-addon1" required/></div>
                
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="select_projection">Projection : </label><select name="select_projection" id="select_projection">
                    ');
                
                $result = recupProj();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $nom = $row["nom"];
                    $date = $row["date_projection"];
                    $date = date("d/m/Y", strtotime($date));
                    echo('<option value="'.$nom.'">'.$nom.' projeté le '.$date.'</option>');
                }
                $result->close();
                echo('
                </select></div>
                <input type="submit" class="btn btn-success" value="S\'inscrire pour le film"/>
            </form>
            
            ');
                
            }

            else{
                echo('<h1>Se désinscrire de la projection</h1>
            <form method="post" action="cine.php" id="form-register">
                <input type="hidden" name="del_mail" id="del_mail" value="'.$_SESSION["mail"].'"/>
                <input type="submit" class="btn btn-danger" value="Se désinscrire"/>
            </form>');
                
            }

            ?>
            
		</div>
	</div>
    
    
</body>
</html>
