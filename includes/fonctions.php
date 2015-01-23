<?php 
    function connect(){
		$GLOBALS["bdd"] = new mysqli('localhost', 'utilisateur', 'azerty', 'moviezen');
		$GLOBALS["bdd"]->set_charset("utf8");
		if ($GLOBALS["bdd"]->connect_errno){
			echo "Echec lors de la connexion à MySQL :(" . $GLOBALS["bdd"]->connect_errno .") " . $GLOBALS["bdd"]->connect_errno;
			return false;
		}
		return true;
	}

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

    function supprInscrit($mail,$projection){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM projections_inscrits WHERE inscrit_mail=? and projection=?");
        $mail = protect($mail);
        $projection = protect($projection);
        $query->bind_param('ss', $mail,$projection);
        $query->execute();
        $query->close();
        return true;
    }

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
        $query2 = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits_lots VALUES (?, ?, ?, ?)");
        $query2->bind_param('ssss', $mail,$lots,$date_emprunt,$date_retour);
        $query2->execute();
        $query2->close();
        return true;
    }

    function modifEmprunt($lots,$date_emprunt,$date_retour,$mail){
        $query = $GLOBALS["bdd"]->prepare("UPDATE inscrits_lots SET lots=?, date_emprunt=?, date_retour=?, WHERE inscrit_mail=?");
        $lots = protect($lots);
        $date_emprunt = protect($date_emprunt);
        $date_retour = protect($date_retour);
        $mail = protect($mail);
        $query->bind_param('ssss', $lots, $date_emprunt, $date_retour, $mail);
        $query->execute();
        $query->close();
        return true;
    }

    function supprEmprunt($mail){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM inscrits_lots WHERE inscrit_mail=?");
        $mail = protect($mail);
        $query->bind_param('s',$mail);
        $query->execute();
        $query->close();
        return true;
    }

    function recupID($identifiant){
        $identifiant = protect($identifiant);
        $query = "SELECT * FROM admin WHERE identifiant=".$identifiant;
        return $GLOBALS["bdd"]->query($query);
    }

    function addAdmin($identifiant,$mdp){
        $identifiant = protect($identifiant);
        $mdp = protect($mdp);
        $mdp = password_hash($mdp,PASSWORD_DEFAULT);
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO admin VALUES(?,?)");
        $query->bind_param('ss',$identifiant,$mdp);
        $query->execute();
        return true;
    }

    function supprAdmin($identifiant){
        $identifiant = protect($identifiant);
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM admin WHERE identifiant=?");
        $query->bind_param('s',$identifiant);
        $query->execute();
        $query->close();
        return true;
    }

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

    function protect($chaine){
        $protect = $GLOBALS["bdd"]->real_escape_string(stripslashes($chaine));	 
        return $protect;
    }


?>