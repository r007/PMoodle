<?php 
 require_once dirname(__FILE__). "/setup.php";

GLOBAL $BeanFactory, $ForumSynchManager, $Out;


$page = new object;
$page->action = synch_GetRequestItem("action");
$page->self = $_SERVER['PHP_SELF'];
$page->message = null;
$ForumSynchManager->setDebug(0);
//$Out->disable();

switch($page->action){
	case 1;//Preview the records to synch
		$page->recordsToSynch = $ForumSynchManager->getRecordsToSynch();
		break;
		
		case 2; //Perform the import.
		if($ForumSynchManager->performImport()){
			$page->message = "The forum was successully imported";
		}
		break;
	case 3; //Perform a simple import.
		if($ForumSynchManager->performSimpleImport()){
			$page->message = "The forum was successully imported";
		}
	break;
	
	case 4:// Rebuild the course cache
		if($ForumSynchManager->rebuildCourseCache()){
			$page->message = "The course cache has been rebuilt.";
		}
	break;

}
?>
<html>
	<head>
		<title>Forum Synch</title>
	</head>
	<body>
		<?php  require_once dirname(__FILE__). "/navigation.php";

			if(isset($page->message)){
				echo $page->message;
			}
		?>
		
		<ul>
			<li>	
	  			<a href="<?php echo $page->self?>?action=1">View records to be imported.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=2">Commence import of the forum module.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=3">Commence basic import of the forum module.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=4">Rebuild the course cache.</a>
	  		</li>
	  	</ul> 
	  	
	  	<?php
	  		if(isset($page->recordsToSynch) && count($page->recordsToSynch)){
	  			
	  			include("body.synch.preview.php");
	  		
	  		}
	  	?>

	</body>
</html>
<?php require_once dirname(__FILE__). "/teardown.php";?>
