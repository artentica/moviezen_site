<?php
    session_start();
    include_once("../includes/fonctions.php");
include_once("../includes/function_global.php");
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
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />

	<link rel="stylesheet" type="text/css" href="../CSS/index.css">
    <link rel="stylesheet" type="text/css" href="../CSS/menu.css">
	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">
	 <?php
        include '../includes/include_on_all_page.php';
    ?>
    <script src="../js/bootstrap.js"></script>

</head>
<body>
   <div id="banniere"  style="background-image: url('../CSS/new_design/images/header.jpg');
background-size: cover;">
        <h1>
            Moviezen
        </h1>
    </div>
    <header>
        <?php
       include '../includes/panel-global.php';?>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">
            <form method="post" action="projection.php" id="form-register">
               <fieldset>
    <legend>Récupérer les inscrits à une projection :</legend>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="recup_proj">Projection : </label></span><select name="recup_proj" id="recup_proj">
                <?php 

                
                $result = recupProjDesc();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $nom = $row["nom"];
                    $date = $row["date_projection"];
                    echo('<option value="'.$nom.'">'.$nom.' projeté le '.$date.'</option>');
                }
                $result->close();?>
                    </select>                   </div>

                 <input type="submit" class="button dark_grey" onClick="$(this).button('loading')" data-loading-text="Loading" value="Récupérer les inscrits"/>

                </fieldset>
            </form>



            <?php
            if(!empty($_POST["recup_proj"])){
                if(recupInscrit($_POST["recup_proj"])){
                    echo('<a class="button dark_grey" href="inscrits.xls"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>  Télécharger le fichier xls</a>');
                }
            
            }
                



            ?>
        </div>
	</div>
    
    
</body>
</html>
