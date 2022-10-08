<?php

require('connexion.php');

/* // fonction pour téléversser l'image
 function insereBddDossier($pdo,$pname,$psize,$dest){
    $sql = "INSERT into images (file_name, size , chemin) VALUES ('$pname','$psize','$dest')";
    
     // dossier ou l'on va insserer les images 
    
     
    if ($_FILES['mon_fichier']['error'] > 0) $erreur = "Erreur lors du transfert";
     // upload de l'image dans le dossier 
    $resultat = move_uploaded_file($_FILES['mon_fichier']['tmp_name'],$dest.$_FILES['mon_fichier']['name']);
    
    if ($resultat) $pdo->query($sql) ; echo "Transfert réussi"; header("Refresh:0");
    } */

    // fonction permettant de récupérer l'extention d'un fichier
    // liste des mots vide
       

    function recupExtention($fname) {
        return substr(strrchr($fname,'.'),1);
        }





            function count_values($array) {
                $result = array();
               
                foreach ($array as $value) {
                    if (isset($result[$value])) {
                        $result[$value]++;
                    } else {
                        $result[$value] = 1;
                    }
                }
                return $result;
            }

            function Filtre($contenu){
                $ListeMot_vides = ["de","mais","un","il","à","le","et","la","les","des","en","du","une","est","ce","qui","par","sur","pas","plus","se","aux","pour","dans","ce","ne","vous","que","avec","son","sa","leur","soit","comme","tout","cette","cet","ces","ses","ceux","celui","celle","elles","ils","on","ont","nous","vous","y","a","d","l","m","n","s","t","j","c","ç","b","p","f","v","h","k","q","g","x","z","w","r","é","è","ê","à","â","î","ô","û","ù","ç","ë","ï","ü","ÿ","œ","æ","-","_",".",",",";","!","?","(",")","[","]","{","}","/","\\","|","&","*","^","%","$","£","€","@","~","#","0","1","2","3","4","5","6","7","8","9","=","+","<",">","'","\"","`","¨","°","§","µ","¤","¶","•","‹","›","«","»","€","™","®","©","¢","¥","–","—","…","‡","°","·","‚","‘","’","“","”","„","†","‡","•","…","‰","‹","›","€","™","®","©","¢","¥","–","—","…","‡","°","·","‚","‘","’","“","”","„","†","‡","•","…","‰","‹","›","€","™","®","©","¢","¥","–","—","…","‡","°","·","‚","‘","’","“","”","„","†","‡","•","…","‰","‹","›","€","™","®","©","¢","¥","–","—","…","‡","°","·","‚","‘","’","“","”","„","†","‡","•","…","‰","‹","›","€","™","®","©","lui"];
                $contenu = strtolower($contenu);

                // on filtre les phrases du fichier
                $contenu = preg_replace('/[^a-zA-Z0-9àâäéèêëîïôöùûüçÀÂÄÉÈÊËÎÏÔÖÙÛÜÇ ]/', ' ', $contenu);
                // on met le contenu dans un tableau
                $contenu = explode(" ", $contenu);
                // on enleve les mots de moins de 3 charactères et on compte les redondances et que le mot n'est pas dans la liste des mots vides
                foreach ($contenu as $key => $value) {
                    if (strlen($value) < 3 || in_array($value, $ListeMot_vides)) {
                        unset($contenu[$key]);
                    }
                
                
                /*     // on compte les redondances de chaque mot dans le tableau $contenu et on les met dans un tableau $mots 
                    $mots[$value] = (isset($mots[$value])) ? $mots[$value] + 1 : 1; */
                }
                return $contenu;
                
                /*     // on insere les mots dans la bdd
                    $sql = "INSERT into mots (mot, nb) VALUES ('$key','$value')";
                    $pdo->query($sql) ; */
                
                
                }

                            // on met dans une fonction 
            function indexeetaffiche($pname,$contenuClean,$pdo){
                
                //on va afficher le resultat de la fonction count_values et les ajouter dans la bdd
                $result = count_values($contenuClean);
                foreach($result as $mot => $nb){
                    // echo $mot." ".$nb." ".'<br>';
                    $sql = "INSERT into tablemots(mot, redondance, nom_du_fichier) VALUES ('$mot','$nb','$pname')";
                    $pdo->query($sql) ;
                }
                echo'<div id="listePagination">'.'Le fichier '. $pname." a etait ouvert et etait transférer dans la base de donnée avec succes".'</div>' ;
                }

            // on retourne les mots les plus redondants d'un nom de fichier donné
            function motredondant($nom_fichier,$pdo){
                // on récupère le mot dans la bdd par rapport à la recherche par ordre de redondance le plus grand au plus petit
                $sql = "SELECT * FROM tablemots WHERE nom_du_fichier = '$nom_fichier' ORDER BY redondance DESC";
                $result = $pdo->query($sql);
                $result = $result->fetchAll();

                return "[".$result[0]['redondance']."]".$result[0]['mot']." "."[".$result[1]['redondance']."]".$result[1]['mot']." "."[".$result[2]['redondance']."]".$result[2]['mot']." "."[".$result[3]['redondance']."]".$result[3]['mot']." "."[".$result[4]['redondance']."]".$result[4]['mot'];
            }

            // fonction permettant de chercher un mot dans la bdd et de l'afficher avec le nombre de redondance et le nom du fichier
            function recherche($mot,$pdo){
                // on récupère le mot dans la bdd par rapport à la recherche par ordre de redondance le plus grand au plus petit
                $sql = "SELECT * FROM tablemots WHERE mot LIKE '%$mot%' ORDER BY redondance DESC";
                $result = $pdo->query($sql);
                $result = $result->fetchAll();
                // on affiche le resultat de la recherche trier par le nombre de redondance le plus grand
                ?> 
                
                    <div class="tbl-header">
                    <table cellpadding="0" cellspacing="0" border="0">
                    <thead>
                        <tr>
                        <th>Mot</th>
                        <th>Nombre de redondance</th>
                        <th>Nom du fichier</th>
                        </tr>
                    </thead>
                    </table>
                </div>
                <div class="tbl-content">
                    <table cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                
                
                
                <?php
                foreach($result as $key => $value){
                    echo '<tr>';
                    echo '<td>'.$value['mot'].'</td>';
                    echo '<td>'.$value['redondance'].'</td>';
                    // on affiche le nom du fichier en cliquable et le nuage de mots
                    echo '<td>'.'<a href="#">'.$value['nom_du_fichier'].'<div class="tooltipcontainer">'.'<div class="tooltip">'.motredondant($value['nom_du_fichier'],$pdo).'</div>'.'</div>'.'</a>'.'</td>';
                    echo '</tr>';
                }

                // si le mot n'est pas dans la bdd on affiche un message d'erreur
                if(empty($result)){
                    echo "Nous n'avons pas trouvé le mot recherché";
                }
                ?>
                
                </tbody>
                </table>
                </div>
                
                
                <?php

            }




            
                
    

?>