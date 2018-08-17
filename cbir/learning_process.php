<?php

include('functions.php');

echo "Scan Folder /images-small..<br>\n";

$folder = new DirectoryIterator('images-small');
$total = 0;
foreach ($folder as $fileInfo) {
  if($fileInfo->isDot()) continue;
  $total++;
}
echo $total." gambar ditemukan<br>\n";
echo "Proses dimulai..<br>\n";

//start timer
$start = microtime(true);
set_time_limit(0);

$i=0;
foreach ($folder as $fileInfo) {
  if($fileInfo->isDot()) continue;
  $i++;
  ProcessImage($fileInfo->getFilename());
  outputProgress($i,$total);
}
$end = microtime(true) - $start;

echo "<p>Proses Selesai!</p>\n";

/**
 * Output span with progress.
 *
 * @param $current integer Current progress out of total
 * @param $total   integer Total steps required to complete
 */
function outputProgress($current, $total) {
    echo "<span style='position: absolute;z-index:$current;background:#FFF;'>Processed " . round($current / $total * 100) . "% </span>";
    myFlush();
    //sleep(1);
}

/**
 * Flush output buffer
 */
function myFlush() {
    echo(str_repeat(' ', 256));
    if (@ob_get_contents()) {
        @ob_end_flush();
    }
    flush();
}

?>
<input type="button" value="Back"  onclick="window.open(&quot;index.php&quot;,&quot;_self&quot;); window.open(&quot;index.php&quot;,&quot;_self&quot;);" />