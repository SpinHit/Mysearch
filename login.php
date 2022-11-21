<!-- le formulaire est caché par defaut et quand on clique sur l'image de login il s'affiche -->
<form method="post" action="">
        <div id="div_login">
            <h1>Connexion</h1>
            <div>
                <input type="text" class="textbox" id="user" name="user" placeholder="Pseudo" />
            </div>
            <div>
                <input type="password" class="textbox" id="pass" name="pass" placeholder="Mot de passe"/>
            </div>
            <div>
                <input type="submit" value="Submit" name="envoyer" id="envoyer" />
            </div>
        </div>
    </form>

<!-- on créer un un bouton pour afficher le formulaire de connexion et quand on






<?php

require('connexion.php');

$user = (isset($_POST["user"]) ? $_POST["user"] : null);
$pass = (isset($_POST["pass"]) ? $_POST["pass"] : null);

if(isset($_POST["user"]) && isset($_POST["pass"]))
{
		
	try
	{

		//sql 
		$stmt = $pdo->prepare("SELECT USERNAME, PASSWORD FROM USERS");

		//on execute
		$stmt->execute();
		
		//fetch
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		print_r($result);
		
		// on enregistre les infos
		$u = $result['USERNAME'];
		$p = $result['PASSWORD'];
		// si le user et le mdp correspondent alors on va dans le admin.php
		if($user == $u && $pass == $p)
		{
			$_SESSION['SESS_MEMBER_USER'] = $u;
			$_SESSION['SESS_MEMBER_PASS'] = $p;
			session_write_close();
			header("location: admin.php");
			exit();
		}
		else
		{ 
			// si il n'y apas le bon mdp on reviens a la case depart
			session_write_close();
			header("location: index.php");
			exit();
		}
		
	}
    catch(PDOException $e)
	{
		echo "Connection failed : " . $e->getMessage();
	}
}


?>

<style>

#login {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	width: 300px;
	height: 300px;
	background: #fff;
	border-radius: 5px;
	box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
	display: none;
}

#login h1 {
	text-align: center;
	color: #333;
	font-size: 30px;
	padding: 0;
	margin: 0;
	line-height: 60px;
}

#login form {
	padding: 20px 30px;
}

#login .textbox {
	width: 100%;
	overflow: hidden;
	font-size: 20px;
	padding: 8px 0;
	margin: 8px 0;
	border-bottom: 1px solid #4caf50;
}

#login input[type="submit"] {
	width: 100%;
	background: none;
	border: 2px solid #4caf50;
	color: #4caf50;
	padding: 5px;
	font-size: 18px;
	cursor: pointer;
	margin: 12px 0;
}

#login input[type="submit"]:hover {
	background: #4caf50;
	color: #fff;
}



</style>