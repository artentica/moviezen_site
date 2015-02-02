<?php

switch (basename($_SERVER['PHP_SELF'])) {
    case 'index.php':
        $page = "Présentation du club";
        break;
    case 'cine.php':
        $page = "Coté Ciné de l'ISEN";
        break;
    case 'emprunt.php':
        $page = "Emprunt de matériel";
        break;
    case 'admin.php':
        $page = "Espace Administrateur";
        break;
    case 'calendrier.php':
        $page = "Calendrier des emprunts";
        break;
    case 'projection.php':
        $page = "Espace liste de projection";
        break;
    default:
}

echo '<div id="main-nav-hold">
            <nav class="main-nav">
						<a href="../index.php" class="nav-btn">Présentation du club</a>
						<a href="cine.php" class="nav-btn">Coté Ciné de l\'ISEN</a>
                        <a href="emprunt.php" class="nav-btn">Emprunt de matériel</a>';

    echo '<span id="title_page_menu">'.$page.'</span>';

if(!empty($_SESSION["authentifie"])){
    echo'<a  title="Deconnexion button" type="button" class="btn" id="decobtn" href="?deco=1"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a>';
}




                        echo'<a href="admin.php" class="nav-btn">Espace Administrateur</a>
                        <a href="calendrier.php" class="nav-btn">Calendrier des emprunts</a>
                        <a href="projection.php" class="nav-btn">Espace liste de projection</a>
                        <div class="menu-btn" onClick="showMenu()"><a><span></span><span></span><span></span></a></div>

		    </nav>
        </div>';





?>
