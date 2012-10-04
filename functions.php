<?php

function __($text)
{
	if(empty($text))
		return '';
	else
		return gettext($text);
}