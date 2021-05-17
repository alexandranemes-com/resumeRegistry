<?php

function flashMessages(){
	if (isset($_SESSION["error"])) {
		echo("<p class='AN-error-message'>". $_SESSION["error"] ."</p>");
		unset($_SESSION["error"]);
	} elseif(isset($_SESSION["success"])){
		echo("<p class='AN-success-message'>". $_SESSION["success"] ."</p>");
		unset($_SESSION["success"]);
	}
}
