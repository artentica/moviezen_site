<?php
    session_start();
    include_once("../includes/fonctions.php");
    include_once("../includes/function_global.php");
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

    connect();
    $temp = 0;
    $suppr_insc = 10;
    if(!empty($_GET["codedesin"])){
        $query = $GLOBALS["bdd"]->prepare("SELECT COUNT(*) as total FROM desinscription WHERE desinscription_code=?");

        $query->bind_param("s",$_GET["codedesin"]);
        $query->execute();
        $query->store_result();
        $query->bind_result($temp);
        $query->fetch();
        $query->close();

        if($temp != 0){
            $tab = array();
            $query = $GLOBALS["bdd"]->prepare("SELECT mail, projection FROM desinscription WHERE desinscription_code=?");
            $query->bind_param("s",$_GET["codedesin"]);
            $query->execute();
            $query->store_result();
            $query->bind_result($mail,$projection);
            $query->fetch();
            $query->close();


            $query = $GLOBALS["bdd"]->prepare("SELECT date_release, date_projection, affiche, back_affiche FROM projections WHERE nom=?");
            $query->bind_param("s",$projection);
            $query->execute();
            $query->store_result();
            $query->bind_result($date_release,$date_proj,$affiche,$affiche_back);
            $query->fetch();
            $query->close();
            $phrase_date =  date("d/m/Y", $date_proj)." à ".date("H\hi", $date_proj);

            $suppr_insc = 0;

            if(supprInscrit($mail,$projection)){
                if(supprdesinc($codesin)){
                    $suppr_insc=1;
                }
            }

        }else $suppr_insc = 2;
    }

?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Désinscription</title>

	<!-- Set Viewport Options -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />



	<link rel="stylesheet" type="text/css" href="../CSS/index.css">
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

        <?php
       include '../includes/panel-global.php';

        if($temp != 0) echo '<div class="wrapper style1" style="background-image: url(\''.$affiche_back.'\');background-size: cover;height: inherit;">';
        /*else echo '<div class="wrapper style1" style="background-image: url(\'../Images/Logo.png\');background-size: cover;height: inherit;">';*/
      ?>


		<div class="panel-body">

            <h1>Désinscription</h1>
            <?php
                if($temp != 0) echo "<h3>De la séance '".$projection."' projeté le ".$phrase_date." au Multiplexe Liberté Brest</h3>";




                        if($suppr_insc == 1){
                            echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Vous ('.$mail.') avez correctemment été désincrit(e) pour le film "'.$projection.'" du '.$phrase_date.' !</div>');
                        }
                        elseif($suppr_insc == 0){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Vous ('.$mail.') n\'avez pas été désincrit(e) pour le film "'.$projection.'" du '.$phrase_date.' ! Une erreur est survenue veuillez réessayer ultérieurement ! </div>');
                        }
                        elseif($suppr_insc == 10){
                            echo("<div>Nothing to see here !</div>");
                        }
                        else{
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Le numéro de désinscription n\'est pas connu, veuillez ne pas modifier l\'URL ! </div>');
                        }




                if($temp != 0) echo '<img src="'.$affiche.'" alt="affiche" class="affiche" style="margin-top:25px">';
                else echo '<img src="../Images/Logo.jpg" alt="affiche" class="affiche" style="margin-top:25px">';

            ?>








		</div>
    </div>
