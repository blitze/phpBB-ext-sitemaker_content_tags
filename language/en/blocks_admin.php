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
	'BLITZE_TAGS_BLOCK_CLOUD'	=> 'Tag Cloud',

	'TAGS_MAX_SIZE'				=> 'Maximum font size',
	'TAGS_MAX_TAGS'				=> 'Maximum number of tags',
	'TAGS_MIN_SIZE'				=> 'Minimum font size',
	'TAGS_SHOW_TOPICS_COUNT'	=> 'Show topics count?',
));
