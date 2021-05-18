<?php
	session_start();
	require_once "pdo.php";
	require_once "style.php";

	if (!isset($_SESSION["user_id"])) {
		die("Access denied");
	}

	if ( ! isset($_GET['profile_id']) ) {
	  $_SESSION['error'] = "Missing profile_id";
	  header('Location: index.php');
	  return;
	} else {
		$profile_id = $_GET["profile_id"];
	}	

	//get existing data
	$profile_pdo = $pdo -> prepare("SELECT * FROM profile WHERE profile_id = :pid");
	$profile_pdo -> execute(array(":pid" => $profile_id));
	$profile = $profile_pdo -> fetch(PDO::FETCH_ASSOC);

	//delete data
	if (isset($_POST["delete"])) {
		$delete_pdo = $pdo -> prepare("DELETE FROM profile WHERE profile_id = :pid");
		$delete_pdo -> execute(array(":pid" => $profile_id));

		$_SESSION["success"] = "Profile deleted";
		header("Location: index.php");
	}

	//cancel action
	if (isset($_POST["cancel"])) {
		header("Location: index.php");
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
		<h1>Deleting Profile</h1>
		<p><b>First Name: </b><?php echo($profile["first_name"]) ?></p>
		<p><b>Last Name: </b><?php echo($profile["last_name"]) ?></p>
		<form method="post">
			<input type="hidden" name="profile_id" value="$profile_id">
			<input type="submit" name="delete" value="Delete" onclick="confirm('Are you sure?');"> 
			<input type="submit" name="cancel" value="Cancel">
		</form>
	</div>
</body>
</html>