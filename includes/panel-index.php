<?php

echo '<div id="main-nav-hold">
            <nav class="main-nav">
						<a href="index.php" class="nav-btn">Présentation du club</a>
						<a href="views/cine.php" class="nav-btn">Coté Ciné de l\'ISEN</a>
                        <a href="views/emprunt.php" class="nav-btn">Emprunt de matériel</a>
                        <button type="button" class="btn btn-danger" aria-label="Left Align">
  <span class="glyphicon glyphicon-off" aria-hidden="true"></span></button>
                        <a href="views/admin.php" class="nav-btn">Espace Administrateur</a>
                        <a href="views/calendrier.php" class="nav-btn">Calendrier des emprunts</a>
                        <a href="views/projection.php" class="nav-btn">Espace liste de projection</a>
                        <div class="menu-btn" onClick="showMenu()"><a><span></span><span></span><span></span></a></div>

		    </nav>
        </div>';

?>
