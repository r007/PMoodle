<?php

	/*
	* I am currently using the file to develop the basic implementation of the synch
	* implementation
	*/
	GLOBAL $CFG;
	require_once("setup.php");
	$page = new object;
?>
<html>
	<head>
		<title>User Synch Home</title>
	</head>
	<body>
		<?php include("navigation.php");

			if(isset($page->message)){
				echo $page->message;
			}
		?>
		<ul>
			<li>	
	  			<a href="synch.php">Synch the user data.</a>
	  		</li>
	  	</ul> 
  
	</body>
</html>
<?php require_once("../synch-teardown.php");?>