<?php

class ChassisDef {
	protected array $chassis_def;
	protected array $file_paths;

	public function __construct(array $chassis_def, string $file_path) {
		$this->chassis_def = $chassis_def;
		$this->file_paths[] = $file_path;
	}

	public function updateDef(array $chassis_def_more, string $file_path):void {
		$this->chassis_def = array_merge($this->chassis_def, $chassis_def_more);
		$this->file_paths[] = $file_path;
	}

	public function getData(): array {
		return [
			$this->getModel(),
			$this->getVariant(),
			$this->getTonnage(),
			$this->getFreeTonnage(),
			$this->getMaxArmor(),
			$this->getFiles(),
		];
	}

	public function hasData(): bool {
		$has_data = true;
		foreach ($this->getData() as $value) {
			if ($value === null) {
				$has_data = false;
			}
		}

		return $has_data;
	}

	private function getModel(): ?string {
		if (!array_key_exists('Description', $this->chassis_def)) {
			return null;
		}
		$description = $this->chassis_def['Description'];
		if (!array_key_exists('Name', $description)) {
			return null;
		}
		return $description['Name'];
	}

	private function getVariant(): ?string {
		if (!array_key_exists('VariantName', $this->chassis_def)) {
			return null;
		}
		return $this->chassis_def['VariantName'];
	}

	private function getTonnage(): ?int {
		if (!array_key_exists('Tonnage', $this->chassis_def)) {
			return null;
		}

		return $this->chassis_def['Tonnage'];
	}

	private function getFreeTonnage(): ?int {
		if (!array_key_exists('InitialTonnage', $this->chassis_def)) {
			return null;
		}

		$tonnage = $this->getTonnage();
		if ($tonnage === null) {
			return null;
		}

		return $this->getTonnage() - $this->chassis_def['InitialTonnage'];
	}

	private function getMaxArmor(): ?int {
		if (!array_key_exists('Locations', $this->chassis_def)) {
			return -1;
		}

		$total_armor = 0;
		foreach ($this->chassis_def['Locations'] as $location) {
			$max_armor = 0;
			$max_rear_armor = 0;
			if (array_key_exists('MaxArmor', $location)) {
				$max_armor = $location['MaxArmor'];
			}
			if (array_key_exists('MaxRearArmor', $location)) {
				$max_rear_armor = $location['MaxRearArmor'];
			}

			if ($max_armor > 0) {
				$total_armor += $max_armor;
			}

			if ($max_rear_armor > 0) {
				$total_armor += $max_rear_armor;
			}
		}
		return $total_armor;
	}

	private function getFiles(): string {
		return implode(',', $this->file_paths);
	}
}

class ChassisDefFinder {

	protected string $directory;

	public function __construct(string $directory) {
		$this->directory = $directory;
	}

	public function getAllChassisFiles(): array {

		$contents = scandir($this->directory);
		if ($contents === false) {
			throw new Exception('Cannot scan directory' . $this->directory);
		}

		$accumulator = [];
		foreach ($contents as $node) {
			// account for . and .. because why does php find that?
			if ('.' === $node) {
				continue;
			}
			if ('..' === $node) {
				continue;
			}

			$node_path = $this->directory . DIRECTORY_SEPARATOR . $node;
			if (is_file($node_path)) {
				$node_info = pathinfo($node_path);
				if (!array_key_exists('extension', $node_info)) {
					continue;
				}
				if (('json' === $node_info['extension']) && (0 === strpos($node_info['filename'], 'chassisdef'))) {
					$accumulator[] = $node_path;
				}
			} elseif (is_dir($node_path)) {
				$chassis_def_finder = new ChassisDefFinder($node_path);
				$accumulator = array_merge($accumulator, $chassis_def_finder->getAllChassisFiles());
			}
		}

		return array_unique($accumulator);
	}
}

// taken from https://stackoverflow.com/questions/13236819/how-to-fix-badly-formatted-json-in-php
function jsonFixer($json){
	$patterns     = [];
	/** garbage removal */
	$patterns[0]  = "/([\s:,\{}\[\]])\s*'([^:,\{}\[\]]*)'\s*([\s:,\{}\[\]])/"; //Find any character except colons, commas, curly and square brackets surrounded or not by spaces preceded and followed by spaces, colons, commas, curly or square brackets...
	$patterns[1]  = '/([^\s:,\{}\[\]]*)\{([^\s:,\{}\[\]]*)/'; //Find any left curly brackets surrounded or not by one or more of any character except spaces, colons, commas, curly and square brackets...
	$patterns[2]  =  "/([^\s:,\{}\[\]]+)}/"; //Find any right curly brackets preceded by one or more of any character except spaces, colons, commas, curly and square brackets...
	$patterns[3]  = "/(}),\s*/"; //JSON.parse() doesn't allow trailing commas
	/** reformatting */
	$patterns[4]  = '/([^\s:,\{}\[\]]+\s*)*[^\s:,\{}\[\]]+/'; //Find or not one or more of any character except spaces, colons, commas, curly and square brackets followed by one or more of any character except spaces, colons, commas, curly and square brackets...
	$patterns[5]  = '/["\']+([^"\':,\{}\[\]]*)["\']+/'; //Find one or more of quotation marks or/and apostrophes surrounding any character except colons, commas, curly and square brackets...
	$patterns[6]  = '/(")([^\s:,\{}\[\]]+)(")(\s+([^\s:,\{}\[\]]+))/'; //Find or not one or more of any character except spaces, colons, commas, curly and square brackets surrounded by quotation marks followed by one or more spaces and  one or more of any character except spaces, colons, commas, curly and square brackets...
	$patterns[7]  = "/(')([^\s:,\{}\[\]]+)(')(\s+([^\s:,\{}\[\]]+))/"; //Find or not one or more of any character except spaces, colons, commas, curly and square brackets surrounded by apostrophes followed by one or more spaces and  one or more of any character except spaces, colons, commas, curly and square brackets...
	$patterns[8]  = '/(})(")/'; //Find any right curly brackets followed by quotation marks...
	$patterns[9]  = '/,\s+(})/'; //Find any comma followed by one or more spaces and a right curly bracket...
	$patterns[10] = '/\s+/'; //Find one or more spaces...
	$patterns[11] = '/^\s+/'; //Find one or more spaces at start of string...

	$replacements     = [];
	/** garbage removal */
	$replacements[0]  = '$1 "$2" $3'; //...and put quotation marks surrounded by spaces between them;
	$replacements[1]  = '$1 { $2'; //...and put spaces between them;
	$replacements[2]  = '$1 }'; //...and put a space between them;
	$replacements[3]  = '$1'; //...so, remove trailing commas of any right curly brackets;
	/** reformatting */
	$replacements[4]  = '"$0"'; //...and put quotation marks surrounding them;
	$replacements[5]  = '"$1"'; //...and replace by single quotation marks;
	$replacements[6]  = '\\$1$2\\$3$4'; //...and add back slashes to its quotation marks;
	$replacements[7]  = '\\$1$2\\$3$4'; //...and add back slashes to its apostrophes;
	$replacements[8]  = '$1, $2'; //...and put a comma followed by a space character between them;
	$replacements[9]  = ' $1'; //...and replace by a space followed by a right curly bracket;
	$replacements[10] = ' '; //...and replace by one space;
	$replacements[11] = ''; //...and remove it.

	$result = preg_replace($patterns, $replacements, $json);

	return $result;
}


if ($argc < 2) {
	echo "Usage: php battletech_mech_parser.php <battletech_directory>\n";
	exit(1);
}

$chassis_defs = [];
$target_dir = $argv[1];
$chassis_def_finder = new ChassisDefFinder($target_dir);
foreach ($chassis_def_finder->getAllChassisFiles() as $chassis_file) {
	$chassis_file_info = pathinfo($chassis_file);
	$chassis_name_parts = explode('_', $chassis_file_info['filename']);
	$chassis_name_parts = array_slice($chassis_name_parts, 0, 3);
	$chassis_name = array_pop($chassis_name_parts);

	$chassis_file_contents = file_get_contents($chassis_file);
	if (false === $chassis_file_contents) {
		echo "Could not read file " . $chassis_file . "\n";
		continue;
	}

	$chassis_def = json_decode($chassis_file_contents, true);
	/**
	 * if decode failed, try fixing the file. Do not try to fix the file before decoding because
	 * the fixer can mess up correct json. Have not troubleshooted the missing condition it does not
	 * handle
	 */
	if (null === $chassis_def) {
		$chassis_file_contents = jsonFixer($chassis_file_contents);
		$chassis_def = json_decode($chassis_file_contents, true);
	}

	if (null === $chassis_def) {
		echo "Invalid chassis_def " . $chassis_file . "|error: " . json_last_error_msg() . "\n";
		continue;
	}
	if (! array_key_exists($chassis_name, $chassis_defs)) {
		$chassis_defs[$chassis_name] = new ChassisDef($chassis_def, $chassis_file);
	} else {
		$chassis = $chassis_defs[$chassis_name];
		$chassis->updateDef($chassis_def, $chassis_file);
	}
}

$headers = [
	'model',
	'variant',
	'tonnage',
	'free_tonnage',
	'max_armor',
];
print(implode(',', $headers) . "\n");
foreach ($chassis_defs as $chassis_def) {
	if (! $chassis_def->hasData()) {
		continue;
	}
	print(implode(',', $chassis_def->getData()) . "\n");
}
