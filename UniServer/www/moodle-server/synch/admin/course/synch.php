<?php
 require_once("setup.php");

GLOBAL $CourseBeanFactory, $CourseSynchManager, $Out;


$page = new object;
$page->action = synch_GetRequestItem("action");
$page->self = $_SERVER['PHP_SELF'];
$page->message = null;
$CourseSynchManager->setDebug(0);
$Out->disable();

switch($page->action){
	case 1;//Preview the records to synch
		$page->recordsToSynch = $CourseSynchManager->getRecordsToSynch();
		break;
		
	case 2; //Perform the import.
		if($CourseSynchManager->performImport()){
			$page->message = "The courses were successully imported";
		}
		break;
	case 3; //Perform a simple import.
		if($CourseSynchManager->performSimpleImport()){
			$page->message = "The courses were successully imported";
		}
	break;
	
	case 4:// Rebuild the course cache
		if($CourseSynchManager->rebuildCourseCache()){
			$page->message = "The cache has been rebuilt.";
		}
	break;

}
?>
<html>
	<head>
		<title>Course Synch</title>
	</head>
	<body>
		<?php include("navigation.php");

			if(isset($page->message)){
				echo $page->message;
			}
		?>
		
		<ul>
			<li>	
	  			<a href="<?php echo $page->self?>?action=1">View records to be imported.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=2">Commence import of the courses.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=3">Commence basic import of the courses.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=4">Rebuild the course cache.</a>
	  		</li>
	  	</ul> 
	  	
	  	<?php
	  		if(isset($page->recordsToSynch) && count($page->recordsToSynch)){
	  			
	  //			include("body.synch.preview.php");
	  		
	  		}
	  	?>

	</body>
</html>
<?php require_once("../synch-teardown.php");?>
