<?php
require('connexion.php');


//
set_time_limit (500);
$path= "documents";

// code pour en faire en sorte d'avoir toute la fonction dans un bouton .
  if (isset($_GET['scanUrls'])) {
	?></center>
	<?php
    scanurls($pdo,$path);
	?>
	<center><?php
}

// fonction qui va ouvrir le fichier urls.txt et le parcourir pour en extraire ligne par ligne les urls et les lancer dans la fonction workUrl
 function scanurls($pdo,$path){
    $file = fopen($path."/urls.txt", "r");
    while(!feof($file)) {
        $line = fgets($file);
        workUrl($pdo,$line);
    }
    fclose($file);
} 

// fonction ou on va mettre les urls dans la bdd sans avoir de doublons
function workUrl($pdo,$url){
    $sql = "SELECT * FROM tableurl WHERE url = :url";
    $req = $pdo->prepare($sql);
    $req->execute(array(
        'url' => $url
    ));
    $result = $req->fetch();
    if($result == false){   
        indexeUrlEtFichier($pdo,$url);


    }
}


// la fonction indexeetaffiche va prendre en parametre l'url et le contenu de l'url et va l'indexer dans la bdd mot par mot en indexant le titre avec la fonction getMetaTitle et en indexant la de de la description avec la fonction getMetaDescription et en indexant les keywords avec la fonction getMetaKeywords


?>



 