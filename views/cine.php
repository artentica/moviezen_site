<?php
    session_start();
    include_once("../includes/fonctions.php");
    include_once("../includes/function_global.php");

    connect();

    $nom_actif = "";
    $result = recupProjActive();
    if($result->num_rows){
        while ($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $nom_actif = $row["nom"];
            $date_projection = $row["date_projection"];
            $date_projection = date("d/m/Y", $date_projection)." à ".date("H\hi", $date_projection);
            $description  = $row["description"];
            $commentaires  = $row["commentaires"];
            $affiche = $row["affiche"];
            $affiche_back = $row["back_affiche"];
		    $langue= $row["langue"];
		    $prix = $row["prix"];
		    $bande_annonce = $row["bande_annonce"];
            $fin_annee = $row["fin_annee"];
        }
        $description  = replace_chara($description);
        $commentaires  = replace_chara($commentaires);
        $nom_actif  = replace_chara($nom_actif);
        $result->close();

    }
        //VAR

        $inscrit = 0;






        $_SESSION["inscrit"]=0;

        //PROTECTION CONTRE XSS

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

        $tab = array();
        $result = recupProj();
        while ($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $tab[] = $row["nom"];
        }
        $result->close();

        if(!empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["classe"]) && !empty($_POST["mail"])){

            if(in_array($_POST["select_projection"],$tab)){
                $temp = ajoutInscrit($_POST["nom"],$_POST["prenom"],$_POST["mail"],$_POST["classe"],$_POST["select_projection"]);
                if($temp == 2) $inscrit = 2;
                elseif($temp == 1){
                    $_SESSION["select_projection"]=$_POST["select_projection"];
                    $_SESSION["inscrit"]=1;
                    $mail = protect($_POST["mail"]);
                    $_SESSION["mail"]=$mail;
                    $inscrit = 1;
                }
            }
            else{
                $inscrit = 3;
            }
        }


        $mailsend = 0;

        if(!empty($_POST["del_mail"])){
           /* if(supprInscrit($_SESSION["mail"],$_POST["del_mail"])){
                $_SESSION["inscrit"]=0;
                unset($_SESSION["mail"]);
            }*/
            $repmail = send_mail($nom_actif,$date_projection,$_POST["del_mail"]);

            if($repmail == 1)$mailsend = 1;
            elseif($repmail == 2)$mailsend = 2;
            elseif($repmail == 4)$mailsend = 4;

            else $mailsend = 3;
        }




    //sending mail

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Du côté du CINE de l'ISEN</title>

	<!-- Set Viewport Options -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />



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
                if(isset($affiche_back)){
                    echo '<div class="wrapper style1" style="background-image: url(\''.$affiche_back.'\');
                                       background-size: cover;">';
                }
      ?>




      <div class="panel-body">

         <div class="courts clearfix">


               <h2>Test</h2>

         </div>
         <div class="desc_video collapse clearfix">

                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                  <div class="img_film  clearfix" style="background-image: url('../Images/Brotherhood.jpg');"></div>
                  <div style="clear:both;"></div>
            </div>
      </div>
      <div class="panel-body">
         <div class="courts" data-collapse="0" style="background-image:url('../Images/url.jpg');">
               <h2>Baoualag ar brezel</h2>
               <span class="desc_video">
                  <p>dsqdqsdqsdqcqscqcqscqcsq</p>
            </span>
         </div>
      </div>
            <?php
        //Section COURT-METRAGES

        if(isset($fin_annee)){
            if($fin_annee){
                $result = recupCourts();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $titre_court = $row["titre"];
                    $description_court = $row["description"];
                    $affiche_court = $row["affiche"];
                    echo("<div class=\"panel-body\">
                            <div class=\"courts clearfix\">
                                <h2>".$titre_court."</h2>
                            </div>
                            <div class=\"desc_video collapse clearfix\">
                                <p>".$description_court."</p>
                                <div class=\"img_film  clearfix\" style=\"background-image: url('".$affiche_court."');\"></div>
                                <div style=\"clear:both;\"></div>
                            </div>
                        </div>");
                }
                $result->close();
            }
        }

?>

    <script>
    $( ".courts" ).click(function() {
        if($(this).attr("data-collapse")=="1"){
              $(this).children("h2").transition({ y: '95px' },250 );
              $(this).attr("data-collapse","0");
            $( this ).animate({
    height: "200px",
  }, 200);
        }else{
             $(this).children("h2").transition({ y: '40px'},250 );
             $(this).attr("data-collapse","1");
            $( this ).animate({
    height: "100px",
  }, 200);
        }
        $( this ).parent().children(".desc_video").collapse('toggle');

});
</script>

    <?php
        //Fin de la section des courts-métrages
        echo('<div class="panel-body">');


                if(isset($description)){
                   echo('<h1>'.$nom_actif.' ('.$langue.')</h1>
                <h3>projeté le '.$date_projection.' au multiplexe Liberté Brest<br><br>Prix : '.$prix.'&euro;</h3>
                <img src="'.$affiche.'" alt="affiche" class="affiche" style=""/>
                <p class="description">'.$description.'</p>
                <p class="description">'.$commentaires.'</p>
                ');

		echo ('<!-- 16:9 aspect ratio --><div class="my_embed">
<div class="embed-responsive embed-responsive-16by9">
  <iframe class="embed-responsive-item" src="'.$bande_annonce.'" frameborder="0" allowfullscreen></iframe>
</div></div>
');
            /*if(!$_SESSION["inscrit"] && empty($_SESSION["mail"])){*/

                echo('<div class="panel panel-default">
		<div class="panel-body">


			<form method="post" action="cine.php#inscr" class="form-register" style="margin-top: 10px;">
            <fieldset>
                <legend id="inscr">S\'inscrire pour la projection</legend>
                <p>Merci de renseigner tous les champs</p>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="nom">Nom : </label></span><input name="nom" id="nom" type="text" placeholder="Nom" class="form-control"  required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="prenom">Prénom : </label></span><input type="text" name="prenom" id="prenom" placeholder="Prénom" class="form-control"  required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="classe">Classe : </label></span><select name="classe" id="classe">');


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

                <div class="input-group max"><span class="input-group-addon form-label start_span projection"><label for="select_projection">Projection : </label></span><input type="text"  name="select_projection" class="form-control" value="'.$nom_actif.'" readonly/>

                </div>
                <input type="submit" class="button dark_grey inscrval" id="save_cine" value="S\'inscrire pour le film"/>
            </fieldset></form>');

            if($inscrit!=0){
                        if($inscrit == 2){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Le mail '.$_POST["mail"].' est déjà incrit pour le film "'.$_POST["select_projection"].'" du '.$date_projection.' !</div>');
                        }
                        elseif($inscrit){
                            echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Mr/Mme '.$_POST["nom"].' '.$_POST["prenom"].' ('.$_POST["mail"].') de la classe '.$_POST["classe"].' avez bien été incrit pour le film "'.$_POST["select_projection"].'" du '.$date_projection.' !</div>');
                        }
                        elseif($inscrit == 3){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Merci de ne pas vous amuser à changer la valeur du select !</div>');
                        }
                        else{
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Une erreur est survenue vous n\'avez pas pu être inscrit !</div>');
                        }
                    }




            echo ('
            <form method="post" action="cine.php#desinscr"  class="form-register">
            <fieldset>
                <legend id="desinscr">Se désinscrire pour la projection</legend>
                 <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="del_email">@ ISEN : </label></span><input type="email" name="del_mail" class="form-control" placeholder="prenom.nom@isen.fr" required/></div>
                <input type="submit" class="button dark_grey inscrval" value="Se désinscrire de '.$nom_actif.'"/>
                </fieldset></form>');

            if($mailsend == 1){
                            echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Un mail de désinscription vous a été envoyé. Veuillez suivre le lien reçu pour confirmer la désinscription. <br> Le mail provient de "moviezen Brest", et a été envoyé à '.$_POST["del_mail"].' !</div>');
                        }
                        elseif($mailsend == 2){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Le mail '.$_POST["del_mail"].' n\'est pas inscrit pour la séance: "'.$nom_actif.'."</div>');
                        }
                        elseif($mailsend == 3){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Un problème est survenu et le mail n\a pas pu être envoyé ! <br> Réessayez plus tard.</div>');
                        }
                        elseif($mailsend == 4){

                            $verif = $GLOBALS["bdd"]->prepare("SELECT last_send FROM desinscription WHERE mail=? AND projection=?");
                            $query2->bind_param("ss",$_POST["del_mail"],$nom_actif);
                            $query2->execute();
                            $query2->store_result();
                            $query2->bind_result($temp);
                            $query2->fetch();
                            $query2->close();

                            $date = date_create();
                            $date=date_timestamp_get($date);
                            $time = $date - $temp;

                            $min=floor($time/60);
                            $sec= $time%60;




                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Un mail a déjà été envoyé il y a '.$min.' min et '.$sec.' s, veuillez attendre 5min entre chaque essai. <br> Vérifiez vos spams.</div>');
                        }
                    }
                else{
                    echo('Aucune projection n\'a été activée dans le Ciné de l\'Isen !');
                }
            echo('
            </div>

            </div>

            ');
           /* }

            else{
                echo('<h1 id="inscr">Se désinscrire de la projection</h1>
            <form method="post" action="cine.php" class="form-register">

                <input type="hidden" name="del_mail" id="del_mail" value="'.$_SESSION["mail"].'"/>
                <input type="submit" class="button dark_grey" id="save_cine" value="Se désinscrire"/>
            </form>');

            }*/

            ?>


		</div>
	</div>


</body>
</html>
