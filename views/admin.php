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
            $_SESSION["authentifie"]=1;
        }
        else{
            $_SESSION["authentifie"]=0;
        }
    }

     if(!empty($_POST["add_id"]) && !empty($_POST["add_mdp"])){
        addAdmin($_POST["add_id"],$_POST["add_mdp"]);
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
            <h1>Espace d'administration</h1>
            
            <form method="post" action="admin.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="id">Identifiant : </label></span><input name="id" id="id" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1"/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="mdp">Mot de passe : </label></span><input type="text" name="mdp" id="mdp" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1"/></div>
                
                <input type="submit" class="btn btn-info" value="Se connecter"/>
            </form>
            
            
            
            <?php

            if(!empty($_SESSION["authentifie"])){
                if($_SESSION["authentifie"]){
                    
                    echo('
                    
                    <h1>Gestion des administrateurs</h1>
                    <h3>Ajouter un administrateur</h3>
                    <form method="post" action="admin.php" id="form-register">
                    <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_id">Identifiant : </label></span><input name="add_id" id="add_id" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1"/></div>
                    <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_mdp">Mot de passe : </label></span><input type="text" name="add_mdp" id="add_mdp" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1"/></div>

                    <input type="submit" class="btn btn-info" value="Ajouter un administrateur"/>
                </form>
                
                <h1>Gestion des projections</h1>
                <h3>Ajouter une Projection</h3>
                <form method="post" action="admin.php" id="form-register">
                    <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_nom">Nom du film : </label></span><input name="projection_nom" id="projection_nom" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1"/></div>
                    <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_release">Date de release : </label></span><input type="text" name="projection_release" id="projection_release" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1"/></div>
                    <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_date">Date de projection : </label></span><input type="text" name="projection_date" id="projection_date" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1"/></div>
                    <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_description">Description : </label></span><input type="text" name="projection_description" id="projection_description" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1"/></div>
                    <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_commentaires">Commentaires : </label></span><input type="text" name="projection_commentaires" id="projection_commentaires" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1"/></div>

                    <input type="submit" class="btn btn-success" value="Ajouter un administrateur"/>
                </form>
                
                <h1>Gestion des lots</h1>
                <h3>Ajouter un lot</h3>
                <form method="post" action="admin.php" id="form-register">
                    <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="id_lot">Identifiant du lot : </label></span><input name="id_lot" id="id_lot" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1"/></div>
                    <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="composition_lot">Composition du lot: </label></span><input type="text" name="composition_lot" id="composition_lot" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1"/></div>
                </form>
                ');
                }
            }


            ?>
		</div>
	</div>
    
    
</body>
</html>