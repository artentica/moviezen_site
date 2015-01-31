<?php 

    //FONCTION DE CONNEXION A LA BDD
    function connect(){
		$GLOBALS["bdd"] = new mysqli('localhost', 'utilisateur', 'azerty', 'moviezen');
		$GLOBALS["bdd"]->set_charset("utf8");
		if ($GLOBALS["bdd"]->connect_errno){
			echo "Echec lors de la connexion à MySQL :(" . $GLOBALS["bdd"]->connect_errno .") " . $GLOBALS["bdd"]->connect_errno;
			return false;
		}
		return true;
	}


    //FONCTION DE PROTECTION DES CHAINES UTILISATEURS
    function protect($chaine){
        $protect = $GLOBALS["bdd"]->real_escape_string(stripslashes(html_entity_decode ($chaine)));	 
        return $protect;
    }


//################################################################################################################################################################

    //FONCTIONS GESTION DES INSCRITS

    
    //AJOUT D'INSCRITS A UNE PROJECTION (UTILISATEUR)
    function ajoutInscrit($nom,$prenom,$mail,$classe,$projection){
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits VALUES (?, ?, '', ?, ?)");
        $nom = protect($nom);
        $prenom = protect($prenom);
        $mail = protect($mail);
        $classe = protect($classe);
        $query->bind_param('ssss', $nom,$prenom,$mail, $classe);
        $query->execute();
        $projection = protect($projection);
        $query2 = $GLOBALS["bdd"]->prepare("INSERT INTO projections_inscrits VALUES (?, ?)");
        $query2->bind_param('ss', $mail, $projection);
        $query2->execute();
        $query->close();
        return true;
    }


    
    //FONCTION MODIFICATION D'INSCRITS A UNE PROJECTION (UTILISATEUR)
    function modifInscrit($mail, $projection, $ancien_mail){
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections_inscrits SET  inscrit_mail=?, projection=? WHERE inscrit_mail=?");
        $mail = protect($mail);
        $projection = protect($projection);
        $ancien_mail = protect($ancien_mail);
        $query->bind_param('sss', $mail, $prenom, $ancien_mail);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION SUPPRESSION D'INSCRITS A UNE PROJECTION  (UTILISATEUR)
    function supprInscrit($mail,$projection){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM projections_inscrits WHERE inscrit_mail=? and projection=?");
        $mail = protect($mail);
        $projection = protect($projection);
        $query->bind_param('ss', $mail,$projection);
        $query->execute();
        $query->close();
        return true;
    }






//################################################################################################################################################################


    //FONCTIONS GESTION DES EMPRUNTS

    //FONCTION AJOUT D'EMPRUNT
    function ajoutEmprunt($nom,$prenom,$tel,$mail, $classe,$lots,$date_emprunt,$date_retour){
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits VALUES (?, ?, ?, ?, ?)");
        $nom = protect($nom);
        $prenom = protect($prenom);
        $tel = protect($tel);
        $mail = protect($mail);
        $classe = protect($classe);
        $query->bind_param('sssss', $nom,$prenom,$tel,$mail,$classe);
        $query->execute();
        $query->close();
        $lots = protect($lots);
        $date_emprunt = protect($date_emprunt);
        $date_retour = protect($date_retour);
        $date_emprunt = date("Y-m-d H:m:s", strtotime($date_emprunt));
        $date_retour = date("Y-m-d H:m:s", strtotime($date_retour));
        $date_ajd = date("Y-m-d H:m:s");
        $date_ajd = new DateTime($date_ajd);
        $date_ajd = $date_ajd->format('Ymd');
        $date_emprunt_test = new DateTime($date_emprunt);
        $date_emprunt_test = $date_emprunt_test->format('Ymd');
        $date_retour_test = new DateTime($date_retour);
        $date_retour_test = $date_retour_test->format('Ymd');
        if( $date_ajd < $date_emprunt_test && $date_emprunt_test < $date_retour_test ){
            if(strpos($lots, ",")!=false){
                $liste = explode(",", $lots);
            }
            else if(strpos($lots, "-")!=false){
                $liste = explode("-", $lots);
            }
            else if(strpos($lots, "/")!=false){
                $liste = explode("/", $lots);
            }
            else if(strpos($lots, " ")!=false){
                $liste = explode(" ", $lots); 
            }
            else{
                $liste = str_split($lots);
            }
            foreach($liste as $liste){
                $query2 = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits_lots VALUES (?, ?, ?, ?)");
                $query2->bind_param('ssss', $mail,$liste,$date_emprunt,$date_retour);
                $query2->execute();
                $query2->close();
                $query2 = $GLOBALS["bdd"]->prepare("UPDATE lots SET disponible='0' WHERE id=?");
                $query2->bind_param('s', $liste);
                $query2->execute();
                $query2->close();
            }
            return true;
        }
        else{
            return false;   
        }
    }



    //FONCTION MODIFICATION D'EMPRUNT (UTILISATEUR)
    function modifEmprunt($lots,$date_emprunt,$date_retour,$mail){
        $query = $GLOBALS["bdd"]->prepare("UPDATE inscrits_lots SET lots=?, date_emprunt=?, date_retour=? WHERE inscrit_mail=?");
        $lots = protect($lots);
        $date_emprunt = protect($date_emprunt);
        $date_retour = protect($date_retour);
        $mail = protect($mail);
        $query->bind_param('ssss', $lots, $date_emprunt, $date_retour, $mail);
        $query->execute();
        $query->close();
        return true;
    }

    
    //FONCTION SUPPRESSION D'UN EMPRUNT(UTILISATEUR)
    function supprEmprunt($mail){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM inscrits_lots WHERE inscrit_mail=?");
        $mail = protect($mail);
        $query->bind_param('s',$mail);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE RECUPERATION DES EMPRUNTS EFFECTUES PAR UN INSCRIT
    function recupEmprunt($mail){
        $mail = protect($mail);
        $query = "SELECT * FROM inscrits_lots WHERE inscrit_mail='".$mail."'";
        return $GLOBALS["bdd"]->query($query);
    }




//################################################################################################################################################################
    


    //FONCTIONS GESTION DES PROJECTIONS



    //FONCTION DE RECUPERATION DES INSCRITS A UNE PROJECTION, CREE UN DOCUMENT XLS TELECHARGEABLE SUR LE SERVEUR
    function recupInscrit($projection){
        $projection = protect($projection);
        $query = "SELECT * from projections_inscrits WHERE projection='".$projection."'";
        $result = $GLOBALS["bdd"]->query($query);
        echo('<table id="table-style" data-toggle="table"><thead><tr><th>Numéro</th><th class="col-md-6">Nom</th><th class="col-md-6">Prenom</th><th class="col-md-4">Classe</th></tr></thead>');
        $table = "<html><body><table><tr><td><b>Nom</b></td><td><b>Prenom</b></td><td><b>Classe</b></td></tr>";
        $i=1;
        while ($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            
            $mail = $row["inscrit_mail"];
            $query = "SELECT * from inscrits WHERE mail='".$mail."'";
            $result2 = $GLOBALS["bdd"]->query($query);
            while ($row2 = $result2->fetch_array(MYSQLI_ASSOC))
            {
                $nom = $row2["nom"];
                $prenom = $row2["prenom"];
                $classe = $row2["classe"];
                $table = $table."<tr>";
                $table = $table."<td>".$nom."</td><td>".$prenom."</td><td>".$classe."</td>";
                $table = $table."</tr>";
                echo('<tr><td>'.$i.'</td><td>'.$nom.'</td><td>'.$prenom.'</td><td>'.$classe.'</td></tr>');
                $i++;
            }
            $result2->close();
            
        }
        $table = $table."</table></body></html>";
        fopen("inscrits.xls","w+"); 
        $file = ("inscrits.xls"); 
        if(!$myfile = fopen($file, "w+"))     
        {
            print("erreur: ");
            print("le fichier n'existe pas!\n");
            exit;
        }
        fwrite($myfile,$table,strlen($table));
        fclose($myfile);
        $result->close();
        echo('</table>');
        return true;
    }


    //FONCTION RECUPERANT TOUTES LES PROJECTIONS EXISTANTES
    function recupProj(){
        $query = "SELECT * from projections";
        return $GLOBALS["bdd"]->query($query);
    }

    //FONCTION RECUPERANT UNE PROJECTION EN PARTICULIER
    function recupUniqueProj($nom){
        $nom = protect($nom);
        $query = 'SELECT * from projections WHERE nom="'.$nom.'"';
        return $GLOBALS["bdd"]->query($query);
    }

    //FONCTION PERMETTANT D'ACTIVER UNE PROJECTION (MET LA PROJECTION AFFICHEE SUR LA PAGE CINE)
    function activateProj($nom){
        $nom = protect($nom);
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET active='0' WHERE active='1'");
        $query->execute();
        $query->close();
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET active='1' WHERE nom=?");
        $query->bind_param('s',$nom);
        $query->execute();
        $query->close();
    }

    //FONCTION RECUPERANT LA PROJECTION ACTIVE ACTUELLE
    function recupProjActive(){
        $query ="SELECT * FROM projections WHERE active='1'";
        return $GLOBALS["bdd"]->query($query);
    }



    //FONCTION D'AJOUT D'UNE PROJECTION A LA BDD
    function addProj($nom,$date_release,$date_projection,$description,$commentaires,$affiche){
        $nom = protect($nom);
        $date_release = protect($date_release);
        $date_projection = protect($date_projection);
        $description = protect($description);
        $commentaires = protect($commentaires);
        $affiche = protect($affiche);
        $date_release = date("Y-m-d", strtotime($date_release));
        $date_projection = date("Y-m-d", strtotime($date_projection));
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO projections VALUES(?,?,?,?,?,?)");
        $query->bind_param('ssssss',$nom,$date_release,$date_projection,$description,$commentaires,$affiche);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE SUPPRESSION D'UNE PROJECTION DE LA BDD
    function supprProj($nom){
        $nom = protect($nom);
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM projections WHERE nom=?");
        $query->bind_param('s',$nom);
        $query->execute();
        $query->close();
        $query2 = $GLOBALS["bdd"]->prepare("DELETE FROM projections_inscrits WHERE projection=?");
        $query2->bind_param('s',$nom);
        $query2->execute();
        $query2->close();
        return true;
    }

    //FONCTION DE MODIFICATION D'UNE PROJECTION
    function modifProj($nom,$date_release,$date_projection,$description,$commentaires,$affiche,$ancien_nom){
        $nom = protect($nom);
        $date_release = protect($date_release);
        $date_projection = protect($date_projection);
        $description = protect($description);
        $commentaires = protect($commentaires);
        $ancien_nom = protect($ancien_nom);
        $date_release = date("Y-m-d", strtotime($date_release));
        $date_projection = date("Y-m-d", strtotime($date_projection));
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET nom=?, date_release=?, date_projection=?, description=?, commentaires=?, affiche=? WHERE nom=?");
        $query->bind_param('sssssss',$nom,$date_release,$date_projection,$description,$commentaires,$affiche,$ancien_nom);
        $query->execute();
        $query->close();
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections_inscrits SET projection=? WHERE projection=?");
        $query->bind_param('ss',$nom,$ancien_nom);
        $query->execute();
        $query->close();
        return true;
    }

    



//################################################################################################################################################################



    //FONCTION GESTION DES ADMINISTRATEURS


    //FONCTION VERIFIANT SI L'UTILISATEUR EST CONNU OU NON
    function recupID($identifiant){
        $identifiant = protect($identifiant);
        $query = "SELECT * FROM admin WHERE identifiant=".$identifiant;
        return $GLOBALS["bdd"]->query($query);
    }

    

    //FONCTION D'AJOUT D'UN ADMIN DANS LA BASE
    function addAdmin($identifiant,$mdp){
        $identifiant = protect($identifiant);
        $mdp = protect($mdp);
        $mdp = password_hash($mdp,PASSWORD_DEFAULT);
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO admin VALUES(?,?)");
        $query->bind_param('ss',$identifiant,$mdp);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE SUPPRESSION D'UN ADMIN DANS LA BASE
    function supprAdmin($identifiant){
        $identifiant = protect($identifiant);
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM admin WHERE identifiant=?");
        $query->bind_param('s',$identifiant);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE CHANGEMENT DE MOT DE PASSE POUR L'ADMINISTRATEUR COURANT
    function modifMDP($identifiant, $mdp){
        $identifiant = protect($identifiant);
        $mdp = protect($mdp);
        $mdp = password_hash($mdp,PASSWORD_DEFAULT);
        $query = $GLOBALS["bdd"]->prepare("UPDATE admin SET mdp=? WHERE identifiant=?");
        $query->bind_param('ss',$mdp,$identifiant);
        $query->execute();
        $query->close();
        return true;
    }

//################################################################################################################################################################

    //FONCTIONS GESTION DES LOTS

    //FONCTION D'AJOUT D'UN LOT
    function addLot($identifiant, $composition,$image,$caution){
        $identifiant = protect($identifiant);
        $composition = protect($composition);
        $caution = protect($caution);
        $disponible = 1;
        $image = protect($image);
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO lots VALUES(?,?,?,?,?)");
        $query->bind_param('ssisi',$identifiant,$composition,$disponible,$image,$caution);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE SUPPRESSION D'UN LOT
    function supprLot($identifiant){
        $identifiant = protect($identifiant);
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM lots WHERE id=?");
        $query->bind_param('s',$identifiant);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE MODIFICATION D'UN LOT
    function modifLot($identifiant,$composition, $caution, $image,$ancien_identifiant){
        $identifiant = protect($identifiant);
        $composition = protect($composition);
        $ancien_identifiant = protect($ancien_identifiant);
        $caution = protect($caution);
        $query = $GLOBALS["bdd"]->prepare("UPDATE lots SET id=?, composition=?, image=?, caution=?  WHERE id=?");
        $query->bind_param('sssss',$identifiant,$composition,$image,$caution,$ancien_identifiant);
        $query->execute();
        $query->close();
        return true;
    }



    //FONCTION DE RECUPERATION DE TOUT LES LOTS
    function recupLot(){
        $query = "SELECT * from lots";
        return $GLOBALS["bdd"]->query($query);
    }

    function recupUniqueLot($id){
        $id = protect($id);
        $query = "SELECT * from lots WHERE id='".$id."'";
        return $GLOBALS["bdd"]->query($query);
    }

?>