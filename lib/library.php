<?

function file_get_lines($filename) {
	$lines = file_get_contents($filename);
	$lines = preg_split('/[\r\n]+/', $lines, -1, PREG_SPLIT_NO_EMPTY);
	return $lines;
}

function cleanfilepath($filepath, $func) {
	$parts = explode('/', $filepath);
	foreach($parts as &$part) {
		$part = call_user_func($func, $part);
	}
	return join('/', $parts);
}

function htmlcleanfilepath($filepath) {
	return cleanfilepath($filepath, 'htmlspecialchars');
}

function uricleanfilepath($filepath) {
	return cleanfilepath($filepath, 'rawurlencode');
}

function &imagemap() {

	static $imagemap;

	static $imagedir = '/images';
	static $ext_regexp = '/\.(png|jpg)$/';

	global $webDir;
	global $serverUrl;

	if(!isset($imagemap)) {

		$imagemap = array();

		$files = preg_grep($ext_regexp, scandir($webDir.$imagedir) ?: array());

		foreach($files as $file) {
			$name = preg_replace($ext_regexp, '', $file);
			$imagemap[$name] = $serverUrl.$imagedir.'/'.$file;
		}
	}

	return $imagemap;
}

// any content name, usually the full file name, will be matched against
// every image in the images directory and the first one to match will
// be used as the content preview image. first the contnet name is normalized
// by s/[-+_.\s]+/-/g and then a case-insensitive substring search for each
// image name is performed. if an image name is a substring of the normalized
// content name, then the corresponding image file is concatenated to the
// server url and returned as the imageurl. image names are not normalized
// so it is important that images use only the - (dash) as a seperator and
// to avoid the rest of the symbols in $normalization_regexp

function imageurl($contentname) {

	static $imageurl = array();
	static $normalization_regexp = '/[-+_.\s]+/';

	if(!array_key_exists($contentname, $imageurl)) {

		$normalname = preg_replace($normalization_regexp, '-', $contentname);

		$imageurl[$contentname] = '';

		foreach(imagemap() as $name => $url) {
			if(stristr($normalname, $name) !== false) {
				$imageurl[$contentname] = $url;
				break;
			}
		}

	}

	return $imageurl[$contentname];
}
