<?php

include('functions.php');

$filename = UploadImage("query");
  
header( 'Location: index.php?pic='.$filename.'&page=1' ) ;

?>