<?

require_once('./etc/config.php');
require_once('./lib/library.php');

function feed_item_string($sdimg, $hdimg, $title, $id, $type, $q, $fmt, $br, $url, $synopsis, $genre, $runtime) {

	return
		"<item sdImg=\"$sdimg\" hdImg=\"$hdimg\">\n".
		"\t<title>".htmlspecialchars($title)."</title>\n".
		"\t<contentId>$id</contentId>\n".
		"\t<contentType>$type</contentType>\n".
		"\t<contentQuality>$q</contentQuality>\n".
		"\t<streamFormat>$fmt</streamFormat>\n".
		"\t<media>\n".
		"\t\t<streamQuality>$q</streamQuality>\n".
		"\t\t<streamBitrate>$br</streamBitrate>\n".
		"\t\t<streamUrl>".$url."</streamUrl>\n".
		"\t</media>\n".
		"\t<synopsis>".htmlspecialchars($synopsis)."</synopsis>\n".
		"\t<genres>$genre</genres>\n".
		"\t<runtime>$runtime</runtime>\n".
		"</item>\n";
}

$contentId = 1;

function feed_item($filename) {

	global $baseUrl;
	global $contentId;

	$img = '';

	preg_match('/\.(\w\w\w)$/', $filename, $format);

	if(isset($format) and isset($format[1])) {
		$format = $format[1];
	} else {
		return null;
	};

	$title = preg_replace('/\.'.$format.'$/', '', $filename);

	$title = preg_replace('/\s+/', ' ', $title);
	$title = preg_replace('/^\s*\d{0,2}\s*-?\s*/', '', $title);
	$title = preg_replace('/\s+$/', '', $title);

	$synopsis = str_replace(' ', '·', $baseUrl.'/'.$filename);

	$url = str_replace(' ', '%20', $baseUrl.'/'.$filename);

	return array($img, $img, $title, $contentId++, 'Talk', 'SD', $format, 1500, $url, $synopsis, 'Music', 120);
}

$datere = isset($_GET['date']) ? $_GET['date'] : '....-..-..';
$charre = isset($_GET['char']) ? $_GET['char'] : '.';

$files = file_get_lines('./data/videos.txt');
$files = array_values(preg_filter('/^'.$datere.','.$charre.'/i', '', $files));

sort($files);

$feed = array();

foreach($files as $file) {
	$item = feed_item($file);
	if($item !== null) {
		$feed[] = $item;
	}
}

$feedSz = sizeof($feed);

header('Content-Type: application/xml');

echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n";
?>
<feed>
<resultLength><?= $feedSz ?></resultLength>
<endIndex><?= $feedSz ?></endIndex>
<?

foreach($feed as $item) {
	echo call_user_func_array('feed_item_string', $item);
}

?>
</feed>
