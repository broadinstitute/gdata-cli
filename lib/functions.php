<?php
/**
 * prints an array to the console that's easy to read
 */
function debug($v) {
	print_r($v);
}

/**
 * prints a line to the console with a carriage return
 */
function out($v) {
	print "$v\n";
}

/**
 * prints a line to standard error with a carriage return
 */
function errout($v) {
  fwrite(STDERR, "$v\n");
}

/**
 * returns a human-readable explanation of the result codes
 */
function error($code) {
	switch ($code) {
		case 'EntityExists':
			$str = 'Cannot complete that action because the entity already exists';
			break;
		case 'EntityDoesNotExist':
			$str = 'Cannot complete that action because that entity does not exist';
			break;
		case 'InvalidPassword':
			$str = 'The password supplied is not valid';
			break;
		default:
			$str = $code;
	}
	return $str;
}

/**
 * determines whether incoming argument list is correct, and returns named argument to the calling function
 */
function syntax($action, $args) {
	global $syntax;
	if (count($args) < count($syntax[$action]['argv'])) {
		die(errout("Missing argument: $action " . implode(' ', $syntax[$action]['argv'])));
	} else {
		$out = array();
		foreach ($syntax[$action]['argv'] as $k => $v) {
			$out[$v] = $args[$k];
		}
		return $out;
	}
}
?>
