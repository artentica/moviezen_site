<?php
    session_start();
    include_once("../includes/fonctions.php");
    connect();



    if(!empty($_POST["id"]) && !empty($_POST["mdp"])){
        $temp = protect($_POST["id"]);
        $mdp = protect($_POST["mdp"]);
        $query = "SELECT * FROM admin WHERE identifiant='".$temp."'";
        $result = $GLOBALS["bdd"]->query($query) or trigger_error($GLOBALS["bdd"]->error.$query);
        while ($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $hash = $row["mdp"];
            $id = $row["identifiant"];
        }
        $result->free();
        if(password_verify($mdp, $hash) && strcmp($id,$temp)==0){
            $_SESSION["authentifie"]=true;
            $_SESSION["id"] = $id;
        }
        else{
            unset($_SESSION["authentifie"]);
        }
    }

    if(!empty($_POST["add_id"]) && !empty($_POST["add_mdp"]) && $_SESSION["authentifie"]){
        addAdmin($_POST["add_id"],$_POST["add_mdp"]);
    }

    if(!empty($_POST["deco"]) && $_SESSION["authentifie"]){
        unset($_SESSION["authentifie"]);
    }

    if(!empty($_POST["suppr_admin"]) && $_SESSION["authentifie"]){
        supprAdmin($_POST["suppr_admin"]);
    }

    if(!empty($_POST["suppr_proj"]) &&  $_SESSION["authentifie"]){
        supprProj($_POST["suppr_proj"]);
    }

    if(!empty($_POST["suppr_lot"]) && $_SESSION["authentifie"]){
        supprProj($_POST["suppr_lot"]);
    }

    if(!empty($_POST["modif_mdp"]) && $_SESSION["authentifie"]){
        modifMDP($_POST["modif_id"],$_POST["modif_mdp"]);
    }

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Espace administrateur</title>
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
						<li><a href="cine.php">Coté Ciné de l'ISEN</a></li>
                        <li><a href="emprunt.php">Emprunt de matériel</a></li>
                        <li class="active"><a href="admin.php">Espace Administrateur</a></li>
					</ul>
				</div>
			</div>
		</nav>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">
            
            
            
            
            <?php

            if(!empty($_SESSION["authentifie"])){
                if($_SESSION["authentifie"]){
                    
                    echo('
                    <h2>Se déconnecter</h2>
                        <form method="post" action="admin.php" id="form-register">
                            <input type="hidden" name="deco" id="deco" value="1"/>
                            <input type="submit" class="btn btn-danger" value="Se déconnecter"/>
                        </form>
                    <h1>Gestion des administrateurs</h1>
                    
                        <h3>Modifier votre mot de passe :</h3>
                            <form method="post" action="admin.php" id="form-register">
                                <input type="hidden" name="modif_id" id="modif_id" value="'.$_SESSION["id"].'"></input>
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="modif_mdp">Nouveau mot de passe : </label></span><input type="text" name="modif_mdp" id="modif_mdp" placeholder="azertyU²&io$p" class="form-control" aria-describedby="basic-addon1" required/></div>
                                <input type="submit" class="btn btn-success" value="Modifier votre mot de passe"/>
                            </form>
                            
                        <h3>Ajouter un administrateur</h3>
                            <form method="post" action="admin.php" id="form-register">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_id">Identifiant : </label></span><input name="add_id" id="add_id" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_mdp">Mot de passe : </label></span><input type="text" name="add_mdp" id="add_mdp" placeholder="azertyU²&io$p" class="form-control" aria-describedby="basic-addon1" required/></div>

                                <input type="submit" class="btn btn-info" value="Ajouter un administrateur"/>
                            </form>
                            
                            
                            
                        <h3>Supprimer un administrateur</h3>
                            <p>Attention, cette action est irréversible</p>
                            <form method="post" action="admin.php" id="form-register">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="suppr_admin">Identifiant : </label></span><input name="suppr_admin" id="suppr_admin" type="text" placeholder="Turing" class="form-control" aria-describedby="basic-addon1" required/></div>

                                <input type="submit" class="btn btn-danger" value="Supprimer cet administrateur"/>
                            </form>
                
                    <h1>Gestion des projections</h1>
                    <h3>Ajouter une Projection</h3>
                        <form method="post" action="admin.php" id="form-register">
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_nom">Nom du film : </label></span><input name="projection_nom" id="projection_nom" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_release">Date de release : </label></span><input type="text" name="projection_release" id="projection_release" placeholder="15-01-2015" class="form-control" aria-describedby="basic-addon1"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_date">Date de projection : </label></span><input type="text" name="projection_date" id="projection_date" placeholder="21-01-2015" class="form-control" aria-describedby="basic-addon1" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_description">Description : </label></span><input type="text" name="projection_description" id="projection_description" placeholder="Ce film raconte l\'histoire de ..." class="form-control" aria-describedby="basic-addon1" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_commentaires">Commentaires : </label></span><input type="text" name="projection_commentaires" id="projection_commentaires" placeholder="Ce film est génial et décevant à la fois" class="form-control" aria-describedby="basic-addon1"/></div>

                            <input type="submit" class="btn btn-info" value="Ajouter cette projection"/>
                        </form>
                        
                        <h3>Modifier une projection</h3>
                            <form method="post" action="admin.php" id="form-register">
                                <select name="modif_projection" id="modif_projection">
                                    <option value="value1">Nom du film 1</option> 
                                    <option value="value2" selected>Nom du film 2</option>
                                    <option value="value3">Nom du film 3</option>
                                </select>
                                <input type="submit" class="btn btn-success" value="Modifier cette projection"/>
                            </form>
                        
                        <h3>Supprimer une projection</h3>
                            <p>Attention, cette action est irréversible</p>
                            <form method="post" action="admin.php" id="form-register">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="suppr_proj">Nom du film : </label></span><input name="suppr_proj" id="suppr_proj" type="text" placeholder="Le magicien d\'Oz"" class="form-control" aria-describedby="basic-addon1" required/></div>

                                <input type="submit" class="btn btn-danger" value="Supprimer cette projection"/>
                            </form>
                
                    <h1>Gestion des lots</h1>
                        <h3>Ajouter un lot</h3>
                            <form method="post" action="admin.php" id="form-register">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="id_lot">Identifiant du lot : </label></span><input name="id_lot" id="id_lot" type="text" placeholder="Lettre majuscule (A,B,K,...)" class="form-control" aria-describedby="basic-addon1" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="composition_lot">Composition du lot: </label></span><input type="textarea" name="composition_lot" id="composition_lot" placeholder="Caméra sony avec 3 batteries" class="form-control" aria-describedby="basic-addon1" required/></div>
                                <input type="submit" class="btn btn-info" value="Ajouter ce lot"/>
                            </form>
                            
                        <h3>Modifier un lot</h3>
                            <form method="post" action="admin.php" id="form-register">
                                <select name="modif_lots" id="modif_lots">
                                    <option value="value1">Titre du lot 1</option> 
                                    <option value="value2" selected>Titre du lot 2</option>
                                    <option value="value3">Titre du lot 3</option>
                                </select>
                                <input type="submit" class="btn btn-success" value="Modifier ce lot"/>
                            </form>
                            
                        <h3>Supprimer un lot</h3>
                            <p>Attention, cette action est irréversible</p>
                            <form method="post" action="admin.php" id="form-register">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="suppr_lot">Lettre du lot : </label></span><input name="suppr_lot" id="suppr_lot" type="text" placeholder="K" class="form-control" aria-describedby="basic-addon1" required/></div>

                                <input type="submit" class="btn btn-danger" value="Supprimer ce lot"/>
                            </form>
                ');
                }
            }
            else{
                echo('<h1>Espace d\'administration</h1>
            
            <form method="post" action="admin.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="id">Identifiant : </label></span><input name="id" id="id" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="mdp">Mot de passe : </label></span><input type="text" name="mdp" id="mdp" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1" required/></div>
                
                <input type="submit" class="btn btn-info" value="Se connecter"/>
            </form>');
            }


            ?>
		</div>
	</div>
    
    
</body>
</html>