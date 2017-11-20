<?php

function smarty_modifier_html( $string ) {
	return apply_filters( 'the_content', $string );
}
