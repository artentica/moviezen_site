<?php
    session_start();
    include_once("../includes/fonctions.php");
    connect();

    

    if(!empty($_POST["id"]) && !empty($_POST["mdp"])){
        recupID($_POST["id"]);
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
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="nom">Identifiant : </label></span><input name="id" id="id" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1"/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="prenom">Mot de passe : </label></span><input type="text" name="mdp" id="mdp" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1"/></div>
                
                <input type="submit" class="btn btn-info" value="Se connecter"/>
            </form>
            
            
		</div>
	</div>
    
    
</body>
</html>