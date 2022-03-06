<?php

/**
 * 配列の階層をドット区切りのパスで辿って値を返す
 */
function _p($array, $path, $default = '') {
	$path_parts = explode('.', $path);
	foreach ($path_parts as $part) {
		if (is_array($array) == false) {
			return $default;
		}

		if (isset($array[$part]) == true) {
			$array = $array[$part];
		}
		else {
			return $default;
		}
	}

	return $array;
}
