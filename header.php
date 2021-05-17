<?php 
	require_once "pdo.php";
	require_once "style.php";
	require_once "functions.php";

	$logged_in = false;
	if (isset($_SESSION["user_id"])) {
		$logged_in = true;
	}
 ?>
<nav class="navbar navbar-light bg-light">
	<div class="AN-nav-left">
	  <h1>
	  	<a href="index.php">
		    <img src="ARR.svg" width="auto" height="100" alt="">
		  </a>
		  <span>Alexandra's Resume Registry</span>
	  </h1>
	</div>

  <?php if ($logged_in == true): ?>
  <div class="AN-nav-right">
  	<h3><a href="logout.php">Logout</a></h3>
  </div>
  <?php endif ?>
</nav>