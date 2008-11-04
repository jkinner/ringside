<?php
class RingsideWebTestUtils {
	public static function parse_headers($headers) {
		$header_array = array();
		$lines = split("\n", $headers);
		$header_name = '';
		$header_value = '';
		foreach ( $lines as $line ) {
			if ( preg_match("/^[ \t]/", $line) ) {
				if ( $header_name ) {
					$header_value .= $line;
				}
			} else {
				if ( $header_name && $header_value ) {
					$header_array[$header_name][] = trim(preg_replace("/[ \t][ \t]*/", " ", $header_value));
				}
				$header_name = strtolower(substr($line, 0, strpos($line, ':') ));
				$header_value = substr($line, strpos($line, ':') + 1);
			}
		}
		if ( $header_name && $header_value ) {
			$header_array[$header_name][] = trim(preg_replace("/[ \t][ \t]*/", " ", $header_value));
		}
		return $header_array;
	}
}
?>