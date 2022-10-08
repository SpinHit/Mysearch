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
                    
                $extensionValide = array("txt");
                $tmp = explode(".", $entree);
                $type = end($tmp);
                // on regarde si l'entrée est une image  et on lance la fonction pour mettre l'image dans le dossier local et mettre les informations dans la bdd .
                if (in_array($type,$extensionValide)){
					//fonction permettant de transferer toutes les images du fichier docs a la base de donnée et les verssée dans le dossier image
                    //echo " <p> <i class='far fa-image'></i> ".$path_source."</p><br>";
                    $fp = fopen($path_source, 'r');
                        // on lit le fichier
                    $contenu = fread($fp, filesize($path_source));
                        // on ferme le fichier
                    fclose($fp);
                        // on filtre les phrases du fichier
                    $contenuClean = Filtre($contenu);

                    indexeetaffiche($entree,$contenuClean,$pdo);
                    
                  //insereBddDossierCopy($pdo,$entree,filesize($path_source),"images/",$path_source);
                }

			}
		}
	}
    ?></div><?php
	closedir($folder);  
}
?>