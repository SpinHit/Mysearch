<!-- script pour afficher les icones -->
<script src="https://kit.fontawesome.com/45e38e596f.js" crossorigin="anonymous"></script>
<?php
require('connexion.php');
require('fonctions.php');
$pdo = new PDO($dsn, $dbusername, $dbpassword);



//si il y a un fichier dans le formulaire
if(isset($_FILES["mon_fichier"])){
$pname = $_FILES['mon_fichier']['name'];
$psize = $_FILES['mon_fichier']['size'];

$fp = fopen($_FILES['mon_fichier']['tmp_name'], 'r');
// on lit le fichier
$contenu = fread($fp, filesize($_FILES['mon_fichier']['tmp_name']));
// on ferme le fichier
fclose($fp);
// on filtre les phrases du fichier
$contenuClean = Filtre($contenu);
}

// compter les redondances de chaque mot et les mettre dans un tableau avec le nombre de redondances et le mot sans doublons

// on lance la fonction en cliquant sur le bouton bouttonscan et on affiche le resultat




?>



<!-- Partie HTML-->


<center>
<h1>Gestion d'images</h1><br/>
 
 <form method="post" enctype="multipart/form-data">
      <label for="mon_fichier">choisir un document a téléversser :</label>
      <input type="file" name="mon_fichier" id="mon_fichier" accept="images/pdf, images/txt, images/word" />
      <input type="submit" name="submit" value="Envoyer" />
 </form>

 

<!-- on va afficher image par image et l'id avec-->
<div id="listePagination">
        <h1>Le fichier <?php echo $pname ?> a etait ouvert et les mots :<?php 
        //on va afficher le resultat de la fonction count_values et les ajouter dans la bdd
        $result = count_values($contenuClean);
        foreach($result as $mot => $nb){
            echo $mot." ".$nb." ";
            $sql = "INSERT into tablemots(mot, redondance, nom_du_fichier) VALUES ('$mot','$nb','$pname')";
            $pdo->query($sql) ;
        }


        ?> ont etait transférer dans la base de donnée avec succes</h1>
    
    </div>
    

    <!-- création des boutons en fonction du nombre de page-->
    <div id="pagination">

    </div>


<div id='bouttonscan'>
<a href='index.php?scan=true'>Scan</a>
</div>


<center/>

<style>
<?php include 'style.css'; ?>
</style>