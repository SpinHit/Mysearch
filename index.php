<!-- script pour afficher les icones -->
<script src="https://kit.fontawesome.com/45e38e596f.js" crossorigin="anonymous"></script>
<meta http-equiv="content-type" content="text/html" charset="utf-8" />

<?php
//require('connexion.php');

require('fonctions.php');
$pdo = new PDO($dsn, $dbusername, $dbpassword);
require('scan.php');

//$pdo = new PDO($dsn, $dbusername, $dbpassword);


// compter les redondances de chaque mot et les mettre dans un tableau avec le nombre de redondances et le mot sans doublons

// on lance la fonction en cliquant sur le bouton bouttonscan et on affiche le resultat




 

?>




<!-- Partie HTML-->


<center>
<h1>Moteur de recherche</h1>
 
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
// on transforme le tableau en chaîne de caractère et on filtre les mots avec la fonction filtre
// on regle l'encodage pour les accents en utf8


/* $chaine = implode(" ",openhtml('https://www.lesnumeriques.com/'));
//echo $chaine;
  $chaine = filtre($chaine); 
// on affiche la liste des mots
 foreach($chaine as $key => $value){
     echo $value.'<br>';
}   */

 






?>

</div>


<br>
<div id='bouttonscan'>
<a href='index.php?scan=true'>Scan</a>
</div>


<center/>

<style>
<?php include 'style.css'; ?>
</style>