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
$number_of_recent_posts=4;

/*
  this will result in the titles in http link as list items.
  call it so to provide the "latest posts" on the web site.
  example:
     http://bloghome.com/blog.php?r=3 
*/
function get_recent_post_titles($howmany)
{
	$entryDir = opendir("blogs/");
	$titles = array();
	while (false !== ($file = readdir($entryDir))){
	  $lastname = substr(strrchr($file, "."), 1);
	  if($lastname == "blog" && $lastname != "php" && $file != "." && $file != ".."){
			$entrys[filemtime("blogs/$file")] = $file;
	  }
	}
	
	krsort($entrys);
	/* use only as many as requested. */
	$chunked_entries=array_chunk($entrys,$howmany,TRUE);
	$entries = $chunked_entries[0];
	$retval="";
	foreach($entrys as $creation => $entry){
		$pattern = "/<h1 ?.*>(.*)<\/h1>/";
		$post = file_get_contents("blogs/$entry");

		preg_match($pattern, $post, $matches);
		$title = $matches[0];				
		$body = str_replace($title,'', $post);
		$body = substr(strip_tags($body),0,$summary_length);
		$title=strip_tags($title);
		$site="http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']) . '/blog.php';
		$retval .= "<li><a class=\"link\" href=\"$site?$entry\">$title</a></li>\n";
   }
	return $retval;
}
/* Gets the number of post link requests and displays. */
if (isset($_REQUEST['r'])) {
	$listitems=get_recent_post_titles($_REQUEST['r']);
	exit($listitems);
}

/* creates a Year-month string to use for sorting and comparing entry dates.
   also used to create the 'archives' menu. */
if (isset($_REQUEST['m'])){
	// Should be unix timestamp of the 15th at noon.
	$strdate=date("Y-m",$_REQUEST['m']) ."-15 12:00:00";
} else {
	$strdate=date('Y-m') ."-15 12:00:00";
}
$thismonth=strtotime($strdate); //unix time of this month
$archives[$thismonth] = date("F Y",$thismonth); //Month Year for the archive list



/* html below may be changed to work with a site integration. */
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
	/* we are looking for blog article or the blogs for current
	   or the specified month. */
	$parsed_url = parse_url($_SERVER['REQUEST_URI']);
	if(isset($parsed_url['query'])) {
		$checkme = $parsed_url['query'];
	} else {
		$checkme = false;
	}
	
	//request is for a particular post
	if( $checkme && ( strpos($checkme,"m=") === false ) ) {
		$blog_post = $parsed_url['query'];
		$lastname = substr(strrchr($blog_post, "."), 1);
	   if($lastname == "blog" && substr_count($blog_post,'.') == 2){
  	   	echo "<a href=\"./\">up</a><br>";
  	   	$the_entry = file_get_contents("blog/$blog_post");
			echo $the_entry;
		}
	}
	// show the blogs from current or specified month
	else {
      $entryDir = opendir("blog/");
      $entrys = array();
      while (false !== ($file = readdir($entryDir))){
        $lastname = substr(strrchr($file, "."), 1);
        if($lastname == "blog" && $lastname != "php" && $file != "." && $file != ".."){
          	//test if the blog entry is in the month permitted to show.
          	$date=date('Y-m',filemtime("blog/$file")) ."-15 12:00:00";
          	$blogmonth=strtotime($date);
          	if($blogmonth == $thismonth) {
					$entrys[filemtime("blog/$file")] = $file;
				} else {
					// Add the month-year to the archive array if it is not there.
					if( ! array_key_exists($blogmonth, $archives) ) {
						$archives[$blogmonth]=date('F Y',$blogmonth);
					}
				}
			}
      }

      krsort($entrys);
      
		echo "<ul>\n";
		foreach($entrys as $creation => $entry){
			$pattern = "/<h1 ?.*>(.*)<\/h1>/";
			$post = file_get_contents("blog/$entry");

			preg_match($pattern, $post, $matches);
			$title = $matches[0];				
			$body = str_replace($title,'', $post);
			$body = substr(strip_tags($body),0,$summary_length);
			echo "<li>\n";
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
			echo "</li>\n";
      }
      
		echo "</ul>";
		
      closedir($entryDir);
	}
?>
	<div id="archive-links" class="archives">
		<h3>archive</h3>
		<ul>
<?php
krsort($archives);
foreach($archives as $ydashm => $mspacey){
echo "			<li>
				<a href=\"?m=$ydashm\">$mspacey</a>
			</li>";
}
?>
		</ul>
	</div>
  </body>
</html>
