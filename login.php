<?php
	session_start();
	require_once "pdo.php";
	require_once "style.php";

	$message = "";

	if (isset($_POST["cancel"])) {
		header("Location: index.php");
	}elseif (isset($_POST["login"])) {
		if (trim($_POST["email"]) == false || trim($_POST["pass"]) == false) {
			$message = "Both fields must be filled out";
			$_SESSION["error"] = $message;
			error_log($message);
			header("Location: login.php");
			return;
		}elseif (strpos($_POST["email"], "@") == false) {
			$message = "Invalid email";
			$_SESSION["error"] = $message;
			error_log($message);
			header("Location: login.php");
			return;
		} else{
			$salt = "XyZzy12*_";
			$email = $_POST["email"];
			$password = hash("md5", $salt . $_POST["pass"]);
			try {
				$login_pdo = $pdo->prepare("SELECT user_id, name FROM users WHERE email = :em AND password = :pw");
				$login_pdo -> execute(array(
					":em" => $email,
					":pw" => $password
				));
				$user = $login_pdo->fetch(PDO::FETCH_ASSOC);

				if ($user == true) {
					$_SESSION['name'] = $user['name'];
				    $_SESSION['user_id'] = $user['user_id'];
				    header("Location: index.php");
				    return;				
				}else{
					$message = "Incorrect password";
					$_SESSION["error"] = $message;			
					header("Location: login.php");
					return;
				}
			} catch (Exception $e) {			
				error_log($message ." for email ". $email);
				unset($_SESSION["error"]);
			}
		}
				
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Alexandra RR</title>
</head>
<body>
	<div class="container">
	 	<?php include "header.php"; ?>
		<h1>Please log in</h1>
		<!-- display back-end error messages -->
		<?php 
		if (isset($_SESSION["error"])) {
			echo("<p class='AN-error-message'>". $_SESSION["error"] ."</p>");
			unset($_SESSION["error"]);
		}
		?>

		<form method="post">
			<label for="email">Email</label>
			<input type="text" name="email" id="email">
			<label for="password">Password</label>
			<input type="text" name="pass" id="password">
			<input type="submit" onclick="return doValidate();" name="login" value="Log In">
			<input type="submit" name="cancel" value="Cancel">
		</form>
		<p clas="text-muted">For a user/password hint view source code</p>
		<!-- User: umsi@umich.edu -->
		<!-- Password: php123 -->
	</div>
	<script type="text/javascript">
		function doValidate(){
		   console.log('Validating...');
	        try {
	            pw = document.getElementById('password').value;
	            em = document.getElementById('email').value;

	            console.log("Validating pw="+pw);

	            if (pw == null || pw == "" || em == null || em == "") {
	                alert("Both fields must be filled out");
	                return false;
	            }else if(em.includes("@") === false){
	            	alert("Invalid email");
	            	return false;
	            }
	            return true;

	        } catch(e) {
	            return false;
	        }
	        return false;
		}
	</script>
</body>
</html>