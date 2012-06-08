<?php
$version="";

$root= substr($_SERVER["DOCUMENT_ROOT"],0,-4);   // Get path
$file="$root\uni_con\config_tracker.ini" ;     // Name and path of configuration file

if (file_exists($file) && is_readable($file)){   // Check file
  $settings=parse_ini_file($file,true);          // parse file into an array
  $version=$settings["VERSION"]["us_version"];   // get parameter
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title>The Uniform Server </title>
<meta name="Description" content="The Uniform Server 8.1.0-Coral." />
<meta name="Keywords" content="The Uniform Server, MPG, Mike, Ric, UniServer, Olajide, BobS " />
<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
</head>

<style type="text/css">
/*****************************************/
.intro{
  margin-top:30px;
  padding:10px;
  font-size:12px;
  font-family:Verdana;
  background-color: #E7E7FD;
}
/*****************************************/
</style>

<body>

<div id="wrap">
  <div id="header"><a href="http://www.uniformserver.com"><img src="images/logo.jpg" align="left" alt="The Uniform Server" /></a>
    <p style=" font-size:24px; margin: 0px; padding-top:10px">
      The Uniform Server <br />  <?php print $version; ?>
    </p>
  </div>

  <div id="content">
    <h1>Welcome to The Uniform Server - Coral</h1>
    <p class="intro">This is a test page named <b>index.php</b><br />It was served from root folder UniServer\<b>www</b></p>

  <div align="center" style="height:170px; padding-top:30px;">
     <img src="images/padlock2.gif"  alt="Padlock" />
  </div>
<!-- subdirs -->
  <table>
  <tr><td><h2>Served Subdirectories</h2></td></tr>
  </table>
  <table width=100%>
  <?php $n = 0; foreach (scandir("./") as $file){
    if (is_dir($file) && !in_array($file, array(".", "..", "css", "images"))){
        $n++;
        echo ($n % 3 ? (($n+1) % 3 ? "<tr><td width=33%>$n - <a href='" . $file . "' target='_blank'>" . $file . "</a></td>" : "<td width=33%>$n - <a href='" . $file . "' target='_blank'>" . $file . "</a></td>") : "<td>$n - <a href='" . $file . "' target='_blank'>" . $file . "</a></td></tr>");
    }
  }
  echo ($n == 0 ? "<tr><td style='color: red;' colspan='3'>None</td></tr>" : ($n % 2 == 0 ? "" : "<td></td></tr>"));?>
  </table>
<!-- php files -->
  <table>
  <tr><td><h2>Served PHP Files</h2></td></tr>
  </table>
  <table width=100%>
  <?php $n = 0; foreach (scandir("./") as $file){
    if (strtolower(strrchr($file, '.'))==".php" ){
        $n++;
        echo ($n % 3 ? (($n+1) % 3 ? "<tr><td width=33%>$n - <a href='" . $file . "' target='_blank'>" . $file . "</a></td>" : "<td width=33%>$n - <a href='" . $file . "' target='_blank'>" . $file . "</a></td>") : "<td>$n - <a href='" . $file . "' target='_blank'>" . $file . "</a></td></tr>");
    }
  }
  echo ($n == 0 ? "<tr><td style='color: red;' colspan='3'>None</td></tr>" : ($n % 2 == 0 ? "" : "<td></td></tr>"));?>
  </table>
<!-- -->   

  <div id="divider">Developed By <a href="http://www.uniformserver.com/">The Uniform Server Development Team</a></div>
</div>
</div>
</body>
</html>
