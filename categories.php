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

$videos = file_get_lines('./data/videos.txt');
$videoSz = sizeof($videos);

?>
<categories>

<? if($ret): ?>
	<category title="Error" description="Error genering video list in bin/update-hq-videos.sh : <?= join($buf) ?>" sd_img="" hd_img=""> </category>

<? else: ?>

	<category title="Videos by Download Date" description="<?= $videoSz ?> videos found" sd_img="" hd_img="">
<?
	$dates = file_get_lines('./data/dates.txt');
	rsort($dates);
	foreach($dates as $date) {
		echo "<categoryLeaf title='$date' description='Videos from $date' feed='http://roku.256.bz/videos.php?date=$date' />\n";
	}
?>
	</category>

	<category title="Videos by First Character" description="<?= $videoSz ?> videos found" sd_img="" hd_img="">
<?
	$chars = file_get_lines('./data/chars.txt');
	foreach($chars as $char) {
		$htmlchar = htmlspecialchars($char);
		$urlchar = htmlspecialchars($char);
		echo "<categoryLeaf title='$htmlchar' description='Videos beginning with $htmlchar' feed='http://roku.256.bz/videos.php?char=$urlchar' />\n";
	}
?>
	</category>

	<category title="All Playable Videos" description="<?= $videoSz ?> videos found" sd_img="" hd_img="">
		<categoryLeaf title='All' description='All Videos' feed='http://roku.256.bz/videos.php' />
	</category>

<? endif ?>

 </categories>
