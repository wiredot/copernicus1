<?php

function smarty_modifier_translate_group( $meta, $key ) {
	$return = '';

	if ( isset( $meta[ $key . LANGUAGE_SUFFIX ] ) && $meta[ $key . LANGUAGE_SUFFIX ] ) {
		$return = $meta[ $key . LANGUAGE_SUFFIX ];
	} else {
		$return = $meta[ $key ];
	}

	return $return;
}
