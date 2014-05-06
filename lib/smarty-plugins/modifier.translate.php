<?php

function smarty_modifier_translate($string) {
	global $CP_Translation;
	
	return $CP_Translation->translate($string);
}