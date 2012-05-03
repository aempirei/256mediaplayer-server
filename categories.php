<?

require_once('./etc/config.php');
require_once('./lib/library.php');

if($realtime) {
	exec("./bin/update-hq-videos.sh", &$buf, &$ret);
} else {
	$ret = 0;
}

header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n";

?>
<categories>

<? if($ret): ?>
	<category title="Error" description="Error genering video list in bin/update-hq-videos.sh : <?= join($buf) ?>" sd_img="" hd_img=""> </category>

<? else: ?>

	<category title="Videos" description="HellaNZB Videos" sd_img="" hd_img="">
<?

	$dates = file_get_lines('./data/dates.txt');
	rsort($dates);
	echo "<categoryLeaf title='All' description='All Videos' feed='http://roku.256.bz/videos.php' />\n";
	foreach($dates as $date) {
		echo "<categoryLeaf title='$date' description='Videos Created on $date' feed='http://roku.256.bz/videos.php?date=$date' />\n";
	}
?>
	</category>
<? endif ?>

 </categories>
