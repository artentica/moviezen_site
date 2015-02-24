<?php
    session_start();
    include_once("../includes/fonctions.php");
include_once("../includes/function_global.php");

    connect();

        $codesin = $_GET["codedesin"];

        $count = "SELECT COUNT(*) FROM desinscription WHERE desinscription_code='".$codesin."'";


        $result = $GLOBALS["bdd"]->query($count);
        $row = $result->fetch_array(MYSQLI_ASSOC);

        $temp = $row["COUNT(*)"];
        $result->close();


        if($temp != 0){
            $query ="SELECT * FROM desinscription WHERE desinscription_code='".$codesin."'";

            $result=$GLOBALS["bdd"]->query($query);

                    while ($row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $mail = $row["mail"];
                        $projection = $row["projection"];
                    }
                    $result->close();

            $query = "SELECT * from projections WHERE nom='".$projection."'";
            $result=$GLOBALS["bdd"]->query($query);


                    while ($row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $date_release = $row["date_release"];
                        $date_proj = $row["date_projection"];
                        $affiche = $row["affiche"];
                        $affiche_back = $row["back_affiche"];
                    }
                    $result->close();

            $phrase_date =  date("d/m/Y", $date_proj)." à ".date("H\hi", $date_proj);

            $suppr_insc = 0;

            if(supprInscrit($mail,$projection)){
                if(supprdesinc($codesin)){
                    $suppr_insc=1;
                }
            }

        }else $suppr_insc = 2;

?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Désinscription</title>

	<!-- Set Viewport Options -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;"/>
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
                        else{
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Le numéro de désinscription n\'est pas connu, veuillez ne pas modifier l\'URL ! </div>');
                        }




                if($temp != 0) echo '<img src="'.$affiche.'" alt="affiche" class="affiche" style="margin-top:25px">';
                else echo '<img src="../Images/Logo.jpg" alt="affiche" class="affiche" style="margin-top:25px">';

            ?>








		</div>
    </div>
