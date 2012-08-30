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
