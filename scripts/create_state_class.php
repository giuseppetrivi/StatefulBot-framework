<?php

/**
 * CLI helper to create a new State class file under control/states
 * Usage:
 *  php scripts/create_state_class.php --name=MyState [--description="Desc"] [--extend=AbstractState] [--path=firstpath/subdir]
 */

function prompt($text, $default = null) {
  /* PHP_SAPI gets the environment where the script is executed */
	if (PHP_SAPI !== 'cli') return $default;
	if ($default !== null) $text .= " [$default]";
	$text .= ": ";
	if (function_exists('readline')) {
		$val = readline($text);
		if ($val === false) return $default;
		$val = trim($val);
	} else {
		// Fallback for environments (like Windows) without readline extension
		echo $text;
		$val = fgets(STDIN);
		if ($val === false) return $default;
		$val = trim($val);
	}
	return $val === '' ? $default : $val;
}

$options = getopt("", ["name:", "description::", "extend::", "path::", "help"]);
if (isset($options['help'])) {
	echo "Usage: php scripts/create_state_class.php --name=MyState [--description=\"Desc\"] [--extend=AbstractState] [--path=firstpath/subdir]\n";
	exit(0);
}

$name = $options['name'] ?? null;
$description = $options['description'] ?? null;
$extend = $options['extend'] ?? 'AbstractState';
$path = $options['path'] ?? '';

/* --- Check on ClassName --- */
if (!$name) {
	$name = prompt('Class name (e.g. MyState)');
}

if (!$name) {
	fwrite(STDERR, "Error: class name is required\n");
	exit(1);
}

if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
	fwrite(STDERR, "Error: invalid PHP class name\n");
	exit(1);
}

/* --- Check on description --- */
if ($description === null) {
	$description = prompt('Description (optional, empty for null)', '');
	if ($description === '') $description = null;
}

// Determine target directory and namespace following project conventions:
$base_states_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'control' . DIRECTORY_SEPARATOR . 'states';
$path_trim = trim($path, "\\/");
if ($path_trim === '') {
	$target_dir = $base_states_dir;
} else {
	// split path on both forward and back slashes; use # delimiter to avoid escaping '/'
	$segments = preg_split('#[\\/]+#', $path_trim);
	$target_dir = $base_states_dir . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments);
}

// Determine parent class and use statement
$use_line = '';
$parent_short = $extend;
if (strpos($extend, '\\') === false) {
	// AbstractState is under CustomBotName\control
	$use_line = "use StatefulBotFramework\\framework\\state_logic\\$extend;";
	$parent_short = $extend;
} else {
	// fully-qualified given
	$parts = explode('\\', $extend);
	$parent_short = end($parts);
	$use_line = "use $extend;";
}

// Ensure directory exists
if (!is_dir($target_dir)) {
	if (!mkdir($target_dir, 0777, true)) {
		fwrite(STDERR, "Error: cannot create directory $target_dir\n");
		exit(1);
	}
}

$filename = $target_dir . DIRECTORY_SEPARATOR . $name . '.php';
if (file_exists($filename)) {
	fwrite(STDERR, "Error: file already exists: $filename\n");
	exit(1);
}

$doc_desc = $description !== null ? $description : null;

$content_lines = [];
$content_lines[] = "<?php";
$content_lines[] = "";
$content_lines[] = "// TODO add a valid namespace based on the state pathname (not directory path)";
$content_lines[] = "";
if ($use_line !== '') $content_lines[] = $use_line;
$content_lines[] = "";
if ($doc_desc !== null) {
	$content_lines[] = "/**";
	$content_lines[] = " * $doc_desc";
	$content_lines[] = " */";
}
$content_lines[] = "class $name extends $parent_short {";
$content_lines[] = "";
$content_lines[] = "  protected function defineStaticInputs() {}";
$content_lines[] = "  	\$this->addStaticInput(\"command\", \"xProcedure\")";
$content_lines[] = "  }";
$content_lines[] = "";
$content_lines[] = "  protected function xProcedure() {";
$content_lines[] = "    // procedure to implement";
$content_lines[] = "  }";
$content_lines[] = "";
$content_lines[] = "}";
$content_lines[] = "";

$content = implode(PHP_EOL, $content_lines) . PHP_EOL;

if (file_put_contents($filename, $content) === false) {
	fwrite(STDERR, "Error: cannot write file $filename\n");
	exit(1);
}

echo "Created: $filename\n";
exit(0);

?>