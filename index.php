<?php 
	// Open session, load required files
	session_start();
	require_once "pdo.php";
	require_once "style.php";
	require_once "functions.php";

	// Declare required variables
	$logged_in = false;
	$empty_db = true;

	// Query database to check if empty
	$count_db_sql = $pdo -> prepare("SELECT COUNT(*) FROM profile");
	$count_db_sql -> execute();

	// Check if the user is authenticated
	if (isset($_SESSION["user_id"])) {
		$logged_in = true;
	}

	if ($count_db_sql > "0") {
		$empty_db = false;

		//Query database if user is authenticated
		if ($logged_in == true) {
			$view_db_pdo = $pdo -> prepare("SELECT * FROM profile ORDER BY last_name, :user_id");
			$view_db_pdo -> bindParam(":user_id", $_SESSION["user_id"]);
			$view_db_pdo -> execute();
			$rows = $view_db_pdo -> fetchAll(PDO::FETCH_ASSOC);
		} else {//Query database if user is not authenticated
			$view_db_pdo = $pdo -> prepare("SELECT * FROM profile ORDER BY last_name");
			$view_db_pdo -> execute();
			$rows = $view_db_pdo -> fetchAll(PDO::FETCH_ASSOC);
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
 		
	 	<!-- Display options for authenticated users -->
	 	<?php if ($logged_in == true): ?>
	 		<p class="AN-subtitle"><a href="logout.php">Logout</a> | <a href="add.php">Add New Entry</a></p>
	 	<?php endif ?>

 		<!-- Display options for unauthenticated users -->
	 	<?php if ($logged_in == false): ?>
	 		<p class="AN-subtitle"><a href="login.php">Please log in</a> | <a href="register.php">Create account</a></p>
	 	<?php endif ?>

	 	<!-- Print flash messages -->
	 	<?php flashMessages(); ?>
	 	<?php 
	 		if (isset($_SESSION["test"])) {
	 			print("<p>".$_SESSION["test"]."</p>");
	 			unset($_SESSION["test"]);
	 		}
	 	?>
	 	
	 	<!-- Display message if no data in database -->
	 	<?php if ($empty_db == true): ?>
	 		<p>No profiles</p>
	 	<?php endif ?>

	 	<!-- Display profiles data if there is any -->
	 	<?php if ($empty_db == false): ?>
	 	<table class="table">
	 		<thead>
	 			<th>Name</th>
	 			<th>Headline</th>
	 			<th>Actions</th>
	 		</thead> 		
	 		<tbody>
	 			<?php foreach ($rows as $row): ?>
				<tr>
					<td>
						<?php echo("<a href=view.php?profile_id=".htmlentities($row["profile_id"]).">".htmlentities($row['first_name'])." ".htmlentities($row['last_name'])."</a>"); ?>
					</td>
					<td><?php echo(htmlentities($row['headline'])); ?></td>
					<td>
						<?php 
						if ($logged_in == true && $row['user_id'] == $_SESSION["user_id"]) {
							echo("<a href=edit.php?profile_id=". $row['profile_id'] . ">Edit</a>");
							echo(" / <a href=delete.php?profile_id=". $row['profile_id'] . ">Delete</a>");
						} else {
							echo "No actions available";
						}
						?>
					</td>
				</tr>
	 			<?php endforeach ?>
	 		</tbody>
	 	</table>
	 	<?php endif ?>
 	</div>
 </body>
 </html>