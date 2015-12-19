<html>
  <head>

	<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="../index.css">
	<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
	<link rel="top" title="klassikers blog" href="index.php">

	<meta name="author" content="auser" >
	<meta name="publisher" content="klassiker">
	<meta name="robots" content="noindex, nofollow">

	<title>
		Blog Editor
	</title>
	<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
	<script>tinymce.init({ selector:'textarea' });</script>
	
	<style type="text/css">
		@import "../index.css";
	</style>

  </head>

  <body>
  <h1>Create and edit blog entries</h1>
  
  <p>This is a very simple blog machine. On the first line, choose 
  &quot;Formats -&gt; Headings -&gt; Heading 1&quot; - the text will 
  become the title of your blog post.</p> 

	<?php
		function getTextBetweenTags($string, $tagname) 
		{
			$pattern = "/<$tagname ?.*>(.*)<\/$tagname>/";
			preg_match($pattern, $string, $matches);
			return $matches[1];
		}

		$title='';
		if(isset($_POST["entry"])){
			$title = str_replace(" ","_",'.'.getTextBetweenTags($_POST["entry"],"h1")); 
			//echo $title;
		}
		
		if(!(empty($_POST["btnVal"]))){
			if($_POST["btnVal"] == "New"){
				if(file_exists(date("dmY")."$title.blog")){
				unlink(date("dmY")."$title.blog");
				} else {
				$logFile = fopen("../log", "a");
				fwrite($logFile, "Error deleting old blog."."\n");
				fclose($logFile);
				}
			}
			if($_POST["btnVal"] == "Save" && !(empty($_POST["entry"]))){
				$toWrite = $_POST["entry"];
				$file = fopen(date("dmY")."$title.blog", "w+");
				fwrite($file, $toWrite);
				fclose($file);
				unlink(date("dmY").".blog");
			}
		}

		if(!(empty($_POST["entry"]))){
			$tarea = $_POST["entry"];
		} elseif(file_exists(date("dmY")."$title.blog")){
			$tarea = file_get_contents(date("dmY")."$title.blog");
		} elseif($_POST["btnVal"] == "Edit" && !(empty($_POST["entryname"] ) ) ){
				$tarea = file_get_contents($_POST["entryname"]);
		} else {
			$tarea = "";
		}
	?>

	<script type="text/javascript">
		function insert(ins,entry) {
			if(ins == "a"){
				var tmp = prompt("URL?","");
				entry.value+='<a href='+tmp+'>';
			}
			if(ins == "h"){
				var tmp = prompt("Size? 1-6","");
				entry.value+='<h'+tmp+'>';
			}else{
				entry.value+='<'+ins+'>';
			}
			entry.focus();
			if(ins.indexOf("/")!=-1){
				end++;
			}else{
				start++;
			}
		}
		var start = 0;
		var end = 0;
	</script>

	<form method="post" action="index.php">
		<input type="submit" value="New" name="btnVal" onclick="entry.value='';"/>
		<input type="submit" value="Save" name="btnVal"/>
		<textarea name="entry" style="height:400px"><?PHP echo $tarea; ?></textarea>
	</form>
	<h2>Manage blog entries</h2>
	
	<?php
		if(!(empty($_POST["btnVal"]))){
			if($_POST["btnVal"] == "Delete"){
				if(isset($_POST["entryname"])){
					unlink($_POST["entryname"]);
				}
			}
		}

      $entryDir = opendir("./");
      $entrys = array();
      while (false !== ($file = readdir($entryDir))) {
        $lastname = substr(strrchr($file, "."), 1);
        if($lastname == "blog" && $lastname != "php" && $file != "." && $file != ".."){
          $entrys[filemtime("$file")] = $file;
        }
      }

      krsort($entrys);

      foreach($entrys as $entry){
        echo '<form method="post" action="index.php">';
        echo '<input type="hidden" name="entryname" value="'.$entry.'"/>';
        $exploded = explode(".",$entry);
        $name_string = str_replace("_"," ",$exploded[1]);
        echo "$name_string <input type=\"submit\"  name=\"btnVal\" value=\"Delete\"/>";
        echo "<input type=\"submit\"  name=\"btnVal\" value=\"Edit\"/>";
        echo "</form>";
      }
	?>

  </body>
</html>
