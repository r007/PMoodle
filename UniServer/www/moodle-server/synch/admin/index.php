<?php
	require_once('synch-setup.php');
	/*
	* I am currently using the file to develop the basic implementation of the synch
	* implementation
	*/
	GLOBAL $CFG;

	$page = new object;
?>
<html>
	<head>
		<title>Synch Administration Home</title>
	</head>
	<body>
		<?php include("navigation.php");

			if(isset($page->message)){
				echo $page->message;
			}
		?>
		<ul>
			<li><a href="index.php" >Home</a></li>
			<li><a href="user/index.php" >Users</a></li>
			<li><a href="group/index.php" >Groups</a></li>
			<li><a href="course/index.php" >Courses</a></li>
			<li><a href="forum/index.php" >Forum</a></li> 
		</ul>
  
	</body>
</html>
<?php require_once("synch-teardown.php");?>