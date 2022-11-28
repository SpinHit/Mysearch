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
     <!-- bouton qui redirige vers la page loginPage.php -->
     <div id="bouttonlogin">
     <button class="btn btn-primary" onclick="window.location.href='loginPage.php'"><img src="https://img.icons8.com/ios/50/000000/login-rounded-right.png"/></button>
     </div>

<!-- Logo du moteur "logo.png" -->
<h1><img src="logo.png" alt="logo" width="200" height="auto"></h1>
 
     <!--  Barre de recherche -->
     <form class='wrap' method="get" action="index.php">
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
<!-- <div class='row' style='display: inline-flex; gap: 1vw;'>
<div id='bouttonscan'>
<a href='index.php?scan=true'>Scan</a>
</div>
<div id='bouttonscan'>
<a href='index.php?scanUrls=true'>ScanUrls</a>
</div>
</div> -->


<center/>

<style>
<?php include 'style.css'; ?>
</style>