<!-- script pour afficher les icones -->
<script src="https://kit.fontawesome.com/45e38e596f.js" crossorigin="anonymous"></script>
<meta http-equiv="content-type" content="text/html" charset="utf-8" />

<?php
//require('connexion.php');

require('fonctions.php');
$pdo = new PDO($dsn, $dbusername, $dbpassword);
require('scan.php');
require('scanUrl.php');




?>

<!-- Partie HTML-->

<center>

<!-- on créer un un bouton cliquable pour se déconnecter et revenir vers la apge index.php -->
<button class="btn btn-primary" onclick="window.location.href='index.php'">Déconnexion</button>


<!-- on va créer une fenetre back office pour pouvoir modifier les données -->
<?php
backoffice($pdo);
?>

<div class='row' style='display: inline-flex; gap: 1vw;'>
<div id='bouttonscan'>
<a href='admin.php?scan=true'>Scan</a>
</div>
<div id='bouttonscan'>
<a href='admin.php?scanUrls=true'>ScanUrls</a>
</div>
</div> 
<br>

<div class="container">
 
     <!--  Barre de recherche -->
     <form class='wrap' method="get" action="admin.php">
          <div class="search">
     <input type="text" class="searchTerm" name="recherche" placeholder="Que cherchez vous ?">
     <input class="searchButton" type="submit" name="submit" value="Rechercher">
     </div>
     </form>
     <br>
     

    
    
<div class='affichageresultat'> 
<?php

//on recupere la valeur de la recherche
if (isset($_GET['recherche'])){
     // on la fonction de recherche
     $recherche = $_GET['recherche'];
     recherche($recherche,$pdo);
}

?>

</div>


<br>



<center/>

<style>
<?php include 'style.css'; ?>
</style>