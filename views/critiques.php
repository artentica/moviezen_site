<?php
    session_start();
    include_once("../includes/fonctions.php");
    include_once("../includes/function_global.php");

    connect();

    //Protection contre faille XSS et attaques HTML-JS
    //Pour tableau POST (et GET au cas où)
    //On parcourt la totalité du tableau POST et GET et pour chaque variable, on enlève les éléments "génants"
        foreach( $_POST as $cle=>$value )
        {
            if(is_array($_POST[$cle])) {
                foreach($_POST[$cle] as $cle2 =>$value2){
                    $_POST[$cle2] = strip_tags(htmlentities($value2, ENT_QUOTES, 'UTF-8'));
                }
            }
            else{
                $_POST[$cle] = strip_tags(htmlentities($value, ENT_QUOTES, 'UTF-8'));
            }

        }

        foreach( $_GET as $cle=>$value )
        {
            if(is_array($_GET[$cle])) {
                foreach($_GET[$cle] as $cle2 =>$value2){
                    $_GET[$cle2] = strip_tags(htmlentities($value2, ENT_QUOTES, 'UTF-8'));
                }
            }
            else{
                $_GET[$cle] = strip_tags(htmlentities($value, ENT_QUOTES, 'UTF-8'));
            }

        }
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Critiques de l'ISEN</title>

	<!-- Set Viewport Options -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>



	<link rel="stylesheet" type="text/css" href="../CSS/index.css">
	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">

       <?php
        include '../includes/include_on_all_page.php';
    ?>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/transit.js"></script>

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

    <form method="post" action="critiques.php#inscr" class="form-register" style="margin-top:10px;">
        <fieldset>
                <legend id="inscr">S\'inscrire effectuer des critiques</legend>
                <p>Merci de renseigner tous les champs. Votre mail ISEN est <b>obligatoire</b> pour la vérification.</p>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="nom">Nom : </label></span><input name="nom" id="nom" type="text" placeholder="Nom" class="form-control"  required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="prenom">Prénom : </label></span><input type="text" name="prenom" id="prenom" placeholder="Prénom" class="form-control"  required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="classe">Classe : </label></span><select name="classe" id="classe">
                <?php
                $result = recupPromo();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $promo = $row["promotion"];
                    echo('<option value="'.$promo.'">'.$promo.'</option>');
                }
                $result->close();
                  echo('

                    </select>
                </div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="mail">@ ISEN : </label></span><input type="email" name="mail" id="mail" placeholder="moviezen.brest@isen-bretagne.fr" class="form-control" required/></div>
                <input type="submit" class="button dark_grey inscrval" id="save_cine" value="S\'inscrire pour les critiques"/>
            </fieldset></form>');

            // PAS FINI, RESTE A PENSER UN PEU PLUS AU DEROULEMENT DES OPERATIONS
            echo ('
            <form method="post" action="cine.php#critiques"  class="form-register">
            <fieldset>
                <legend id="critiques">Poster une critique</legend>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="titre">Prénom : </label></span><input type="textarea" name="titre" id="titre" placeholder="Skyfall" class="form-control"  required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="critique">Prénom : </label></span><input type="textarea" name="critique" id="critique" placeholder="Il était une fois au pays imaginaire..." class="form-control"  required/></div>
                <input type="submit" class="button dark_grey inscrval" value="Poster cette critique"/>
                </fieldset></form>');

           ?>


		</div>
	</div>


</body>
</html>
