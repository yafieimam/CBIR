<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Content-based Image Retrieval About Cars</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/all.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/prettyPhoto.css" media="screen" />
</head>
<body>
<?php
include ('functions.php');

$connection = mysqli_connect($db_server["host"], $db_server["username"], $db_server["password"], $db_server["database"]);
?>
<div id="wrapper">
  <div id="wrapper-top"> </div>
  <div id="left">
    <a href="index.php"><h1 style="font-size: 20px; line-height: 30px; padding-left: 10px;">Content-based Image Retrieval About Cars</h1></a>
  </div>
  <div id="right">
	<form class="thumbnails" action="addsample.php" method="post" enctype="multipart/form-data">
		<input type="file" name="file" id="file" class="upload">
        <input type="submit" name="submit" value="Submit">
    </form>
    <ul class="thumbnails">
	<?php
		if (isset($_GET["pic"]) && strlen($_GET["pic"]) > 0)
		{
		  //start timer
		  $start = microtime(true);
			
		  // Parse image to get y,i,q values
		  list($y_values, $i_values, $q_values) = ParseImage("query/".$_GET["pic"]);

		  // Dempose and trunctate
		  DecomposeImage($y_values);
		  $y_trunc = TruncateCoeffs($y_values, $COEFFNUM);
		  DecomposeImage($i_values);
		  $i_trunc = TruncateCoeffs($i_values, $COEFFNUM);
		  DecomposeImage($q_values);
		  $q_trunc = TruncateCoeffs($q_values, $COEFFNUM);

		  // Calculate score for every image in database
		  $connection = mysqli_connect($db_server["host"], $db_server["username"], $db_server["password"], $db_server["database"]);

		  // Initialize scores and filenames
		  $result = mysqli_query($connection, "SELECT * FROM images");
		  while($image = mysqli_fetch_array($result)){
			$scores[$image['image_id']] = $w['Y'][0]*ABS($y_values[0][0] - $image['Y_average'])
						  + $w['I'][0]*ABS($i_values[0][0] - $image['I_average']) 
						  + $w['Q'][0]*ABS($q_values[0][0] - $image['Q_average']);
			$filenames[$image['image_id']] = $image['filename'];
		  }

		  // compare query coefficients with database
		  for ($i = 0; $i < $COEFFNUM; $i++) {

			$query = "SELECT * FROM coeffs_y WHERE X = ".$y_trunc['x'][$i]." AND Y = ".$y_trunc['y'][$i]." AND SIGN = '".$y_trunc['sign'][$i]."'";
			$result = mysqli_query($connection, $query);  
			while($coeff_y = mysqli_fetch_array($result)){
			  $scores[$coeff_y['image']] -= $w['Y'][bin($coeff_y['X'],$coeff_y['Y'])];
			}
		  
			$query = "SELECT * FROM coeffs_i WHERE X = ".$i_trunc['x'][$i]." AND Y = ".$i_trunc['y'][$i]." AND SIGN = '".$i_trunc['sign'][$i]."'";
			$result = mysqli_query($connection, $query);  
			while($coeff_i = mysqli_fetch_array($result)){
			  $scores[$coeff_i['image']] -= $w['I'][bin($coeff_i['X'],$coeff_i['Y'])];
			}
		  
			$query = "SELECT * FROM coeffs_q WHERE X = ".$q_trunc['x'][$i]." AND Y = ".$q_trunc['y'][$i]." AND SIGN = '".$q_trunc['sign'][$i]."'";
			$result = mysqli_query($connection, $query);  
			while($coeff_q = mysqli_fetch_array($result)){
			  $scores[$coeff_q['image']] -= $w['Q'][bin($coeff_q['X'],$coeff_q['Y'])];
			}
		  }

		  mysqli_close($connection);
		  asort($scores,SORT_NUMERIC);
		  
		  // paging
		  if ($_GET["page"] == 1)
			$prev_page = 1;
		  else
			$prev_page = $_GET["page"] - 1;
		  $next_page = $_GET["page"] + 1;
		  
		  $i = 0;
		  foreach($scores as $key => $value){
			if ($i >= 16*($_GET["page"]-1) && $i <= (16*$_GET["page"])-1){
				echo "<li><a href='images-big/".$filenames[$key]."'><img src='images-small/".$filenames[$key]."' alt='' width='150' height='121' /></a></li>";
			}
			$i++;
		  }
	?>
    </ul>
	<div class="navigation">
	<?php
		echo "<a href='index.php?pic=".$_GET["pic"]."&page=".$prev_page."' class='prev'>Previous</a> <a href='index.php?pic=".$_GET["pic"]."&page=".$next_page."' class='next'>Next</a>";
	}
	?>
	</div>
  </div>
  <div id="wrapper-bottom"> </div>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<script type="text/javascript" src="http://cloud.github.com/downloads/malsup/cycle/jquery.cycle.all.latest.js"></script>
<script type="text/javascript" src="js/jquery.prettyPhotos.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>
