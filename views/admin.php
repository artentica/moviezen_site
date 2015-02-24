<?php
    session_start();
    include_once("../includes/fonctions.php");
    include_once("../includes/function_global.php");
    connect();

    $wrongIDMDP = 0;
    $return = 0;
    // PARTIE AUTHENTIFICATION AVEC MDP CRYPTE

    if(!empty($_POST["id"]) && !empty($_POST["mdp"])){
        $temp = protect($_POST["id"]);
        $mdp = protect($_POST["mdp"]);
        $query = "SELECT * FROM admin WHERE identifiant='".$temp."'";
        $result = $GLOBALS["bdd"]->query($query) or trigger_error($GLOBALS["bdd"]->error.$query);
        while ($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $hash = $row["mdp"];
            $id = $row["identifiant"];
            $return++;
        }
        if ($return == 0) $wrongIDMDP = 1;
        $result->free();
        if(!empty($hash)){
            if(password_verify($mdp, $hash) && strcmp($id,$temp)==0){
                $_SESSION["authentifie"]=true;
                $_SESSION["id"] = $id;
            }
            else{
                unset($_SESSION["authentifie"]);
                $wrongIDMDP = 1;
            }
        }
    }


//  Fonction de suppression et modification en tout genre


    //var

    $modifMDP = 0;
    $addAdmini = 0;
    $changeResp = 0;
    $supprAdmin = 0;
    $addProjection = 0;
    $modifProj = 0;
    $activeProj = 0;
    $supprProj = 0;
    $ajoutLot = 0;
    $supprLot = 0;

    //modif MDP
    if(!empty($_POST["modif_mdp"]) && !empty($_POST["ancien_modif_mdp"]) && $_SESSION["authentifie"]){
        if(modifMDP($_POST["modif_id"],$_POST["modif_mdp"],$_POST["ancien_modif_mdp"])){
                $modifMDP = 1;
        }
        else $modifMDP = 2;
    }

    //Ajout Admin
     if(!empty($_POST["add_id"]) && !empty($_POST["add_mdp"]) && !empty($_POST["add_mail"]) && $_SESSION["authentifie"]){
            if(!empty($_POST["add_respons"])){
                $respons = 1;
            }
            else{
                $respons = 0;
            }
         if(addAdmin($_POST["add_id"],$_POST["add_mdp"],$_POST["add_mail"],$respons)) $addAdmini=1;
         else $addAdmini = 2;
     }

    //change resp Admin
    if(!empty($_POST["add_respons_id"]) && isset($_POST["add_respons"])){
        if(changeAdmin($_POST["add_respons_id"],$_POST["add_respons"])) $changeResp = 1;
        else $changeResp = 2;

    }

    //Supprimer Admin
    if(!empty($_POST["suppr_admin"]) && $_SESSION["authentifie"]){
         if(strcmp($_POST["suppr_admin"],$_SESSION["id"])!=0){
                if(supprAdmin($_POST["suppr_admin"])) $supprAdmin = 1;
                else $supprAdmin = 2;
          }
          else $supprAdmin = 3;
    }

    //Ajout de Projection
    if(!empty($_POST["projection_nom"]) && !empty($_POST["projection_date"]) && !empty($_POST["projection_description"]) && $_SESSION["authentifie"]){
        $nom="";
        if(empty($_POST["projection_release"])){
            $date_release = "";
        }
        else{
            $date_release = $_POST["projection_release"];
        }
        if(empty($_POST["projection_commentaires"])){
            $commentaires = "";
        }
        else{
            $commentaires = $_POST["projection_commentaires"];
        }
        if(!empty($_FILES["projection_affiche"])){
            $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
            $extension_upload = strtolower(  substr(  strrchr($_FILES['projection_affiche']['name'], '.')  ,1)  );
            if ( in_array($extension_upload,$extensions_valides) ){
                $nom = md5(uniqid(rand(), true));
                $nom = "../Images/affiche/".$nom.".".$extension_upload;
                $resultat = move_uploaded_file($_FILES['projection_affiche']['tmp_name'],$nom);
            }
        }

        if(!empty($_FILES["back_affiche"])){
            $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
            $extension_upload = strtolower(  substr(  strrchr($_FILES['back_affiche']['name'], '.')  ,1)  );
            if ( in_array($extension_upload,$extensions_valides) ){
                $nomback = md5(uniqid(rand(), true));
                $nomback = "../Images/affiche/".$nomback.".".$extension_upload;
                $resultat = move_uploaded_file($_FILES['back_affiche']['tmp_name'],$nomback);
            }
        }

        if(addProj($_POST["projection_nom"],$date_release,$_POST["projection_date"],$_POST["projection_description"],$commentaires,$nom,$nomback,$_POST["langue"],$_POST["prix"],$_POST["bande_annonce"]))  $addProjection =1;
        else $addProjection = 2;
    }


    //MODIFICATION DE PROJECTION
    if(!empty($_POST["new_projection_nom"]) && !empty($_POST["new_projection_date"]) && !empty($_POST["new_projection_description"]) && !empty($_POST["old_projection_nom"]) && $_SESSION["authentifie"]){
        if(empty($_POST["new_projection_release"])){
            $date_release = "";
        }
        else{
            $date_release = $_POST["new_projection_release"];
        }
        if(empty($_POST["new_projection_commentaires"])){
            $commentaires = "";
        }
        else{
            $commentaires = $_POST["new_projection_commentaires"];
        }
        $nom="";
        $nomback="";
        if(!empty($_FILES["new_projection_affiche"])){
            $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
            $extension_upload = strtolower(  substr(  strrchr($_FILES['new_projection_affiche']['name'], '.')  ,1)  );
            if ( in_array($extension_upload,$extensions_valides) ){
                $nom = md5(uniqid(rand(), true));
                $nom = "../Images/affiche/".$nom.".".$extension_upload;
                $resultat = move_uploaded_file($_FILES['new_projection_affiche']['tmp_name'],$nom);
            }

            if(!empty($_FILES["back_affiche"])){
                $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
                $extension_upload = strtolower(  substr(  strrchr($_FILES['back_affiche']['name'], '.')  ,1)  );
                if ( in_array($extension_upload,$extensions_valides) ){
                    $nomback = md5(uniqid(rand(), true));
                    $nomback = "../Images/affiche/".$nomback.".".$extension_upload;
                    $resultat = move_uploaded_file($_FILES['back_affiche']['tmp_name'],$nomback);
                }
            }
        }
        if(modifProj($_POST["new_projection_nom"],$date_release,$_POST["new_projection_date"],$_POST["new_projection_description"],$commentaires, $nom, $_POST["old_projection_nom"],$nomback,$_POST["langue"],$_POST["prix"],$_POST["bande_annonce"])) $modifProj = 1;
                        else $modifProj = 2;
    }


    //ACTIVATION DE PROJECTION
                    if(!empty($_POST["activ_proj"]) && $_SESSION["authentifie"]){
                        if(activateProj($_POST["activ_proj"])) $activeProj = 1;
                        else $activeProj = 2;
                    }

    //SUPPRESSION DE PROJECTION
                    if(!empty($_POST["suppr_proj"]) &&  $_SESSION["authentifie"]){
                        if(supprProj($_POST["suppr_proj"])) $supprProj = 1;
                        else $supprProj = 2;
                    }
     //AJOUT DE LOTS
                    if(!empty($_POST["add_lot_id"]) && !empty($_POST["add_lot_composition"]) && !empty($_POST["add_lot_caution"]) && $_SESSION["authentifie"]){
                        $nom="";
                        if(!empty($_FILES["add_lot_photo"])){
                            $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
                            $extension_upload = strtolower(  substr(  strrchr($_FILES['add_lot_photo']['name'], '.')  ,1)  );
                            if ( in_array($extension_upload,$extensions_valides) ){
                                $nom = md5(uniqid(rand(), true));
                                $nom = "../Images/lot/".$nom.".".$extension_upload;
                                $resultat = move_uploaded_file($_FILES['add_lot_photo']['tmp_name'],$nom);
                            }
                        }



                        if(addLot($_POST["add_lot_id"],$_POST["add_lot_composition"],$nom,$_POST["add_lot_caution"])) $ajoutLot = 1;
                        else $ajoutLot = 2;
                    }

    //MODIFICATION DE LOTS

                    if(!empty($_POST["modif_lot_id"]) && !empty($_POST["modif_lot_compo"]) && !empty($_POST["modif_lot_id_old"]) && !empty($_POST["modif_lot_caution"]) && $_SESSION["authentifie"]){
                        $nom="";

                        if(!empty($_FILES["modif_lot_photo"])){
                            $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
                            $extension_upload = strtolower(  substr(  strrchr($_FILES['modif_lot_photo']['name'], '.')  ,1)  );
                            if ( in_array($extension_upload,$extensions_valides) ){
                                $nom = md5(uniqid(rand(), true));
                                $nom = "../Images/lot/".$nom.".".$extension_upload;
                                $resultat = move_uploaded_file($_FILES['modif_lot_photo']['tmp_name'],$nom);
                            }
                        }



                        if(modifLot($_POST["modif_lot_id"],$_POST["modif_lot_compo"],$_POST["modif_lot_caution"],$nom,$_POST["modif_lot_id_old"])){
                            $modifie = true;
                        }
                        else{
                            $modifie = false;
                        }
                    }


        //SUPPRESSION DE LOTS
                if(!empty($_POST["suppr_lot"]) && $_SESSION["authentifie"]){
                    if(supprLot($_POST["suppr_lot"])) $supprLot = 1;
                    else $supprLot = 2;
                }

//Nb of admin or 'lot' or projection

    //Nr d'admin
        $nbradmin = 0;
        $temp = recupAdmin();
                while ($row = $temp->fetch_array(MYSQLI_ASSOC))
                {
                    $nbradmin++;
                }
                $temp->close();


    //Nr de proj
        $nbrproj = 0;
        $temp = recupProjDesc();
                while ($row = $temp->fetch_array(MYSQLI_ASSOC))
                {
                    $nbrproj++;
                }
                $temp->close();


    //Nr de lot
        $nbrLot = 0;
        $temp = recupLot();
                while ($row = $temp->fetch_array(MYSQLI_ASSOC))
                {
                    $nbrLot++;
                }
                $temp->close();

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Espace administrateur</title>

	<!-- Set Viewport Options -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />

	<link rel="stylesheet" type="text/css" href="../CSS/index.css">
	<link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../CSS/jquery.datetimepicker.css"/ >
    <?php
        include '../includes/include_on_all_page.php';
    ?>
        <script src="../js/jquery.datetimepicker.js"></script>
        <script src="../js/bootstrap.js"></script>
        <script src="../js/inputfile.js"></script>

    <script>
        $(function(){
            $( ".datepicker" ).datetimepicker({
                lang:'fr',
                closeOnDateSelect:false,
                timepicker:true,
                step:5,
                format:"d/m/Y H:i"
            });
            $( document ).ready(function() {
                $(".back_affiche").filestyle({buttonText: " Affiche de fond (only png)",buttonBefore: true,badge: false});
                $(".affiche").filestyle({buttonText: " Affiche du film",buttonBefore: true,badge: false});
                $(".img_lot").filestyle({buttonText: " Image du lot",buttonBefore: true,badge: false});

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
       include '../includes/panel-global.php';
        include '../includes/menu-mobile.php';?>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">

            <?php

            if(!empty($_SESSION["authentifie"])){
                if($_SESSION["authentifie"]){

                    echo('
                    <a title="Deconnexion button" type="button" class="btn" id="decoInAdmin" href="?deco=1"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a>
                    <h1>Gestion des administrateurs</h1>


                            <form method="post" action="admin.php#mdpchange" id="form-register">
                            <fieldset>
    <legend id="mdpchange">Modifier votre mot de passe</legend>
                                <input type="hidden" name="modif_id" id="modif_id" value="'.$_SESSION["id"].'"></input>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="modif_mdp">Ancien <span title="Mot de passe">MDP</span> : </label></span><input type="password" name="ancien_modif_mdp" id="ancien_modif_mdp" placeholder="p4$$w08d" class="form-control" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="modif_mdp">Nouveau <span title="Mot de passe">MDP</span> : </label></span><input type="password" name="modif_mdp" id="modif_mdp" placeholder="p4$$w08d" class="form-control" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="modif_mdp">Confirmation : </label></span><input type="password" onkeyup="verif_same_new_mdp($(this))" placeholder="p4$$w08d" class="form-control" required/></div>
                                <input id="valid_new_mdp" type="submit" class="button dark_grey" value="Modifier votre mot de passe" disabled/>
                            </fieldset></form>

                        ');

                    //MESSAGE CHANGEMENT DE MDP
                         if($modifMDP == 1){
                             echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Votre mot de passe a été changé avec succés !
                            </div>');
                         }
                        elseif($modifMDP == 2){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Une erreur s\'est déclenchée durant le changement de votre mot de passe</div>');
                        }


                    echo('


                            <form method="post" action="admin.php#add_admin" id="form-register">
                            <fieldset>
    <legend id="add_admin">Ajouter un administrateur</legend>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="add_id">Identifiant : </label></span><input name="add_id" id="add_id" type="text" placeholder="Nom" class="form-control" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="add_mdp">Mot de passe : </label></span><input type="password" name="add_mdp" id="add_mdp" placeholder="p4$$w08d" class="form-control" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="add_mail">Adresse mail : </label></span><input type="email" name="add_mail" id="add_mail" placeholder="admin@gmail.com" class="form-control" required/></div>
                                <label class="checkbox"><input type="checkbox" name="add_respons" value="1">Faire de cet administrateur un responsable des emprunts ?</label>
                                <input type="submit" class="button dark_grey" value="Ajouter un administrateur"/>
                            </fieldset></form>
                        ');

                    //AJOUT D'ADMINISTRATEUR


                        if($addAdmini == 1){

                            echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              L\'administrateur "'.protect($_POST["add_id"]).'" a bien été ajouté à la base de données !
                            </div>');
                        }
                        elseif($addAdmini == 2){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              L\'administrateur "'.protect($_POST["add_id"]).'" n\'a pas pu être ajouté à la base de données !
                            </div>');
                        }
                    }


                    echo('

                            <form method="post" action="admin.php#change_respons" id="form-register">
                            <fieldset>
    <legend id="change_respons">Mettre un administrateur responsable des emprunts</legend>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="add_respons_id">Identifiant : </label></span><select name="add_respons_id" id="add_respons_id">');
                        $result = recupAdmin();
                        while ($row = $result->fetch_array(MYSQLI_ASSOC))
                        {
                            $id = $row["identifiant"];
                            $resp = $row["responsable_emprunt"];
                            echo('<option value="'.$id.'">'.$id);
                            if($resp) echo('   (Responsable)');
                            echo('</option>');
                        }
                        $result->close();


                    echo('</select></div>
                                <label class="checkbox"><input type="radio" name="add_respons" value="1" checked>Faire de cet administrateur un responsable des emprunts</label>
                                <label class="checkbox"><input type="radio" name="add_respons" value="0">Ne plus faire de cet administrateur un responsable des emprunts</label>
                                <input type="submit" class="button dark_grey" value="Modifier la responsabilité"');
                            if($nbradmin == 0)echo " disabled ";
                            echo('/>
                            </fieldset></form>');
                            //Changer Resp Admin

                        if($changeResp == 1){
                                echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              L\'administrateur "'.protect($_POST["add_respons_id"]).'" ');

                                if($_POST["add_respons"])echo('est maintenant responsable des emprunts</div>');
                                else echo('n\'est plus responsable des emprunts</div>');
                            }
                            elseif($changeResp == 2) echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Une erreur s\'est produite en essayant de supprimer cet administrateur</div>');

                        echo('
                            <form method="post" action="admin.php#del_admin" id="form-register"><fieldset>
    <legend id="del_admin">Supprimer un administrateur</legend>
    <p class="be_aware"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> Attention, cette action est irréversible <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span></p>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="suppr_admin">Identifiant : </label></span><select name="suppr_admin" id="suppr_admin">');
                     $result = recupAdmin();
                        while ($row = $result->fetch_array(MYSQLI_ASSOC))
                        {
                            $id = $row["identifiant"];
                            echo('<option value="'.$id.'">'.$id.'</option>');
                        }
                        $result->close();
                    echo('</select></div>


                                                                                                <!-- Button trigger modal -->
<input type="button" class="button dark_grey" data-toggle="modal" onClick="suppr_admin_conf()" value="Confirmer suppression"');
                            if($nbradmin == 0)echo " disabled ";
                            echo('/>


<!-- Modal -->
<div class="modal fade" id="adminModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="lotModalLabel"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> Suppression d\'un Administrateur <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span></h4>
      </div>
      <div id="admin_texte_suppr" class="modal-body">

          <input type="text" id="admin_proj_to_suppr" placeholder="Nom de l\'administrateur" onkeyup="verif_same($(this))" class="form-control" required>


      </div>
      <div class="modal-footer">
        <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span><input type="submit" class="button dark_grey" value="Supprimer cet Administrateur" disabled/><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
      </div>
    </div>
  </div>
</div>


                            </fieldset></form>
                    ');

                    //SUPPRESSION D'ADMINISTRATEUR


                            if($supprAdmin ==1){
                                echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>L\'administrateur "'.protect($_POST["suppr_admin"]).'" a bien été retiré de la base de données</div>');
                            }
                            elseif($supprAdmin == 2){
                                echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Une erreur s\'est produite en essayant de supprimer l\'administrateur: "'.protect($_POST["suppr_admin"]).'"</div>');
                            }

                        if($supprAdmin == 3){
                            echo('<div class="alert alert-danger message">Vous ne pouvez pas vous supprimer vous même !</div>');
                        }

                    echo '</div></div><div class="panel panel-default">
		<div class="panel-body">';
                    echo('
                    <h1>Gestion des projections</h1>

                        <form method="post" action="admin.php#add_proj" id="form-register" enctype="multipart/form-data"><fieldset>
    <legend id="add_proj">Ajouter une Projection</legend>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="projection_nom">Titre du film : </label></span><input name="projection_nom" id="projection_nom" type="text" placeholder="Nom" class="form-control" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="projection_release">Date de sortie : </label></span><input  name="projection_release" id="projection_release" placeholder="jj/mm/aaaa hh:mm" class="form-control datepicker"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="projection_date">Date de projection : </label></span><input  name="projection_date" id="projection_date" placeholder="jj/mm/aaaa hh:mm" class="form-control datepicker" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="projection_description">Description : </label></span><textarea name="projection_description" id="projection_description" placeholder="Ce film raconte l\'histoire de ..." class="form-control" required></textarea></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="projection_commentaires">Commentaires : </label></span><textarea name="projection_commentaires" id="projection_commentaires" placeholder="Ce film est génial et décevant à la fois" class="form-control"></textarea></div>

                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label>Langue : </label></span><input type="text" name="langue" placeholder="VO sous-titré français/VF..." class="form-control" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label>Prix (en &euro;) : </label></span><input type="text" name="prix" placeholder="4" class="form-control" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label>Bande Annonce : </label></span><input type="text" name="bande_annonce" placeholder="https://www.youtube.com/embed/..." class="form-control" required/></div>

                            <div class="input-group max center"><!--<span class="input-group-addon form-label start_span"></span>--><input type="file"  name="projection_affiche" id="projection_affiche" class="affiche form-control" required/></div>
                            <div class="input-group max center"><input type="file" name="back_affiche" id="back_affiche" class="back_affiche form-control" required/></div>
                            <input type="submit" class="button dark_grey" value="Ajouter cette projection"/>
                        </fieldset></form>

                        ');

                        if($addProjection == 1){
                            echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>La projection "'.$_POST["projection_nom"].'" a bien été ajoutée dans la base de données !</div>');
                        }
                        elseif($addProjection == 2){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>La projection "'.$_POST["projection_nom"].'" n\'a pas pu être ajoutée dans la base de données !</div>');
                        }






                    echo('


                            <form method="post" action="admin.php#mod_proj" id="form-register"><fieldset>
    <legend id="mod_proj">Modifier une projection</legend>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="modif_proj">Projection : </label></span><select name="modif_proj" id="modif_proj">
              ');


                $result = recupProjDesc();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $nom = $row["nom"];
                    $date = $row["date_projection"];
                    echo('<option value="'.$nom.'">'.$nom.' projeté le '.date("d/m/Y", $date).' à '.date("H\hi", $date).'</option>');
                }
                $result->close();

                  echo('  </select></div>

                    <input type="submit" class="button dark_grey" value="Modifier cette projection"');
                            if($nbrproj == 0)echo " disabled ";
                            echo('/>
                        </fieldset></form>

                        ');

                    if(!empty($_POST["modif_proj"]) &&  $_SESSION["authentifie"]){
                        $result = recupUniqueProj($_POST["modif_proj"]);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC))
                        {
                            $nom = $row["nom"];
                            $date_release = $row["date_release"];
                            $date_projection = $row["date_projection"];
                            $description = $row["description"];
                            $prix = $row["prix"];
                            $langue = $row["langue"];
                            $bande_annonce = $row["bande_annonce"];
                            $commentaires = $row["commentaires"];


                            $description  = replace_chara($description);
                            $commentaires  = replace_chara($commentaires);
                            $nom  = replace_chara($nom);

                            /*$description = preg_replace("/\r/\n",'<br/>',$description);*/
                            echo('<form method="post" action="admin.php#mod_proj" id="form-register" enctype="multipart/form-data">
                            <input type="hidden" value="'.$nom.'" name="old_projection_nom" id="old_projection_nom"/>
                           <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="projection_nom">Titre du film : </label></span><input name="new_projection_nom" id="new_projection_nom" type="text" placeholder="Nom" class="form-control" required value="'.$nom.'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="projection_release">Date de sortie : </label></span><input  name="new_projection_release" id="new_projection_release" placeholder="jj/mm/aaaa hh:mm" class="form-control datepicker" value="'.date("d/m/Y", $date_release).' '.date("H\hi", $date_release).'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="projection_date">Date de projection : </label></span><input  name="new_projection_date" id="new_projection_date" placeholder="jj/mm/aaaa hh:mm" class="form-control datepicker" required value="'.date("d/m/Y", $date_projection).' '.date("H\hi", $date_projection).'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="projection_description">Description : </label></span><textarea name="new_projection_description" id="new_projection_description" placeholder="Ce film raconte l\'histoire de ..." class="form-control" required> '.$description.'</textarea></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="projection_commentaires">Commentaires : </label></span><textarea name="new_projection_commentaires" id="new_projection_commentaires" placeholder="Ce film est génial et décevant à la fois" class="form-control">'.$commentaires.'</textarea></div>

                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label>Langue : </label></span><input type="text" name="langue" placeholder="VO sous-titré français/VF..." class="form-control" value="'.$langue.'" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label>Prix (en &euro;) : </label></span><input type="text" name="prix" placeholder="4" class="form-control" value="'.$prix.'" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label>Bande Annonce : </label></span><input type="text" name="bande_annonce" placeholder="https://www.youtube.com/embed/..." class="form-control" value="'.$bande_annonce.'" required/></div>

                            <div class="input-group max center"><!--<span class="input-group-addon form-label"><label for="new_projection_affiche">Affiche de la projection: </label></span>--><input type="file" name="new_projection_affiche" id="new_projection_affiche" class="affiche form-control"/></div>
                            <div class="input-group max center"><input type="file" name="back_affiche" class="back_affiche form-control"/></div>
                            <input type="submit" class="button dark_grey" value="Sauvegarder les changements"');
                            if($nbrproj == 0)echo " disabled ";
                            echo('/>



                            </form>
                            ');
                        }
                        $result->close();
                    }


                    //MODIFICATION DE PROJECTION
                     if($modifProj == 1){
                            echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>La projection: "'.$_POST["new_projection_nom"].'" a bien été modifiée !</div>');
                        }
                        elseif($modifProj == 2){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Une erreur s\'est produite lors de la modification de la projection: "'.$_POST["new_projection_nom"].'"</div>');
                        }



                    echo('


                        <form method="post" action="admin.php#act_proj" id="form-register">
                        <fieldset>
    <legend id="act_proj">Rendre une projection active</legend>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label>Projection : </label></span><select name="activ_proj" id="activ_proj">
                                ');
                            $result = recupProjDesc();
                            while ($row = $result->fetch_array(MYSQLI_ASSOC))
                            {
                                $nom = $row["nom"];
                                $date = $row["date_projection"];
                                echo('<option value="'.$nom.'">'.$nom.' projeté le '.date("d/m/Y", $date).' à '.date("H\hi", $date).'</option>');
                            }
                            $result->close();
                    echo('</select></div>
                            <input type="submit" class="button dark_grey" value="Activer cette projection"');
                            if($nbrproj == 0)echo " disabled ";
                            echo('/>

                        </fieldset></form>');

                    //ACTIVATION DE PROJECTION

                        if($activeProj == 1){
                            echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>La projection: "'.$_POST["activ_proj"].'" a bien été activée dans le Ciné de l\'ISEN</div>');
                        }
                        elseif($activeProj == 2){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>La projection: "'.$_POST["activ_proj"].'" n\'a pu être activée</div>');
                        }




                    echo('



                            <form method="post" action="admin.php#del_proj" id="form-register"><fieldset>
    <legend id="del_proj">Supprimer une projection</legend>
    <p class="be_aware"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>Attention, cette action est irréversible<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span></p>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label>Projection : </label></span><select name="suppr_proj" id="suppr_proj">');
                    $result = recupProjDesc();
                    while ($row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $nom = $row["nom"];
                        $date = $row["date_projection"];
                        echo('<option value="'.$nom.'">'.$nom.' projeté le '.date("d/m/Y", $date).' à '.date("H\hi", $date).'</option>');
                    }
                    $result->close();
                    echo('</select></div>


                                <!-- Button trigger modal -->
<button type="button" class="button dark_grey" data-toggle="modal" onClick="suppr_projec_conf()"');
                            if($nbrproj == 0)echo " disabled ";
                            echo('>
  Confirmer suppression
</button>

<!-- Modal -->
<div class="modal fade" id="projectionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="projectionModalLabel"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>Suppression de projections<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span></h4>
      </div>
      <div id="proj_texte_suppr" class="modal-body">

          <input type="text" id="anim_proj_to_suppr" placeholder="Nom de la projection" onkeyup="verif_same($(this))" class="form-control" required>


      </div>
      <div class="modal-footer">
        <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span><input type="submit" class="button dark_grey" value="Supprimer cette projection" disabled/><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
      </div>
    </div>
  </div>
</div>
                            </fieldset></form>');

                    //SUPPRESSION DE PROJECTION

                        if($supprProj == 1){
                            echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>La projection "'.$_POST["suppr_proj"].'" a bien été retirée dans la base de données !</div>');
                        }
                        elseif($supprProj == 2){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Une erreur s\'est produite lors de la suppression de: "'.$_POST["suppr_proj"].'" </div>');
                        }




echo '</div></div><div class="panel panel-default">
		<div class="panel-body">';



                    //GESTION DES LOTS





                    echo('

                    <h1>Gestion des lots</h1>
                            <form method="post" action="admin.php#ajoute_lot" id="form-register" enctype="multipart/form-data">
                            <fieldset>
    <legend id="ajoute_lot">Ajouter un lot</legend>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="add_lot_id"><span title="Identifiant">Id</span> du lot : </label></span><input name="add_lot_id" id="add_lot_id" type="text" placeholder="Lettre majuscule (A,B,K,...)" class="form-control" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="add_lot_composition">Description: </label></span><textarea name="add_lot_composition" id="add_lot_composition" placeholder="Caméra sony avec 3 batteries" class="form-control" required></textarea></div>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="add_lot_caution"><span title="en euro (&euro;)">Caution du lot : </label></span><input type="number" name="add_lot_caution" id="add_lot_caution" placeholder="150&euro;" class="form-control" required/></div>
                                <div class="input-group max center"><input type="file" name="add_lot_photo" id="add_lot_photo" class="img_lot form-control" required/></div>
                                <input type="submit" class="button dark_grey" value="Ajouter ce lot"/>
                            </fieldset></form>
                            ');

                    //AJOUT DE LOTS

                        if($ajoutLot == 1){

                            echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Le lot: "'.$_POST["add_lot_id"].'" a bien été ajouté dans la base de données</div>');
                        }
                        elseif($ajoutLot == 2){
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Le lot: "'.$_POST["add_lot_id"].'" n\'a pas pu être ajouté dans la base de données</div>');
                        }




                    echo('

                            <form method="post" action="admin.php#modifie_lot" id="form-register"><fieldset>
    <legend id="modifie_lot">Modifier un lot</legend>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="modif_lot">Lot(s) : </label></span><select name="modif_lot" id="modif_lot">
                                     ');


                $result = recupLot();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $id = $row["id"];
                    $composition = $row["composition"];
                    echo('<option value="'.$id.'">'.$id.', composé de '.$composition.'</option>');
                }
                $result->close();
                  echo('
                                </select></div>
                                <input type="submit" class="button dark_grey" value="Modifier ce lot"');
                            if($nbrLot == 0)echo " disabled ";
                            echo('/>
                            </fieldset></form>

                      ');

                    if(!empty($_POST["modif_lot"]) &&  $_SESSION["authentifie"]){
                        $result = recupUniqueLot($_POST["modif_lot"]);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC))
                        {
                            $id = $row["id"];
                            $composition = $row["composition"];
                            $caution = $row["caution"];
                            echo('<form method="post" action="admin.php#modifie_lot" id="form-register" enctype="multipart/form-data">

                            <input type="hidden" value="'.$id.'" name="modif_lot_id_old" id="modif_lot_id_old"/>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="modif_lot_id"><span title="Identifiant">Id</span> du lot : </label></span><input name="modif_lot_id" id="modif_lot_id" type="text" placeholder="Lettre majuscule (A,B,K,...)" class="form-control" required value="'.$id.'"/></div>
                             <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="modif_lot_compo">Description: </label></span><textarea name="modif_lot_compo" id="modif_lot_compo" placeholder="Caméra sony avec 3 batteries" class="form-control" required>'.$composition.'</textarea></div>
                            <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="modif_lot_caution"><span title="en euro (&euro;)">Caution du lot : </label></span><input type="number" name="modif_lot_caution" id="modif_lot_caution" placeholder="150&euro;" class="form-control" required value="'.$caution.'"/></div>
                            <div class="input-group max center"><!--<span class="input-group-addon form-label start_span"><label for="modif_lot_photo">Photo du lot: </label></span>--><input type="file" name="modif_lot_photo" id="modif_lot_photo" class="img_lot form-control"/></div>
                            <input type="submit" class="button dark_grey" value="Sauvegarder les changements"/>



                            </form>
                            ');
                        }
                        $result->close();
                    }

                    if(!empty($modifie)){
                        if($modifie){
                            echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Le lot: "'.$_POST["modif_lot_id"].'" a été correctement modifié !</div>');
                        }
                        else{
                            echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Le lot: "'.$_POST["modif_lot_id"].'" n\'a pas pu être modifié !</div>');
                        }
                    }
                        echo('

                            <form method="post" action="admin.php#supprimer_lot" id="form-register"><fieldset>
    <legend id="supprimer_lot">Supprimer un lot</legend>
    <p class="be_aware"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>Attention, cette action est irréversible<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span></p>
                                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label>Lot(s) : </label></span><select name="suppr_lot" id="suppr_lot">');
                $result = recupLot();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $id = $row["id"];
                    $composition = $row["composition"];
                    echo('<option value="'.$id.'">'.$id.', composé de '.$composition.'</option>');
                }
                $result->close();
                    echo('</select></div>

                                                                <!-- Button trigger modal -->
<button type="button" class="button dark_grey" data-toggle="modal" onClick="suppr_lot_conf()"');
                            if($nbrLot == 0)echo " disabled ";
                            echo('>
  Confirmer suppression
</button>

<!-- Modal -->
<div class="modal fade" id="lotModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="lotModalLabel"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>Suppression de Lot<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span></h4>
      </div>
      <div id="lot_texte_suppr" class="modal-body">

          <input type="text" id="lot_proj_to_suppr" placeholder="Nom du lot" onkeyup="verif_same($(this))" class="form-control" required>


      </div>
      <div class="modal-footer">
        <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span><input type="submit" class="button dark_grey" value="Supprimer ce lot" disabled/><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
      </div>
    </div>
  </div>
</div>
                            </fieldset></form>
                ');

                //SUPPRESSION DE LOTS

                    if($supprLot == 1){
                        echo('<div class="alert message alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Le lot: "'.$_POST["suppr_lot"].'" a bien été supprimé !</div>');
                    }
                    elseif($supprLot == 2){
                        echo('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Le lot: "'.$_POST["suppr_lot"].'" n\'a pas pu être supprimé !</div>');
                    }


                }

            else{
                echo('<h1>Espace d\'administration</h1>');
                if($wrongIDMDP == 1) echo ('<div class="alert message alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Mauvais Username ou Mot de passe !</div>');
            echo ('<form method="post" action="admin.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="id">Identifiant : </label></span><input name="id" id="id" type="text" placeholder="Username" class="form-control" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label start_span"><label for="mdp">Mot de passe : </label></span><input type="password" name="mdp" id="mdp" placeholder="Password" class="form-control" required/></div>

                <input type="submit" class="button dark_grey" value="Se connecter"/>
            </form>');
            }

            ?>






		</div>
	</div>

<script>
    function suppr_projec_conf(){
        $('#projectionModal').modal({backdrop: true,keyboard: true});
        var proj = $('#suppr_proj').val();
        $( "#proj_texte_suppr p" ).remove();
        $( "#proj_texte_suppr" ).prepend('<p>Etes vous sûr(e) de vouloir supprimer la projection: "<span value="'+proj+'">'+proj+'</span>" ?</p>');

    }

    function suppr_lot_conf(){
        $('#lotModal').modal({backdrop: true,keyboard: true});
        var lot = $('#suppr_lot').val();
        $( "#lot_texte_suppr p" ).remove();
        $( "#lot_texte_suppr" ).prepend('<p>Etes vous sûr(e) de vouloir supprimer le lot: "<span value="'+lot+'">'+lot+'</span>" ?</p>');

    }


    function suppr_admin_conf(){
        $('#adminModal').modal({backdrop: true,keyboard: true});
        var admin = $('#suppr_admin').val();
        $( "#admin_texte_suppr p" ).remove();
        $( "#admin_texte_suppr" ).prepend('<p>Etes vous sûr(e) de vouloir supprimer l\'administrateur: "<span value="'+admin+'">'+admin+'</span>" ?</p>');

    }

    function verif_same_new_mdp(e){
        ($("#modif_mdp").val()==e.val())?$("#valid_new_mdp").attr('disabled',false):$("#valid_new_mdp").attr('disabled',true);
    }

    function verif_same(e){
        var to_suppr = e.parent().children("p").children("span").text();

        var currentval = e.val();
        var input = e.parent().parent().children(".modal-footer").children("input.button.dark_grey");
        (to_suppr==currentval)?input.attr('disabled',false):input.attr('disabled',true);
/*
        console.log(currentval+"  "+to_suppr);
*/
/*
        console.log(to_suppr);
*/
    }
</script>


    </body>
</html>
