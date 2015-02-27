<?php
    session_start();
    include_once("../includes/fonctions.php");
include_once("../includes/function_global.php");
    connect();

    foreach( $_POST as $cle=>$value )
        {
            $_POST[$cle] = strip_tags(htmlentities($value, ENT_QUOTES, 'UTF-8'));
        }
    
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
            <form method="post" action="projection.php" class="form-register">
               <fieldset>
    <legend id="tableau">Récupérer les inscrits à une projection :</legend>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="recup_proj">Projection : </label></span><select name="recup_proj" id="recup_proj">
                <?php 

                
                $result = recupProjDesc();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $nom = $row["nom"];
                    $date = $row["date_projection"];
                    $date = date("d/m/Y", $date)." à ".date("H\hi", $date);
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
                    $replace = array('\"',"\'","'",'"'," ");
                    $_POST["recup_proj"] = str_replace($replace,'_',$_POST["recup_proj"]);
                    echo('<a class="button dark_grey" href="../xls/inscrits_'.$_POST["recup_proj"].'.xls"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>  Télécharger le fichier "inscrits_'.$_POST["recup_proj"].'.xls"</a>');
                }
            
            }
                



            ?>
        </div>
	</div>
    
    
</body>
</html>
