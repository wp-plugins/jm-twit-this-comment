<?php

namespace TokenToMe\tweet_this;

class Utilities {

	/**
	 * @param $at
	 *
	 * @return bool|mixed
	 */
	static function remove_at( $at ) {

		if ( ! is_string( $at ) ) {
			return false;
		}

		$noat = str_replace( '@', '', $at );

		return $noat;
	}
}