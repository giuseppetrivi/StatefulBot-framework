<?php

/**
 * CLI script to rename the bot namespace globally across the project.
 * 
 * Usage:
 *  php scripts/change_bot_name.php --new-name=MyBot [--old-name=CustomBotName] [--dry-run] [--backup]
 * 
 * Options:
 *  --new-name=NAME      The new bot name (PascalCase, required).
 *  --old-name=NAME      The old bot name to replace (default: auto-detect from project_autoloader).
 *  --dry-run            Simulate changes without writing files.
 *  --backup             Create .backup copies of modified files.
 *  --help               Show this help message.
 */

function prompt($text, $default = null) {
	if (PHP_SAPI !== 'cli') return $default;
	if ($default !== null) $text .= " [$default]";
	$text .= ": ";
	if (function_exists('readline')) {
		$val = readline($text);
		if ($val === false) return $default;
		$val = trim($val);
	} else {
		echo $text;
		$val = fgets(STDIN);
		if ($val === false) return $default;
		$val = trim($val);
	}
	return $val === '' ? $default : $val;
}

function validate_bot_name($name) {
	// PascalCase: must start with uppercase letter, contain only letters, digits, underscore
	return preg_match('/^[A-Z][A-Za-z0-9_]*$/', $name) === 1;
}

function find_old_name($project_root) {
	$autoloader_file = $project_root . DIRECTORY_SEPARATOR . 'project_autoloader.php';
	if (!file_exists($autoloader_file)) {
		return null;
	}
	$content = file_get_contents($autoloader_file);
	if (preg_match('/addNamespace\("([A-Z][A-Za-z0-9_]*)"/m', $content, $matches)) {
		return $matches[1];
	}
	return null;
}

function find_php_files($directory) {
	$files = [];
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
	foreach ($iterator as $file) {
		if ($file->isFile() && $file->getExtension() === 'php') {
			$files[] = $file->getPathname();
		}
	}
	return $files;
}

function replace_in_file($filepath, $old_name, $new_name, $dry_run = false, $backup = false) {
	$content = file_get_contents($filepath);
	$original = $content;
	
	// Replace namespace declarations and use statements
	$patterns = [
		"/\\bnamespace\\s+\\Q{$old_name}\\E(\\\\|;)/m" => "namespace {$new_name}$1",
		"/\\buse\\s+\\Q{$old_name}\\E(\\\\|;)/m" => "use {$new_name}$1",
	];
	
	foreach ($patterns as $pattern => $replacement) {
		$content = preg_replace($pattern, $replacement, $content);
	}
	
	if ($content === $original) {
		return false; // No changes
	}
	
	if (!$dry_run) {
		if ($backup) {
			copy($filepath, $filepath . '.backup');
		}
		file_put_contents($filepath, $content);
	}
	
	return true; // Changes made
}

function update_autoloader($project_root, $old_name, $new_name, $dry_run = false, $backup = false) {
	$autoloader_file = $project_root . DIRECTORY_SEPARATOR . 'project_autoloader.php';
	$content = file_get_contents($autoloader_file);
	$original = $content;
	
	$content = str_replace(
		"addNamespace(\"$old_name\"",
		"addNamespace(\"$new_name\"",
		$content
	);
	
	if ($content === $original) {
		return false;
	}
	
	if (!$dry_run) {
		if ($backup) {
			copy($autoloader_file, $autoloader_file . '.backup');
		}
		file_put_contents($autoloader_file, $content);
	}
	
	return true;
}

function update_config($project_root, $new_name, $dry_run = false, $backup = false) {
	$config_file = $project_root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'actual_config.json';
	if (!file_exists($config_file)) {
		return false;
	}
	
	$json = json_decode(file_get_contents($config_file), true);
	if ($json === null) {
		return false;
	}
	
	if (!isset($json['bot_name']) || $json['bot_name'] !== $new_name) {
		$json['bot_name'] = $new_name;
		
		if (!$dry_run) {
			if ($backup) {
				copy($config_file, $config_file . '.backup');
			}
			file_put_contents($config_file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
		}
		
		return true;
	}
	
	return false;
}

// Parse CLI options
$options = getopt("", ["new-name:", "old-name::", "dry-run", "backup", "help"]);

if (isset($options['help'])) {
	echo "Usage: php scripts/change_bot_name.php --new-name=MyBot [--old-name=CustomBotName] [--dry-run] [--backup]\n";
	echo "\nOptions:\n";
	echo "  --new-name=NAME      The new bot name (PascalCase, required).\n";
	echo "  --old-name=NAME      The old bot name to replace (default: auto-detect).\n";
	echo "  --dry-run            Simulate changes without writing files.\n";
	echo "  --backup             Create .backup copies of modified files.\n";
	echo "  --help               Show this help message.\n";
	exit(0);
}

$new_name = $options['new-name'] ?? null;
$old_name = $options['old-name'] ?? null;
$dry_run = isset($options['dry-run']);
$backup = isset($options['backup']);

$project_root = __DIR__ . DIRECTORY_SEPARATOR . '..';

// Validate new name
if (!$new_name) {
	$new_name = prompt('New bot name (PascalCase)');
}

if (!$new_name) {
	fwrite(STDERR, "Error: new bot name is required\n");
	exit(1);
}

if (!validate_bot_name($new_name)) {
	fwrite(STDERR, "Error: invalid bot name format. Must be PascalCase (e.g. MyBot, MyAwesomeBot)\n");
	exit(1);
}

// Auto-detect old name if not provided
if (!$old_name) {
	$old_name = find_old_name($project_root);
	if (!$old_name) {
		fwrite(STDERR, "Error: could not auto-detect old bot name. Use --old-name=XXX\n");
		exit(1);
	}
	echo "Auto-detected old bot name: $old_name\n";
}

// Confirm before proceeding
if (!$dry_run) {
	$confirm = prompt("Really rename '$old_name' to '$new_name'?", 'n');
	if (strtolower($confirm) !== 'y') {
		echo "Cancelled.\n";
		exit(0);
	}
}

// Run the refactoring
$mode = $dry_run ? 'DRY RUN' : 'RENAME';
echo "\n[$mode] Starting bot name refactoring...\n";

$php_files = find_php_files($project_root);
$file_count = 0;
foreach ($php_files as $filepath) {
	if (replace_in_file($filepath, $old_name, $new_name, $dry_run, $backup)) {
		$rel_path = str_replace($project_root . DIRECTORY_SEPARATOR, '', $filepath);
		echo "  Updated: $rel_path\n";
		$file_count++;
	}
}

if (update_autoloader($project_root, $old_name, $new_name, $dry_run, $backup)) {
	echo "  Updated: project_autoloader.php\n";
	$file_count++;
}

if (update_config($project_root, $new_name, $dry_run, $backup)) {
	echo "  Updated: config/actual_config.json\n";
	$file_count++;
}

if ($file_count === 0) {
	echo "No changes needed.\n";
	exit(0);
}

echo "\nTotal files modified: $file_count\n";
if ($dry_run) {
	echo "(Use without --dry-run to apply changes)\n";
}
if ($backup) {
	echo "(Backup copies created with .backup extension)\n";
}

exit(0);

?>
