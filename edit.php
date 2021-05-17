<?php 
	//TODO: check if the user navigating to the edit page has the same ID as the owner of the entry
	//TODO: move the validate position/education/profile code into functions.php
	// load necessary files
	session_start();
	require_once "pdo.php";
	require_once "style.php";

	//Restrict access
	if (!isset($_SESSION["user_id"])) {
		die("Access prohibited");
	}

	$message = "";
	$profile_id = $_GET["profile_id"];

	//Load existing data for Profile
	$profile_pdo = $pdo -> prepare("SELECT * FROM profile WHERE profile_id = :pid");
	$profile_pdo -> execute(array(":pid" => $profile_id));
	$profile = $profile_pdo -> fetch(PDO::FETCH_ASSOC);

	//Load existing data for Position
	$positions_pdo = $pdo -> prepare("SELECT * FROM position WHERE profile_id  = :pid");
	$positions_pdo -> execute(array(":pid" => $profile_id));
	$positions = $positions_pdo -> fetchAll(PDO::FETCH_ASSOC);
	print_r($positions);

	//Ensure new submitted data fits requirements
	if (isset($_POST["cancel"])) {
		header("Location: index.php"); 
	} elseif(isset($_POST["submit"])){	
		$profile_id = $_GET["profile_id"];	
		try {
			if (trim($_POST["first_name"]) == false || trim($_POST["last_name"]) == false || trim($_POST["email"]) == false || trim($_POST["headline"]) == false || trim($_POST["summary"]) == false) {
				$message = "All fields are required";
				throw new Exception($message);										
				header("Location: edit.php");
				return;
			} elseif (strpos($_POST["email"], "@") == false) {
				$message = "Email address must contain @";
				throw new Exception($message);				
				header("Location: edit.php");
				return;
			} else {
				//Determine the allowed max numbers of positions the user can add
				if(empty($positions)){
					$allowed_positions = 9;
				} else {
					$allowed_positions = array_key_last($positions)+10;
				}

				//Verify new entries on the Positions forms
				for($i = 1; $i <= $allowed_positions; $i++){    					
					if(isset($_POST["year".$i]) && isset($_POST["desc".$i])){
						$year = $_POST["year".$i];
						$desc = $_POST["desc".$i];
						if (strlen($year) == 0 || strlen($desc) == 0) {
							$message = "All fields are required";
							throw new Exception($message);
							header("Location: add.php");
							return;
						} elseif(!is_numeric($year)) {
							$message = "Position year must be numeric";
							throw new Exception($message);
							header("Location: add.php");
							return;
						}
					}				
				}
				//Update Profile data
				$update_profile_pdo = $pdo->prepare("UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :hl, summary = :sm WHERE profile_id = :pid");
				$update_profile_pdo->execute(array(
					':pid' => $profile_id,
					':fn' => $_POST["first_name"],
					':ln' => $_POST["last_name"],
					':em' => $_POST["email"],
					':hl' => $_POST["headline"],
					':sm' => $_POST["summary"]
				));

				//Delete all Positions from the position table
				$delete_position_pdo = $pdo -> prepare("DELETE FROM position WHERE profile_id = :pid");
				$delete_position_pdo -> execute(array(
					":pid" => $profile_id
				));

				//Re-insert in the Position table
				$rank = 1; 				
				for($j = 1; $j <= $allowed_positions; $j++){
					if(isset($_POST["year".$j]) && isset($_POST["desc".$j])){	
						$year = $_POST["year".$j];
						$desc = $_POST["desc".$j];					
		    			$add_position_pdo = $pdo->prepare("INSERT INTO position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :desc)");
		    			$add_position_pdo ->  execute(array(
		    				":pid" => $profile_id,
		    				":rank" => $rank,
		    				":year" => $year,
		    				":desc" => $desc
		    			));
		    			$rank++;
					}		    				
				}			

				//Handle successful db update
				$message = "Profile updated";
				$_SESSION["success"] = $message;
				header("Location: index.php");
				return;
			}
		} catch (Exception $e) {
			error_log($message);
			$_SESSION["error"] = $message;
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
 		<h1>Editing profile for <?php echo($_SESSION["name"]); ?></h1>
 		<?php 
 		if (isset($_SESSION["error"])) {
 			echo("<p class='AN-error-message'>". $_SESSION["error"] ."</p>");
 			unset($_SESSION["error"]);
 		}
 		?>
 		<form method="post">
 			<label for="FirstName">First Name: </label><br>
 			<input type="text" name="first_name" id="FirstName" value="<?= htmlentities($profile['first_name']); ?>"><br>
 			<label for="LastName">Last Name: </label><br>
 			<input type="text" name="last_name" id="LastName" value="<?= htmlentities($profile['last_name']); ?>"><br>
 			<label for="Email">Email: </label><br>
 			<input type="text" name="email" id="Email" value="<?= htmlentities($profile['email']); ?>"><br>
 			<label for="Headline">Hadline: </label><br>
 			<input type="text" name="headline" id="Headline" value="<?= htmlentities($profile['headline']); ?>"><br>
 			<label for="Summary">Summary: </label><br>
 			<textarea name="summary" id="Summary"><?php echo(htmlentities($profile['summary'])); ?></textarea><br><br>
 			<br><br>
            <p>Position: <input type="submit" id="addPos" value="+"></p>
            <div id="position_fields">
            	<?php foreach ($positions as $position): ?>
	            	<div id="<?= 'position'.$position["rank"]?>">
	            		<p>Year:
	            			<input type="text" name="<?= 'year'.$position["rank"]?>" value="<?= htmlentities($position["year"])?>">
	            			<input type="button" value="-" onclick="$(this).parentsUntil('#position_fields').remove();return false;"><br>
	            			<textarea name="<?= 'desc'.$position["rank"]?>" rows="8" cols="80"><?php echo(htmlentities($position["description"])); ?></textarea>
	            		</p>
	            	</div>
	            <?php endforeach ?>
            </div>            
 			<input type="submit" name="submit" value="Save">
 			<input type="submit" name="cancel" value="Cancel">
 		</form>
 	</div>
 	<script
            src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                window.console && console.log('Document ready called');
                if ($("#position_fields").children().length > 0) {                	
                	existingPos = $("#position_fields").children().last().attr("id").slice(8);
                } else {
                	existingPos = 0;
                }
                allowedPos = parseInt(existingPos) + 9;
                console.log("allowedPos "+allowedPos);
                countPos = existingPos;
                $('#addPos').click(function(e){
                    e.preventDefault();
                    if ( countPos >= allowedPos) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position "+countPos);
                    $('#position_fields').append(
                        '<div id="position'+countPos+'"> \
                        <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                        <input type="button" value="-" \
                            onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                        <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                        </div>');
                });
            });
        </script>
</body>
</html>