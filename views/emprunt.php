<?php
    session_start();
    include_once("../includes/fonctions.php");

    connect();

    $_SESSION["emprunteur"]=0;
    
     if(!empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["mail"]) && !empty($_POST["tel"]) && !empty($_POST["classe"]) && !empty($_POST["lots"]) && !empty($_POST["date_emprunt"]) && !empty($_POST["date_retour"]) && $_POST["accepter"])
     {
        
             if(ajoutEmprunt($_POST["nom"],$_POST["prenom"],$_POST["tel"],$_POST["mail"], $_POST["classe"],$_POST["lots"],$_POST["date_emprunt"],$_POST["date_retour"])){
                $_SESSION["mail"]=protect($_POST["mail"]);
                $_SESSION["emprunteur"]=1;
             }
        
     }

    if(!empty($_POST["del_mail"])){
        if(supprEmprunt($_POST["del_mail"])){
            $_SESSION["emprunteur"]=0;
            unset($_SESSION["mail"]);
        }
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
	<link rel="stylesheet" type="text/css" href="../CSS/menu.css">
	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../CSS/jquery.datetimepicker.css"/ >
    <script src="../js/jquery-2.1.3.min.js"></script>
	<script src="../js/menu.js"></script>
    <script src="../js/jquery.datetimepicker.js"></script>
    <script>  
        $(function(){
            $( ".datepicker" ).datetimepicker({
                minDate:'-1970/01/01',
                maxDate:'+1970/03/01',
                showSecond: true, 
                timeFormat: 'hh:mm:ss:l'

            });
        });  
    </script>
</head>
<body>
    <img src="../Images/moviezen2.jpg" alt="bannière" id="banniere"/>
    <header>
        <?php
       include '../includes/panel-global.php';
        include '../includes/menu-mobile.php';?>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">
            
            <?php

            echo('<table><thead><th>Identifiant de lot</th><th>Composition du lot</th><th>Disponible</th><th>Indisponible jusqu\'au</th></thead>');

            
            echo('</table>');

            if(!$_SESSION["emprunteur"]){

                echo('<h1>Emprunter du matériel</h1>
            <p>Merci de renseigner tout les champs</p>
            <form method="post" action="emprunt.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="nom">Nom : </label></span><input name="nom" id="nom" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="prenom">Prénom : </label></span><input type="text" name="prenom" id="prenom" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="mail">@ ISEN : </label></span><input type="email" name="mail" id="mail" placeholder="Essai.tarte@orange.fr" class="form-control" aria-describedby="basic-addon1" required pattern="[a-z0-9._%+-]+@(isen(?:-bretagne)\.fr)$"/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="tel">Tel (portable de préférence) : </label></span><input type="tel" name="tel" id="tel" placeholder="0600000000" class="form-control" aria-describedby="basic-addon1" pattern="^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="classe">Classe : </label></span><input type="text" name="classe" id="classe" placeholder="CIR3" class="form-control" aria-describedby="basic-addon1" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="lots">Lots : </label></span><input type="text" name="lots" id="lots" placeholder="A,K,L,C,...." class="form-control" aria-describedby="basic-addon1"/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="date_emprunt">Date d\'emprunt : </label></span><input type="date" name="date_emprunt" id="date_emprunt" placeholder="AAAA-MM-DD" class="form-control datepicker" aria-describedby="basic-addon1" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="date_retour">Date de retour : </label></span><input type="date" name="date_retour" id="date_retour" placeholder="AAAA-MM-DD" class="form-control datepicker" aria-describedby="basic-addon1" required/></div>
                
                <label><input type="checkbox" name="accepter" required value="1"> <b>Je reconnais avoir pris connaissance des conditions d\'utilisation de l\'emprunt de matériel Moviezen et jure sur l\'honneur de m\'y tenir, sans quoi Satan viendra moisonner mon âme</b></label><br/>
                <input type="submit" class="btn btn-success" value="S\'inscrire"/>
            </form>
            
            <h2>Rappel des règles d\'emprunt concernant le matériel Moviezen</h2>
            <p>Une fois l\'inscription effectuée, le matériel vous est réservé durant la période demandée. Il est évident que cela vous engage à respecter les délais spécifiés. Les délais trop longs tels que des emprunts de plus de 3 mois par exemple ne sont pas autorisés. Un chèque de caution doit être émis à l\'ordre de Moviezen avec le montant total des lots empruntés. Ce chèque de caution est évidemment conservé en guise de garantie et ne sera pas touché si le matériel est rendu dans le même état que lors de l\'emprunt.</p>
            
            
            <h3>Vous avez déja emprunté du matériel ? Connectez vous</h3>
            <form method="post" action="emprunt.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="conn_mail">@ ISEN : </label></span><input type="email" name="conn_mail" id="conn_mail" placeholder="Essai.tarte@orange.fr" class="form-control" aria-describedby="basic-addon1"/></div>
                
                <input type="submit" class="btn btn-info" value="Se connecter"/>
            </form>
            
            ');
                
            if(!empty($_POST["conn_mail"])){
                echo('Vous avez emprunté : <ol>');
                $result = recupEmprunt($_POST["conn_mail"]);
                $_SESSION["conn_mail"]=protect($_POST["conn_mail"]);
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $lot = $row["lots"];
                    $date_emprunt = $row["date_emprunt"];
                    $date_retour = $row["date_retour"];
                    echo('<li>Lot '.$lot.' ');
                    echo('du '.$date_emprunt.' au '.$date_retour.'</li>');
                }
                echo('</ol>');
                $result->close();
            }
                
            }
            else{
                echo('
                <h1>Modifier un emprunt</h1>
                <form method="post" action="emprunt.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="modif_mail">@ ISEN : </label></span><input type="email" name="modif_mail" id="modif_mail" placeholder="Essai.tarte@orange.fr" class="form-control" aria-describedby="basic-addon1"/></div>
                
                <input type="submit" class="btn btn-success" value="Modifier mon emprunt"/>
                </form>
                
                
                <h1>Annuler un emprunt</h1>
            <form method="post" action="emprunt.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="del_mail">@ ISEN : </label></span><input type="email" name="del_mail" id="del_mail" placeholder="Essai.tarte@orange.fr" class="form-control" aria-describedby="basic-addon1" required/></div>
                
                <input type="submit" class="btn btn-danger" value="Se désinscrire"/>
            </form>');
                
            }

            ?>
			
            
            
		</div>
	</div>
    
    
</body>
</html>
