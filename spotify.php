<?

require_once('./etc/config.php');
require_once('./lib/library.php');

@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);

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

$playlists = array("Gothic Hero", "Ozark Gothic", "Ambient Afterlife");

function feed_item($playlistNo, $playlistName) {

	global $baseUrl;
	global $serverUrl;
	global $contentId;

	$img = imageurl($playlistName);

	$synopsis = str_replace(' ', '·', $playlistName);

	// this may be unintuitive, but this is not part of an xml tag attribute,
	// so the url probably isn't supposed to be uri encoded but rather
	// probably is supposed to be html-entity encoded

	$url = str_replace(' ', '%20', $serverUrl.'/spotify.php?playlist='.$playlistNo);

	return array($img, $img, $playlistName, $contentId++, 'Stream', 'SD', 'mp3', 128, $url, $synopsis, 'Music', 120);
}

$playlist = isset($_GET['playlist']) ? intval($_GET['playlist']) : false;

if($playlist === false) {
	generate_xml();
} elseif(isset($playlists[$playlist])) {
	stream_playlist($playlist, $playlists[$playlist]);
} else {
	header("HTTP/1.0 404 Not Found");
}

function stream_playlist($playlistNo, $playlistName) {

	global $playlists;
	global $webDir;
	global $spotifyUser;
	global $spotifyPass;

	set_time_limit(0);

	header("ICY 200 OK");
	header('Content-Type: audio/mpeg');
	header('icy-br: 128');
	header('icy-name: '.$playlistName);
	header('Cache-Control: no-cache');
	header('Content-Encoding: identity');

	$bin = $webDir.'/bin/spotstream';

	chdir($webDir.'/data');

	$cmd = sprintf("%s -u '%s' -p '%s' -l '%s' | lame -r -m s -b 128 -s 44.1 - -", $bin, $spotifyUser, $spotifyPass, $playlistName);

	$handle = popen($cmd, 'r');

	while(!(feof($handle) or connection_aborted())) {
		$data = fread($handle,1024);
		if($data === false)
			break;
		echo $data;
		flush();
	}

	pclose($handle);
}

function generate_xml() {

	$feed = array();

	global $playlists;

	foreach($playlists as $no => $name) {
		$item = feed_item($no, $name);
		if($item !== null)
			$feed[] = $item;
	}

	$feedSz = sizeof($feed);

	header('Content-Type: application/xml');

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
	echo "<feed>\n";
	echo "<resultLength>$feedSz</resultLength>\n";
	echo "<endIndex>$feedSz</endIndex>\n";

		foreach($feed as $item) {
			echo call_user_func_array('feed_item_string', $item);
		}

	echo "</feed>\n";
}
