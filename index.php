<?php
$lang_code="en";
$blog_title="bare minimum blog";
$blog_one_liner="barebone very minimal php blog";
$contact="https://github.com/dansdrivers";
$email="uremail@email.com";
$homepage="../";
$news_link="http://news.google.com";
$twitter="https://www.twitter.com";
$summary_length=300;
?>
<html>
  <head>

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="top" title="<?=$blog_title;?>" href="index.php">

    <meta name="author" content="auser" >
    <meta name="publisher" content="dansdrivers">
    <meta name="description" content="Modified version of github.com/klassiker">
    <meta name="keywords" content="minimal, blog, php, one-page, flat, file">
    <meta http-equiv="content-language" content="<?=$lang_code;?>">
    <meta name="robots" content="index, nofollow">
    <meta name="revisit-after" content="2 days">

    <style type="text/css">
      @import "index.css";
    </style>

    <title>
      <?=$blog_title;?>
    </title>

  </head>

  <body>

    <h2 id="blog-title"><?=$blog_title;?></h2>
    <p id="blog-one-liner"><?=$blog_one_liner;?></p>

    <h4 class="header-links"><a href="<?=$homepage;?>">
    HOME</a> ||
     
    <a href="<?=$contact;?>">
    CONTACT</a> || 

    <a href="<?=$twitter;?>">
    TWITTER</a> || 

    <a href="<?=$news_link;?>">
    NEWS</a> || 

    <a href="mailto:<?=$email;?>">
    EMAIL</a></h4>
    <br>

    <?php

		$parsed_url = parse_url($_SERVER['REQUEST_URI']);
		if(isset($parsed_url['query']) ) {
			$blog_post = $parsed_url['query'];
			$lastname = substr(strrchr($blog_post, "."), 1);
   	   if($lastname == "blog" && substr_count($blog_post,'.') == 2){
   	   	echo "<a href=\"./\">up</a><br>";
   	   	$the_entry = file_get_contents("blog/$blog_post");
				echo $the_entry;
			}
		}
		else {
			
	      $entryDir = opendir("blog/");
	      $entrys = array();
	      while (false !== ($file = readdir($entryDir))){
	        $lastname = substr(strrchr($file, "."), 1);
	        if($lastname == "blog" && $lastname != "php" && $file != "." && $file != ".."){
	          $tmpVar = time() - fileatime("blog/$file");
	          if($tmpVar > 2678400){
	            if(!copy("blog/$file", "old/$file")){
	              $logFile = fopen("log", "a");
	              fwrite($logFile, "Error deleting old posts."."\n");
	            } 
	            else {
	              unlink("blog/$file");
	            }
	          }
	          else{
	            $entrys[filemtime("blog/$file")] = $file;
	          }
	        }
	      }
	
	      krsort($entrys);
	      
			echo "<div class=\"blog\">\n";
			foreach($entrys as $creation => $entry){
				$pattern = "/<h1 ?.*>(.*)<\/h1>/";
				$post = file_get_contents("blog/$entry");

				preg_match($pattern, $post, $matches);
				$title = $matches[0];				
				$body = str_replace($title,'', $post);
				$body = substr(strip_tags($body),0,$summary_length);
				
				echo "<a class=\"link\" href=\"?$entry\">$title</a>";
				echo "<a name=\"$creation\"></a><span class=\"post_date\">".date("l,", $creation)." ".date(" F jS, Y", $creation)."</span>";
				if (strlen($body) >= $summary_length) {
					$lastspace = strrpos($body," ");
					$body = "<p>" . substr($body,0,$lastspace);
					echo $body . "... <a href=\"?$entry\">more</a></p>";
				}
				else {
					echo "<p>$body</p>";
				}
				
	      }
			echo "</div>";
			
	      closedir($entryDir);
		}
    ?>
  </body>
</html>
