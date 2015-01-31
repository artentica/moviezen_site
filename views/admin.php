<?php
    session_start();
    include_once("../includes/fonctions.php");
    connect();



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
        }
        $result->free();
        if(!empty($hash)){
            if(password_verify($mdp, $hash) && strcmp($id,$temp)==0){
                $_SESSION["authentifie"]=true;
                $_SESSION["id"] = $id;
            }
            else{
                unset($_SESSION["authentifie"]);
            }
        }
    }




    //GESTION DES ADMINISTRATEURS
    //AJOUT D'ADMINISTRATEUR
    if(!empty($_POST["add_id"]) && !empty($_POST["add_mdp"]) && $_SESSION["authentifie"]){
        addAdmin($_POST["add_id"],$_POST["add_mdp"]);
    }
    
    //Déconnexion
    if(!empty($_POST["deco"]) && $_SESSION["authentifie"]){
        unset($_SESSION["authentifie"]);
    }

    //SUPPRESSION D'ADMINISTRATEUR
    if(!empty($_POST["suppr_admin"]) && $_SESSION["authentifie"]){
        supprAdmin($_POST["suppr_admin"]);
    }

    //CHANGEMENT DE MDP
    if(!empty($_POST["modif_mdp"]) && $_SESSION["authentifie"]){
        modifMDP($_POST["modif_id"],$_POST["modif_mdp"]);
    }




    //GESTION DES PROJECTIONS
    //AJOUT DE PROJECTION
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
                $nom = "../Images/".$nom.".".$extension_upload;
                $resultat = move_uploaded_file($_FILES['projection_affiche']['tmp_name'],$nom);
            }
        }
        addProj($_POST["projection_nom"],$date_release,$_POST["projection_date"],$_POST["projection_description"],$commentaires,$nom);
    }

    //SUPPRESSION DE PROJECTION
    if(!empty($_POST["suppr_proj"]) &&  $_SESSION["authentifie"]){
        supprProj($_POST["suppr_proj"]);
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
        if(!empty($_FILES["new_projection_affiche"])){
            $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' ); 
            $extension_upload = strtolower(  substr(  strrchr($_FILES['new_projection_affiche']['name'], '.')  ,1)  );
            if ( in_array($extension_upload,$extensions_valides) ){
                $nom = md5(uniqid(rand(), true));
                $nom = "../Images/".$nom.".".$extension_upload;
                $resultat = move_uploaded_file($_FILES['new_projection_affiche']['tmp_name'],$nom);
            }
        }
        modifProj($_POST["new_projection_nom"],$date_release,$_POST["new_projection_date"],$_POST["new_projection_description"],$commentaires, $nom, $_POST["old_projection_nom"]);
    }

    //ACTIVATION DE PROJECTION
    if(!empty($_POST["activ_proj"]) && $_SESSION["authentifie"]){
        activateProj($_POST["activ_proj"]);
    }



    //GESTION DES LOTS

    //AJOUT DE LOTS
    if(!empty($_POST["add_lot_id"]) && !empty($_POST["add_lot_composition"]) && !empty($_POST["add_lot_caution"]) && $_SESSION["authentifie"]){
        $nom="";
        if(!empty($_FILES["add_lot_photo"])){
            $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' ); 
            $extension_upload = strtolower(  substr(  strrchr($_FILES['add_lot_photo']['name'], '.')  ,1)  );
            if ( in_array($extension_upload,$extensions_valides) ){
                $nom = md5(uniqid(rand(), true));
                $nom = "../Images/".$nom.".".$extension_upload;
                $resultat = move_uploaded_file($_FILES['add_lot_photo']['tmp_name'],$nom);
            }
        }
        addLot($_POST["add_lot_id"],$_POST["add_lot_composition"],$nom,$_POST["add_lot_caution"]);
    }

    //SUPPRESSION DE LOTS
    if(!empty($_POST["suppr_lot"]) && $_SESSION["authentifie"]){
        supprLot($_POST["suppr_lot"]);
    }

    //MODIFICATION DE LOTS
    if(!empty($_POST["modif_lot_id"]) && !empty($_POST["modif_lot_compo"]) && !empty($_POST["modif_lot_id_old"]) && !empty($_POST["modif_lot_caution"]) && $_SESSION["authentifie"]){
        $nom="";
        if(!empty($_FILES["modif_lot_photo"])){
            $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' ); 
            $extension_upload = strtolower(  substr(  strrchr($_FILES['modif_lot_photo']['name'], '.')  ,1)  );
            if ( in_array($extension_upload,$extensions_valides) ){
                $nom = md5(uniqid(rand(), true));
                $nom = "../Images/".$nom.".".$extension_upload;
                $resultat = move_uploaded_file($_FILES['modif_lot_photo']['tmp_name'],$nom);
            }
        }
        modifLot($_POST["modif_lot_id"],$_POST["modif_lot_compo"],$_POST["modif_lot_caution"],$nom,$_POST["modif_lot_id_old"]);
    }
   

    

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
    <script src="../js/jquery.js"></script>
    <script src="../js/jquery.datetimepicker.js"></script>
    <?php
        include '../includes/include_on_all_page.php';
    ?>
    <script>  
        $(function(){
            $( ".datepicker" ).datetimepicker({
                minDate:'-1970/01/01',
                format: 'Y/m/d h:m:s'
            });
            $( document ).ready(function() {
                $( "#datepicker" ).datetimepicker( "option", "dateFormat", "yy/MMM/dd hh:mm:ss" );    
            });
        });  
    </script>
</head>
<body>
    <div id="banniere">
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
                    <h2>Se déconnecter</h2>
                        <form method="post" action="admin.php" id="form-register">
                            <input type="hidden" name="deco" id="deco" value="1"/>
                            <input type="submit" class="btn btn-danger" value="Se déconnecter"/>
                        </form>
                    <h1>Gestion des administrateurs</h1>
                    
                        <h3>Modifier votre mot de passe :</h3>
                            <form method="post" action="admin.php" id="form-register">
                                <input type="hidden" name="modif_id" id="modif_id" value="'.$_SESSION["id"].'"></input>
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="modif_mdp">Nouveau mot de passe : </label></span><input type="text" name="modif_mdp" id="modif_mdp" placeholder="azertyU²&io$p" class="form-control" aria-describedby="basic-addon1" required/></div>
                                <input type="submit" class="btn btn-success" value="Modifier votre mot de passe"/>
                            </form>
                            
                        <h3>Ajouter un administrateur</h3>
                            <form method="post" action="admin.php" id="form-register">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_id">Identifiant : </label></span><input name="add_id" id="add_id" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_mdp">Mot de passe : </label></span><input type="text" name="add_mdp" id="add_mdp" placeholder="azertyU²&io$p" class="form-control" aria-describedby="basic-addon1" required/></div>

                                <input type="submit" class="btn btn-info" value="Ajouter un administrateur"/>
                            </form>
                            
                            
                            
                        <h3>Supprimer un administrateur</h3>
                            <p>Attention, cette action est irréversible</p>
                            <form method="post" action="admin.php" id="form-register">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="suppr_admin">Identifiant : </label></span><input name="suppr_admin" id="suppr_admin" type="text" placeholder="Turing" class="form-control" aria-describedby="basic-addon1" required/></div>

                                <input type="submit" class="btn btn-danger" value="Supprimer cet administrateur"/>
                            </form>
                
                    <h1>Gestion des projections</h1>
                    <h3>Ajouter une Projection</h3>
                        <form method="post" action="admin.php" id="form-register" enctype="multipart/form-data">
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_nom">Nom du film : </label></span><input name="projection_nom" id="projection_nom" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_release">Date de release : </label></span><input type="date" name="projection_release" id="projection_release" placeholder="AAAA-MM-JJ" class="form-control datepicker" aria-describedby="basic-addon1"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_date">Date de projection : </label></span><input type="date" name="projection_date" id="projection_date" placeholder="AAAA-MM-JJ" class="form-control datepicker" aria-describedby="basic-addon1" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_description">Description : </label></span><input type="text" name="projection_description" id="projection_description" placeholder="Ce film raconte l\'histoire de ..." class="form-control" aria-describedby="basic-addon1" required/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="projection_commentaires">Commentaires : </label></span><input type="text" name="projection_commentaires" id="projection_commentaires" placeholder="Ce film est génial et décevant à la fois" class="form-control" aria-describedby="basic-addon1"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_lot_photo">Affiche de la projection: </label></span><input type="file" name="projection_affiche" id="projection_affiche" class="form-control" aria-describedby="basic-addon1"/></div>
                            <input type="submit" class="btn btn-info" value="Ajouter cette projection"/>
                        </form>
                        
                        <h3>Modifier une projection</h3>
                            <form method="post" action="admin.php" id="form-register">
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="modif_proj">Projection : </label><select name="modif_proj" id="modif_proj">
              ');

                
                $result = recupProj();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $nom = $row["nom"];
                    $date = $row["date_projection"];
                    $date = date("d/m/Y", strtotime($date));
                    echo('<option value="'.$nom.'">'.$nom.' projeté le '.$date.'</option>');
                }
                $result->close();
            
                  echo('  </select></div>
                  
                    <input type="submit" class="btn btn-success" value="Modifier cette projection"/>
                        </form>
                        
                        ');
                    
                    if(!empty($_POST["modif_proj"]) &&  $_SESSION["authentifie"]){
                        $result = recupUniqueProj($_POST["modif_proj"]);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC))
                        {
                            $nom = $row["nom"];
                            $date_release = $row["date_release"];
                            $date_projection = $row["date_projection"];
                            $description = $row["description"];
                            $commentaires = $row["commentaires"];
                            echo('<form method="post" action="admin.php" id="form-register" enctype="multipart/form-data">
                            <input type="hidden" value="'.$nom.'" name="old_projection_nom" id="old_projection_nom"/>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="new_projection_nom">Nom du film : </label></span><input name="new_projection_nom" id="new_projection_nom" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1" required value="'.$nom.'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="new_projection_release">Date de release : </label></span><input type="date" name="new_projection_release" id="new_projection_release" placeholder="AAAA-MM-JJ" class="form-control datepicker" aria-describedby="basic-addon1" value="'.$date_release.'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="new_projection_date">Date de projection : </label></span><input type="date" name="new_projection_date" id="new_projection_date" placeholder="AAAA-MM-JJ" class="form-control datepicker" aria-describedby="basic-addon1" required value="'.$date_projection.'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="new_projection_description">Description : </label></span><input type="text" name="new_projection_description" id="new_projection_description" placeholder="Ce film raconte l\'histoire de ..." class="form-control" aria-describedby="basic-addon1" required value="'.$description.'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="new_projection_commentaires">Commentaires : </label></span><input type="text" name="new_projection_commentaires" id="new_projection_commentaires" placeholder="Ce film est génial et décevant à la fois" class="form-control" aria-describedby="basic-addon1" value="'.$commentaires.'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="new_projection_affiche">Affiche de la projection: </label></span><input type="file" name="new_projection_affiche" id="new_projection_affiche" class="form-control" aria-describedby="basic-addon1"/></div>
                            <input type="submit" class="btn btn-success" value="Modifier cette projection"/>
                            
                            
                            
                            </form>
                            ');
                        }
                        $result->close();
                    }
                    
                    
                    echo('
                    
                        <h3>Rendre une projection active</h3>
                        <form method="post" action="admin.php" id="form-register">
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><select name="activ_proj" id="activ_proj">
                                ');
                            $result = recupProj();
                            while ($row = $result->fetch_array(MYSQLI_ASSOC))
                            {
                                $nom = $row["nom"];
                                $date = $row["date_projection"];
                                $date = date("d/m/Y", strtotime($date));
                                echo('<option value="'.$nom.'">'.$nom.' projeté le '.$date.'</option>');
                            }
                            $result->close();
                    echo('
                            <input type="submit" class="btn btn-warning" value="Activer cette projection"/>
                            </select></div>
                        </form>
                        
                        <h3>Supprimer une projection</h3>
                            <p>Attention, cette action est irréversible</p>
                            <form method="post" action="admin.php" id="form-register">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><select name="suppr_proj" id="suppr_proj">');
                    $result = recupProj();
                    while ($row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $nom = $row["nom"];
                        $date = $row["date_projection"];
                        $date = date("d/m/Y", strtotime($date));
                        echo('<option value="'.$nom.'">'.$nom.' projeté le '.$date.'</option>');
                    }
                    $result->close();
                    echo('</select></div>

                                <input type="submit" class="btn btn-danger" value="Supprimer cette projection"/>
                            </form>
                
                    <h1>Gestion des lots</h1>
                        <h3>Ajouter un lot</h3>
                            <form method="post" action="admin.php" id="form-register" enctype="multipart/form-data">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_lot_id">Identifiant du lot : </label></span><input name="add_lot_id" id="add_lot_id" type="text" placeholder="Lettre majuscule (A,B,K,...)" class="form-control" aria-describedby="basic-addon1" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_lot_composition">Composition du lot: </label></span><input type="textarea" name="add_lot_composition" id="add_lot_composition" placeholder="Caméra sony avec 3 batteries" class="form-control" aria-describedby="basic-addon1" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_lot_caution">Caution du lot (en euros) : </label></span><input type="number" name="add_lot_caution" id="add_lot_caution" placeholder="150" class="form-control" aria-describedby="basic-addon1" required/></div>
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="add_lot_photo">Photo du lot: </label></span><input type="file" name="add_lot_photo" id="add_lot_photo" class="form-control" aria-describedby="basic-addon1"/></div>
                                <input type="submit" class="btn btn-info" value="Ajouter ce lot"/>
                            </form>
                            
                        <h3>Modifier un lot</h3>
                            <form method="post" action="admin.php" id="form-register">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="modif_lot">Projection : </label><select name="modif_lot" id="modif_lot">
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
                                <input type="submit" class="btn btn-success" value="Modifier ce lot"/>
                            </form>
                            
                      ');
                    
                    if(!empty($_POST["modif_lot"]) &&  $_SESSION["authentifie"]){
                        $result = recupUniqueLot($_POST["modif_lot"]);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC))
                        {
                            $id = $row["id"];
                            $composition = $row["composition"];
                            $caution = $row["caution"];
                            echo('<form method="post" action="admin.php" id="form-register" enctype="multipart/form-data">
                            <input type="hidden" value="'.$id.'" name="modif_lot_id_old" id="modif_lot_id_old"/>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="modif_lot_id">Identifiant du lot : </label></span><input name="modif_lot_id" id="modif_lot_id" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1" required value="'.$id.'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="modif_lot_compo">Date de release : </label></span><input type="text" name="modif_lot_compo" id="modif_lot_compo"  class="form-control" aria-describedby="basic-addon1" value="'.$composition.'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="modif_lot_caution">Caution du lot (en euros) : </label></span><input type="number" name="modif_lot_caution" id="modif_lot_caution" placeholder="150" class="form-control" aria-describedby="basic-addon1" value="'.$caution.'"/></div>
                            <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="modif_lot_photo">Photo du lot: </label></span><input type="file" name="modif_lot_photo" id="modif_lot_photo" class="form-control" aria-describedby="basic-addon1"/></div>
                            <input type="submit" class="btn btn-info" value="Modifier ce lot"/>
                            
                            
                            
                            </form>
                            ');
                        }
                        $result->close();
                    }
                    
                    
                    echo('      
                        <h3>Supprimer un lot</h3>
                            <p>Attention, cette action est irréversible</p>
                            <form method="post" action="admin.php" id="form-register">
                                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><select name="suppr_lot" id="suppr_lot">');
                $result = recupLot();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $id = $row["id"];
                    $composition = $row["composition"];
                    echo('<option value="'.$id.'">'.$id.', composé de '.$composition.'</option>');
                }
                $result->close();
                    echo('</select></div>

                                <input type="submit" class="btn btn-danger" value="Supprimer ce lot"/>
                            </form>
                ');
                    
                    
                
                }
            }
            else{
                echo('<h1>Espace d\'administration</h1>
            
            <form method="post" action="admin.php" id="form-register">
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="id">Identifiant : </label></span><input name="id" id="id" type="text" placeholder="Nom" class="form-control" aria-describedby="basic-addon1" required/></div>
                <div class="input-group max center"><span class="input-group-addon form-label" id="basic-addon1"><label for="mdp">Mot de passe : </label></span><input type="password" name="mdp" id="mdp" placeholder="Prénom" class="form-control" aria-describedby="basic-addon1" required/></div>
                
                <input type="submit" class="btn btn-info" value="Se connecter"/>
            </form>');
            }


            ?>
		</div>
	</div>
    
    
</body>
</html>
