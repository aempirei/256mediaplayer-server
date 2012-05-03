<?

function file_get_lines($filename) {
	$lines = file_get_contents($filename);
	$lines = preg_split('/[\r\n]+/', $lines, -1, PREG_SPLIT_NO_EMPTY);
	return $lines;
}

