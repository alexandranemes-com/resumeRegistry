<?php
  //TODO: move the validate position/education/profile code into functions.php
  session_start();
  require_once "pdo.php";
  require_once "style.php";
  require_once "functions.php";

  $message = "";

  if (isset($_POST["cancel"])) {
      header("Location: index.php");
  } elseif(isset($_POST["add"])){
      try {
          if (trim($_POST["name"]) == false || trim($_POST["email"]) == false || trim($_POST["password"]) == false) {
              $message = "All fields are required";
              throw new Exception($message);
              header("Location: register.php");
              return;
          } elseif (strpos($_POST["email"], "@") == false) {
              $message = "Email address must contain @";
              throw new Exception($message);
              header("Location: register.php");
              return;
          } else {
              //Hash the password
              $salt = "XyZzy12*_";
              $email = $_POST["email"];
              $password = hash("md5", $salt . $_POST["password"]);
              // Handle database insert on the Profile table
              $add_profile_pdo = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:nm, :em, :pw)");
              $add_profile_pdo->execute(array(
                  ':nm' => $_POST["name"],
                  ':em' => $_POST["email"],
                  ':pw' => $password
              ));
              //Handle successful addition of data
              $message = "User created";
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
      <h1>Create your username and password:</h1>
      <?php flashMessages(); ?>
      <form method="post">
        <label for="FirstName">Username: </label><br>
        <input type="text" name="name" id="Name"><br>
        <label for="Email">Email: </label><br>
        <input type="text" name="email" id="Email"><br>
        <label for="Password">Password: </label><br>
        <input type="text" name="password" id="Password"><br>
        <br><br>
        <input type="submit" name="add" value="Add">
        <input type="submit" name="cancel" value="Cancel">
      </form>
    </div>
    <script
      src="https://code.jquery.com/jquery-3.6.0.min.js"
      integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
      crossorigin="anonymous"></script>
    <!-- <script type="text/javascript">
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
      </script> -->
  </body>
</html>