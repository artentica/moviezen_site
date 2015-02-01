<?php

echo '<div id="main-nav-hold">
            <nav class="main-nav">
						<a href="../index.php" class="nav-btn">Présentation du club</a>
						<a href="cine.php" class="nav-btn">Coté Ciné de l\'ISEN</a>
                        <a href="emprunt.php" class="nav-btn">Emprunt de matériel</a>';



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
