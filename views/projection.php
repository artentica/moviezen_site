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

	<!-- Set Viewport Options -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />

	<link rel="stylesheet" type="text/css" href="../CSS/index.css">
    <link rel="stylesheet" type="text/css" href="../CSS/menu.css">
	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">
	 <?php
        include '../includes/include_on_all_page.php';
    ?>
	<script src="../js/jquery-2.1.3.min.js"></script>
	<script src="../js/menu.js"></script>

</head>
<body>
   <div id="banniere">
        <h1>
            Moviezen
        </h1>
    </div>
    <header>
        <?php
       include '../includes/panel-global.php';
        include '../includes/menu-mobile.php';?>
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
                    echo('<br/><a class="btn btn-success" href="inscrits.xls">Télécharger le fichier xls</a>');
                }
            
            }
                



            ?>
        </div>
	</div>
    
    
</body>
</html>
