<?php
    session_start();
    include_once("../includes/fonctions.php");
include_once("../includes/function_global.php");

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
        if(supprInscrit($_SESSION["mail"],$_POST["del_mail"])){
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

	<!-- Set Viewport Options -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />




	<link rel="stylesheet" type="text/css" href="../CSS/index.css">
	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">
   <link rel="stylesheet" href="../CSS/select2.css" type="text/css"/>
      <script type="text/javascript" src="../js/select2.js"></script>
       <?php
        include '../includes/include_on_all_page.php';
    ?>


</head>
<body>
    <div id="banniere"  style="background-image: url('../CSS/new_design/images/header.jpg');
background-size: cover;">
        <h1>
            Moviezen
        </h1>
    </div>

        <?php
       include '../includes/panel-global.php';
      ?>

    <div class="wrapper style1">
		<div class="panel-body">

            <?php
            $nom_actif = "";
            $result = recupProjActive();
            if(!empty($result)){

                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $nom_actif = $row["nom"];
                    $date_projection = $row["date_projection"];
                    setlocale (LC_TIME, 'fr_FR','fra');
                    $date_projection = utf8_encode(strftime("%d %B %Y",strtotime($date_projection)));
                    $description  = $row["description"];
                    $commentaires  = $row["commentaires"];
                    $affiche = $row["affiche"];
                }
                $result->close();
                echo('<h1>'.$nom_actif.'</h1>
                <h3>projeté le '.$date_projection.' au multiplexe Liberté Brest</h3>
                <img src="'.$affiche.'" alt="affiche" class="affiche" style=""/>
                <p>'.$description.'</p>
                <p>'.$commentaires.'</p>
                ');
            }



            if(!$_SESSION["inscrit"] && empty($_SESSION["mail"])){

                echo('<h1 id="inscr">S\'inscrire pour la projection</h1>
            <p>Merci de renseigner tout les champs</p>
			<form method="post" action="cine.php#inscr" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="nom">Nom : </label></span><input name="nom" id="nom" type="text" placeholder="Nom" class="form-control"  required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="prenom">Prénom : </label></span><input type="text" name="prenom" id="prenom" placeholder="Prénom" class="form-control"  required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="classe">Classe : </label></span><input type="text" name="classe" id="classe" placeholder="Prénom" class="form-control"  required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="mail">@ ISEN : </label></span><input type="email" name="mail" id="mail" placeholder="Essai.tarte@orange.fr" class="form-control"  required/></div>

                <div class="input-group max"><span class="input-group-addon form-label start_span projection"><label for="select_projection">Projection : </label></span><span><select name="select_projection" id="select_projection"><option></option>
                    ');

                $result = recupProjDesc();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $nom = $row["nom"];
                    $date = $row["date_projection"];
                    $date = date("d/m/Y", strtotime($date));
                    echo('<option value="'.$nom.'" ');
                    if(strcmp($nom_actif,$nom)==0){
                        echo('selected="selected"');
                    }
                    echo('>'.$nom.' projeté le '.$date.'</option>');
                }
                $result->close();
                echo('
                </select></div>
                <input type="submit" class="button dark_grey inscrval" id="save_cine" value="S\'inscrire pour le film"/>
            </form>

            ');

            }

            else{
                echo('<h1 id="inscr">Se désinscrire de la projection</h1>
            <form method="post" action="cine.php" id="form-register">
                <input type="hidden" name="del_mail" id="del_mail" value="'.$_SESSION["mail"].'"/>
                <input type="submit" class="button dark_grey" id="save_cine" value="Se désinscrire"/>
            </form>');

            }

            ?>


		</div>
	</div>

    <script>
        $(document).ready(function() {
  $("#select_projection").select2({
          placeholder: "Sélectionnez une séance",
          allowClear: true,
          width:"100%"
        });
    });
    </script>
</body>
</html>
