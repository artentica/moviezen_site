<?php
    session_start();
    include_once("../includes/fonctions.php");
    connect();
    
    if(!$_SESSION["authentifie"]){
         header('Location: ../index.php'); 
    }

    
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Espace Liste de projection</title>
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
                        <li><a href="admin.php">Espace Administrateur</a></li>
                        <li><a href="calendrier.php">Calendrier des emprunts</a></li>
                        <li class="active"><a href="projection.php">Espace liste de projection</a></li>
					</ul>
				</div>
			</div>
		</nav>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">
            <h3>Récupérer les inscrits à une projection :</h3>
            <form method="post" action="projection.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="recup_proj">Projection : </label><select name="recup_proj" id="recup_proj">
                <?php 

                
                $result = recupProj();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $nom = $row["nom"];
                    $date = $row["date_projection"];
                    echo('<option value="'.$nom.'">'.$nom.' projeté le '.$date.'</option>');
                }
                $result->close();?>
                    </select>
                 <input type="submit" class="btn btn-success" value="Récupérer les inscrits"/>
            </form>
                    
            <?php
            if(!empty($_POST["recup_proj"])){
                if(recupInscrit($_POST["recup_proj"])){
                    echo('<a href="inscrits.xls">Lien vers le fichier xls</a>');
                }
            
            }
                



            ?>
        </div>
	</div>
    
    
</body>
</html>