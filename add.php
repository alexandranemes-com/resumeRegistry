<?php
  //TODO: move the validate position/education/profile code into functions.php
  session_start();
  require_once "pdo.php";
  require_once "style.php";
  require_once "functions.php";

  if (!isset($_SESSION["user_id"])) {
      die("Access prohibited");
  }

  $message = "";

  if (isset($_POST["cancel"])) {
      header("Location: index.php");
  } elseif(isset($_POST["add"])){
      try {
          if (trim($_POST["first_name"]) == false || trim($_POST["last_name"]) == false || trim($_POST["email"]) == false || trim($_POST["headline"]) == false || trim($_POST["summary"]) == false) {
              $message = "All fields are required";
              throw new Exception($message);
              header("Location: add.php");
              return;
          } elseif (strpos($_POST["email"], "@") == false) {
              $message = "Email address must contain @";
              throw new Exception($message);
              header("Location: add.php");
              return;
          } else {
              //Verify new entries on the Positions data
              for($i = 1; $i <= 9; $i++){
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
              // Handle database insert on the Profile table
              $add_profile_pdo = $pdo->prepare("INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES (:pid, :fn, :ln, :em, :hl, :sm)");
              $add_profile_pdo->execute(array(
                  ':pid' => $_SESSION["user_id"],
                  ':fn' => $_POST["first_name"],
                  ':ln' => $_POST["last_name"],
                  ':em' => $_POST["email"],
                  ':hl' => $_POST["headline"],
                  ':sm' => $_POST["summary"]
              ));

              //Handle database insert on the Position table
              $rank = 1;
              $profile_id = $pdo->lastInsertId();
              for($j = 1; $j <= 9; $j++){
                  if(isset($_POST["year".$j]) && isset($_POST["desc".$j])){
                      $year = $_POST["year".$j];
                      $desc = $_POST["desc".$j];
                      $position_pdo = $pdo->prepare("INSERT INTO position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :desc)");
                      $position_pdo ->  execute(array(
                          ":pid" => $profile_id,
                          ":rank" => $rank,
                          ":year" => $year,
                          ":desc" => $desc
                      ));
                      $rank++;
                  }
              }

              //Handle successful addition of data
              $message = "New record added";
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
    <title>Alexandra Nemes</title>
  </head>
  <body>
    <div class="container">
      <h1>Adding profile for <?php echo($_SESSION["name"]); ?></h1>
      <?php flashMessages(); ?>
      <form method="post">
        <label for="FirstName">First Name: </label><br>
        <input type="text" name="first_name" id="FirstName"><br>
        <label for="LastName">Last Name: </label><br>
        <input type="text" name="last_name" id="LastName"><br>
        <label for="Email">Email: </label><br>
        <input type="text" name="email" id="Email"><br>
        <label for="Headline">Headline: </label><br>
        <input type="text" name="headline" id="Headline"><br>
        <label for="Summary">Summary: </label><br>
        <textarea name="summary" id="Summary"></textarea>
        <br><br>
        <p>Position: <input type="submit" id="addPos" value="+"></p>
        <div id="position_fields"></div>
        <input type="submit" name="add" value="Add">
        <input type="submit" name="cancel" value="Cancel">
      </form>
    </div>
    <script
      src="https://code.jquery.com/jquery-3.6.0.min.js"
      integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
      crossorigin="anonymous"></script>
    <script type="text/javascript">
      countPos = 0;
      $(document).ready(function(){
          window.console && console.log('Document ready called');
          $('#addPos').click(function(e){
              e.preventDefault();
              if ( countPos >= 9 ) {
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