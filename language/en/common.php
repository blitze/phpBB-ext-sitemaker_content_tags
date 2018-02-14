<?php
/**
 *
 * SiteMaker Content Tags. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Daniel A., https://github.com/blitze
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'TAG_CLOUD'						=> 'Tag Cloud',
	'TAGS_RELATED_CONTENTS'			=> 'Related Contents',
	'TAGS_RELATED_CONTENT_TYPE'		=> 'You might also like',
));
