<?php
    session_start();
    include_once("../includes/fonctions.php");
    include_once("../includes/function_global.php");
    connect();

    if(empty($_SESSION["emprunteur"])){
        $_SESSION["emprunteur"]=0;
    }

    if(!empty($_POST["del_mail"])){
        if(supprEmprunt($_POST["del_mail"],$_POST["del_date"])){
            $_SESSION["emprunteur"]=0;
            unset($_SESSION["mail"]);
        }
    }

    if(!empty($_POST["conn_mail"])){
        if(!empty(dejaInscrit($_POST["conn_mail"]))){
            $_SESSION["emprunteur"]=1;
            $_SESSION["mail"]=protect($_POST["conn_mail"]);
        }
    }

    if(!empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["mail"]) && !empty($_POST["tel"]) && !empty($_POST["classe"]) && !empty($_POST["lots"]) && !empty($_POST["date_emprunt"]) && !empty($_POST["date_retour"]) && $_POST["accepter"])
    {
                    if(ajoutEmprunt2($_POST["nom"],$_POST["prenom"],$_POST["tel"],$_POST["mail"], $_POST["classe"],$_POST["lots"],$_POST["date_emprunt"],$_POST["date_retour"])){
                        $_SESSION["mail"]=protect($_POST["mail"]);
                        $_SESSION["date_emprunt"]=date("Y-m-d H:m:s", strtotime(protect($_POST["date_emprunt"])));
                        $_SESSION["emprunteur"]=1;
                    }

    }

    if(!empty($_POST["conn_mail"])){
        if(!empty(recupEmpruntAjd($_POST["conn_mail"]))){
            $_SESSION["emprunteur"]=1;
            $_SESSION["mail"]=protect($_POST["conn_mail"]);
        }
    }

    if(!empty($_POST["decon"])){
       $_SESSION["emprunteur"]=0;
        unset($_SESSION["mail"]);
    }

    if(!empty($_POST["annul_lots"])){
        supprEmprunt($_POST["annul_mail"],$_POST["annul_lots"]);
    }

    if(!empty($_POST["modification_lots"]) && !empty($_POST["new_date_emprunt"]) && !empty($_POST["new_date_retour"])){
        $date = explode('/',$_POST["modif_old_date"]);
        $date_emprunt = $date[0];
        $date_retour = $date[1];
        modifEmprunt($_POST["modification_lots"],$_POST["modif_old_lots"],$date_emprunt,$date_retour,$_POST["modif_old_mail"],$_POST["new_date_emprunt"],$_POST["new_date_retour"]);
    }


?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Emprunter du matériel</title>

	<!-- Set Viewport Options -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />

	<link rel="stylesheet" type="text/css" href="../CSS/index.css">
<!--
    <link rel="stylesheet" href="../CSS/bootstrap-multiselect.css" type="text/css"/>
-->
    <link rel="stylesheet" href="../CSS/select2.css" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../CSS/jquery.datetimepicker.css"/ >
    <?php
        include '../includes/include_on_all_page.php';
    ?>
    <script src="../js/jquery.datetimepicker.js"></script>
    <script src="../js/bootstrap.min.js"></script>

<!--
    <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
-->
    <script type="text/javascript" src="../js/select2.js"></script>


    <!-- Initialize the multiselect: -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#lots').select2({
                width:"100%",
                 placeholder: "Choisissez le matériel"
            });
            $('#modification_lots').select2({
                width:"100%",
                 placeholder: "Choisissez le matériel"
            });
        });
    </script>
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
            <!--





            ESPACE POUR LE CALENDRIER







            -->







            <?php

            echo('<table class="table table-striped table-bordered"><thead><th>Image du lot</th><th>Composition du lot</th><th>Disponible</th><th style="display:none">Identifiant de lot</th><th>Indisponible jusqu\'au</th><th>Caution du lot</th></thead>');
            $result = recupLot();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $id = $row["id"];
                    $composition = $row["composition"];
                    $caution = $row["caution"];
                    $indisponible = "/";
                    $disponible = '0';


                    $date_ajd = date("z");
                    $query = "SELECT ".$id." FROM dispo WHERE jour=".($date_ajd+1);
                    $result_dispo = $GLOBALS["bdd"]->query($query);
                    while ($row_dispo = $result_dispo->fetch_array(MYSQLI_ASSOC))
                    {
                        $disponible = $row_dispo[$id];
                    }



                    if($disponible){
                        $disponible='<button type="button" class="button dark_grey button-large">
  <span class="glyphicon glyphicon-ok" style="color:green"></span></button>';
                        $class="success";
                    }else{
                        $disponible='<button type="button" class="button dark_grey button-large">
  <span class="glyphicon glyphicon-remove" style="color:red"></span></button>';
                        $query=" SELECT * FROM inscrits_lots WHERE lots='".$id."' ORDER BY `date_retour` DESC LIMIT 1";
                        $result2 = $GLOBALS["bdd"]->query($query);
                        while ($row2 = $result2->fetch_array(MYSQLI_ASSOC)){
                            setlocale (LC_TIME, 'fr_FR','fra');
                            $indisponible = $row2["date_retour"];
                            $indisponible = utf8_encode(strftime("%d %B %Y, %H:%M",strtotime($indisponible)));
                        }
                        $class="danger";
                    }
                    $image = $row["image"];
                    echo('<tr><td><img src="'.$image.'" alt="image" style=""/><td>'.$composition.'</td></td><td >'.$disponible.'</td><td style="display:none">'.$id.'</td><td>'.$indisponible.'</td><td>'.$caution.'&euro;</td>');
                    echo('</tr>');
                }
                $result->close();

            echo('</table>');

            if(!$_SESSION["emprunteur"] && empty($_SESSION["mail"])){

                echo('<h1>Emprunter du matériel</h1>
            <p>Merci de renseigner tout les champs</p>
            <form method="post" action="emprunt.php" id="form-register" style="margin-bottom:35px">
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="nom">Nom : </label></span><input name="nom" id="nom" type="text" placeholder="Nom" class="form-control" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="prenom">Prénom : </label></span><input type="text" name="prenom" id="prenom" placeholder="Prénom" class="form-control" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="mail">@ ISEN : </label></span><input type="email" name="mail" id="mail" placeholder="prenom.nom@isen.fr" class="form-control" required pattern="[a-z0-9._%+-]+@(isen(?:-bretagne)\.fr)$"/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="tel">Tel. : </label></span><input type="tel" name="tel" id="tel" placeholder="0612345678" class="form-control" pattern="^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="classe">Classe : </label></span><input type="text" name="classe" id="classe" placeholder="CIR3" class="form-control" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span projection"><label for="lots">Lots : </label></span><select name="lots[]" id="lots" multiple="multiple">
                ');
                //<div class="input-group max center"><span class="input-group-addon form-label"><label for="lots">Lots : </label></span><input type="text" name="lots" id="lots" placeholder="A,K,L,C,...." class="form-control"/></div>

                $result = recupLot();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $id = $row["id"];
                    $composition = $row["composition"];
                    echo('<option value="'.$id.'">'.$id.' composé de '.$composition.'</option>');
                }
                $result->close();

                echo('</select></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="date_emprunt">Date d\'emprunt : </label></span><input name="date_emprunt" id="date_emprunt" placeholder="Date d\'emprunt" class="form-control"  required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="date_retour">Date de retour : </label></span><input name="date_retour" id="date_retour" placeholder="Date de retour" class="form-control datepicker" required/></div>

                <label class="checkbox"><input type="checkbox" name="accepter" required value="1"> <b>Je reconnais avoir pris connaissance des conditions d\'utilisation de l\'emprunt de matériel Moviezen et jure sur l\'honneur de m\'y tenir, sans quoi Satan viendra moisonner mon âme</b></label>
                <input type="submit" class="button dark_grey" value="S\'inscrire"/>
            </form>');

                echo('

            <h2>Rappel des règles d\'emprunt concernant le matériel Moviezen</h2>
            <p>Une fois l\'inscription effectuée, le matériel vous est réservé durant la période demandée. Il est évident que cela vous engage à respecter les délais spécifiés. Les délais trop longs tels que des emprunts de plus de 3 mois par exemple ne sont pas autorisés. Un chèque de caution doit être émis à l\'ordre de Moviezen avec le montant total des lots empruntés. Ce chèque de caution est évidemment conservé en guise de garantie et ne sera pas touché si le matériel est rendu dans le même état que lors de l\'emprunt.</p>


            <h3>Vous avez déja emprunté du matériel ? Connectez vous</h3>
            <form method="post" action="emprunt.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label start_span"
                ><label for="conn_mail">@ ISEN : </label></span><input type="email" name="conn_mail" id="conn_mail" placeholder="prenom.nom@isen.fr" class="form-control"
                /></div>

                <input type="submit" style="margin-top:20px" class="button dark_grey" value="Se connecter"/>
            </form>

            ');


            }
            else{
                echo('
                <legend id="modifie_emprunt">Modifier un emprunt</legend>
                <form method="post" action="emprunt.php#modifie_emprunt" id="form-register">
                <input type="hidden" name="modif_mail" id="modif_mail" value="'.$_SESSION["mail"].'" required/>
                <div class="input-group max center"><span class="input-group-addon form-label start_span projection"><label for="modif_lots">Lots : </label></span><select name="modif_lots" id="modif_lots">
                ');

                $result = recupEmpruntAjd($_SESSION["mail"]);
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $date_emprunt = $row["date_emprunt"];
                    $date_retour = $row["date_retour"];
                    setlocale (LC_TIME, 'fr_FR','fra');
                    $new_date_emprunt = utf8_encode(strftime("%d %b %Y",strtotime($date_emprunt)));
                    echo('<option value="'.$date_emprunt.'/'.$date_retour.'">Emprunt du '.$new_date_emprunt.'</option>');
                }
                $result->close();
                echo('</select></div>
                    <input type="submit" class="button dark_grey" value="Modifier cet emprunt"/>
                </form>
                ');
                if(!empty($_POST["modif_lots"]) && !empty($_POST["modif_mail"])){
                    $date = explode('/', $_POST["modif_lots"]);
                    echo('<form method="post" action="emprunt.php#modifie_emprunt" id="form-register" style="margin-bottom:35px">
                <div class="input-group max center"><span class="input-group-addon form-label start_span projection"><label for="modification_lots">Lots : </label></span><select name="modification_lots[]" id="modification_lots" multiple="multiple">');

                $result = recupLot();
                $result2 = recupEmpruntDate($_SESSION["mail"],$_POST["modif_lots"]);
                $anciens_lots = "";
                while ($row = $result2->fetch_array(MYSQLI_ASSOC))
                {
                    $utilise[] = $row["lots"];
                }
                $result2->close();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $id = $row["id"];
                    $composition = $row["composition"];
                    $anciens_lots .= '/'.$id;
                    if(in_array($id,$utilise)){
                        echo('<option value="'.$id.'" selected="selected">'.$id.' composé de '.$composition.'</option>');
                    }
                    else{
                        echo('<option value="'.$id.'">'.$id.' composé de '.$composition.'</option>');
                    }
                }
                $result->close();





                    echo('
                </select></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="new_date_emprunt">Date d\'emprunt : </label></span><input name="new_date_emprunt" id="new_date_emprunt" placeholder="Nouvelle date d\'emprunt" class="form-control"  value="'.$date[0].'" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="new_date_retour">Date de retour : </label></span><input name="new_date_retour" id="new_date_retour" placeholder="Nouvelle date de retour" class="form-control datepicker"  value="'.$date[1].'" required/></div>
                <input type="hidden" name="modif_old_date" id="modif_old_date" value="'.$_POST["modif_lots"].'" required/>
                <input type="hidden" name="modif_old_mail" id="modif_old_mail" value="'.$_POST["modif_mail"].'" required/>
                <input type="hidden" name="modif_old_lots" id="modif_old_lots" value="'.$anciens_lots.'" required/>
                <input type="submit" class="button dark_grey" value="Modifier cet emprunt"/>
            </form>
                ');
                }


                echo('
                <legend id="annulation_emprunt">Annuler un emprunt</legend>
            <form method="post" action="emprunt.php#annulation_emprunt" id="form-register">
                <input type="hidden" name="annul_mail" id="annul_mail" value="'.$_SESSION["mail"].'" required/>
                <div class="input-group max center"><span class="input-group-addon form-label start_span projection"><label for="annul_lots">Lots : </label></span><select name="annul_lots" id="annul_lots">
                ');

                $result = recupEmpruntAjd($_SESSION["mail"]);
                print_r($result);
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $date_emprunt = $row["date_emprunt"];
                    $date_retour = $row["date_retour"];
                    setlocale (LC_TIME, 'fr_FR','fra');
                    $new_date_emprunt = utf8_encode(strftime("%d %b %Y",strtotime($date_emprunt)));
                    echo('<option value="'.$date_emprunt.'/'.$date_retour.'">Emprunt du '.$new_date_emprunt.'</option>');
                }
                $result->close();
                echo('</select></div>
                <input type="submit" class="button dark_grey" value="Annuler cet emprunt"/>
            </form>


                <legend>Se déconnecter</legend>
            <form method="post" action="emprunt.php" id="form-register">
                <input type="hidden" name="decon" id="decon" value="1" required/>
                <input type="submit" class="button dark_grey" value="Se déconnecter"/>
            </form>');

            }

            ?>



		</div>
	</div>
 <script>
            $( "#date_emprunt" ).datetimepicker({
                format: 'Y/m/d H:m:s',
                 minDate:'-1970/01/01',
                maxDate:'+1970/03/01',
                lang:'fr',
                step:15
            });

            $( "#date_retour" ).datetimepicker({
                format: 'Y/m/d H:m:s',
                 minDate:'-1970/01/01',
                maxDate:'+1970/03/01',
                lang:'fr',
                step:15
            });
            $( "#modif_date_emprunt" ).datetimepicker({
                format: 'Y/m/d H:m:s',
                 minDate:'-1970/01/01',
                maxDate:'+1970/03/01',
                lang:'fr',
                step:15
            });

            $( "#modif_date_retour" ).datetimepicker({
                format: 'Y/m/d H:m:s',
                 minDate:'-1970/01/01',
                maxDate:'+1970/03/01',
                lang:'fr',
                step:15
            });
            $( "#new_date_emprunt" ).datetimepicker({
                format: 'Y/m/d H:m:s',
                 minDate:'-1970/01/01',
                maxDate:'+1970/03/01',
                lang:'fr',
                step:15
            });

            $( "#new_date_retour" ).datetimepicker({
                format: 'Y/m/d H:m:s',
                 minDate:'-1970/01/01',
                maxDate:'+1970/03/01',
                lang:'fr',
                step:15
            });
    </script>

</body>
</html>
