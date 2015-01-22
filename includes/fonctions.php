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
        return true;
    }

    function modifInscrit($mail, $projection, $ancien_mail){
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections_inscrits SET  inscrit_mail=?, projection=? WHERE inscrit_mail=?");
        $mail = protect($mail);
        $projection = protect($projection);
        $ancien_mail = protect($ancien_mail);
        $query->bind_param('sss', $mail, $prenom, $ancien_mail);
        $query->execute();
        return true;
    }

    function supprInscrit($mail,$projection){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM projections_inscrits WHERE inscrit_mail=? and projection=?");
        $mail = protect($mail);
        $projection = protect($projection);
        $query->bind_param('ss', $mail,$projection);
        $query->execute();
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
        $lots = protect($lots);
        $date_emprunt = protect($date_emprunt);
        $date_retour = protect($date_retour);
        $query2 = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits_lots VALUES (?, ?, ?, ?)");
        $query2->bind_param('ssss', $mail,$lots,$date_emprunt,$date_retour);
        $query2->execute();
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
        return true;
    }

    function supprEmprunt($mail){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM inscrits_lots WHERE inscrit_mail=?");
        $mail = protect($mail);
        $query->bind_param('s',$mail);
        $query->execute();
        return true;
    }

    function protect($chaine){
        $protect = $GLOBALS["bdd"]->real_escape_string(stripslashes($chaine));	 
        return $protect;
    }


?>