<?php 
	session_start();
	require_once "pdo.php";
	require_once "style.php";

	$profile_id = $_GET["profile_id"];

	//handle the profile information
	$profile_pdo = $pdo -> prepare("SELECT * FROM profile WHERE profile_id = :pid");
	$profile_pdo -> execute(array(":pid" => $profile_id));
	$profile = $profile_pdo -> fetch(PDO::FETCH_ASSOC);

	//handle the position information
	$positions_pdo = $pdo -> prepare("SELECT * FROM position WHERE profile_id = :pid");
	$positions_pdo -> execute(array(
		":pid" => $profile_id
	));
	$positions = $positions_pdo->fetchAll(PDO::FETCH_ASSOC);
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
 		<h1>Profile information</h1>
 		<p><b>First Name:</b> <?php echo(htmlentities($profile["first_name"])); ?></p>
 		<p><b>Last Name:</b> <?php echo(htmlentities($profile["last_name"])); ?></p>
 		<p><b>Email:</b> <?php echo(htmlentities($profile["email"])); ?></p>
 		<p><b>Headline:</b> <?php echo(htmlentities($profile["headline"])); ?></p>
 		<p><b>Summary:</b> <?php echo(htmlentities($profile["summary"])); ?></p>
 		<?php 
 		foreach ($positions as $position) {
 			echo("<p>".htmlentities($position["year"])." ". htmlentities($position["description"]) . "</p>");
 		}
 		?>
 		<a href="index.php">Done</a>
 	</div>
 </body>
 </html>