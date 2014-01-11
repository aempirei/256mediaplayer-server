<?php

require_once('./etc/config.php');
require_once('./lib/library.php');

if($realtime) {
	exec("./bin/update-hq-videos.sh", $buf, $ret);
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

	<category title="Videos by Download Date" description="<?= $videoSz ?> videos found" sd_img="<?= imageurl('by-download-date') ?>" hd_img="<?= imageurl('by-download-date') ?>">
<?
	$dates = file_get_lines('./data/dates.txt');
	rsort($dates);
	foreach($dates as $date) {
		echo "<categoryLeaf title='$date' description='Videos from $date' feed='$serverUrl/videos.php?date=$date' />\n";
	}
?>
	</category>

	<category title="Videos by First Character" description="<?= $videoSz ?> videos found" sd_img="<?= imageurl('by-1st-char') ?>" hd_img="<?= imageurl('by-1st-char') ?>">
<?
	$chars = file_get_lines('./data/chars.txt');
	foreach($chars as $char) {
		$htmlchar = htmlspecialchars($char);
		$urlchar = htmlspecialchars($char);
		echo "<categoryLeaf title='$htmlchar' description='Videos beginning with $htmlchar' feed='$serverUrl/videos.php?char=$urlchar' />\n";
	}
?>
	</category>

	<category title="All Videos" description="<?= $videoSz ?> videos found" sd_img="<?= imageurl('all-videos') ?>" hd_img="<?= imageurl('all-videos') ?>">
		<categoryLeaf title='All' description='All Videos' feed='<?= $serverUrl ?>/videos.php' />
	</category>

	<? if($spotify): ?>
	<category title="Spotify" description="stream your spotify playlists" sd_img="<?= imageurl('spotify') ?>" hd_img="<?= imageurl('spotify') ?>">
		<categoryLeaf title='Spotify' description='Spotify Playlists' feed='<?= $serverUrl ?>/spotify.php' />
	</category>
	<? endif ?>

<? endif ?>

 </categories>
