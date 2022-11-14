<?php
require('connexion.php');


//
set_time_limit (500);
$path= "documents";


// code pour en faire en sorte d'avoir toute la fonction dans un bouton .
  if (isset($_GET['scan'])) {
	?></center>
	<?php
    explorerDir($pdo,$path);
	?>
	<center><?php
}

function explorerDir($pdo,$path)

{
	// ouvre le dossier
	$folder = opendir($path);
	
	//tant qu'on lis les entrées du fichier
	while($entree = readdir($folder))
	{		
		// si la variable $entree est différente de . et ..
		if($entree != "." && $entree != "..")
		{
			// si un dossier est présent
			if(is_dir($path."/".$entree))
			{
				// met le chemin courant dans une variable
				$sav_path = $path;
				// met le chemin du dossier qu'il a trouvé dans une variable
				$path .= "/".$entree;
                
				// fait un appel récursive avec le nouveau chemin (il va explorer le nouveau dossier trouver)		
				explorerDir($pdo,$path);
				// met l'ancien chemin courant afin de continué à le parcourir
				$path = $sav_path;

			}
			else
			{
				//switch pour afficher un logo en fonction de l'extention
				$extention = recupExtention($entree);
				// c'est le chemin entier de l'entrée + le nom de l'entrée
				$path_source = $path."/".$entree;	
                    
                $extensionValide = array("html");
                $tmp = explode(".", $entree);
                $type = end($tmp);
                // on regarde si l'entrée est une image  et on lance la fonction pour mettre l'image dans le dossier local et mettre les informations dans la bdd .
                if (in_array($type,$extensionValide)){

					
					// si le fichier a déjà été indexé on ne le réindexe pas
					$sql = "SELECT * FROM tableurl WHERE url = :url";
					$req = $pdo->prepare($sql);
					$req->execute(array(
						'url' => $path_source
					));
					$result = $req->fetch();
					if($result == false){
						// on indexe le fichier
						indexeUrlEtFichier($pdo,$path_source);
					}
					


					
                }

			}
		}
	}
    ?></div><?php
	closedir($folder);  
}
?>