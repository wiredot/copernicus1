<?php

function smarty_modifier_translate( $string, $group = null, $language = null ) {
	global $CP_Translation;
	return $CP_Translation->translate( $string, $group, $language );
}
