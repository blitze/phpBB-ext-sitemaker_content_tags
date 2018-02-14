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
	'TAGS_FIELD'									=> 'Tags',
	'TAGS_DISPLAY'									=> 'Display style',
	'TAGS_DISPLAY_LIST'								=> 'Comma-separated list',
	'TAGS_DISPLAY_BUTTON'							=> 'Button',
	'TAGS_DISPLAY_LABEL'							=> 'Label',
	'TAGS_LABEL_COLOUR'								=> 'Label Colour',
	'TAGS_LABEL_COLOUR_DANGER'						=> 'Danger',
	'TAGS_LABEL_COLOUR_DYNAMIC'						=> 'Dynamic',
	'TAGS_LABEL_COLOUR_GRAYSCALE'					=> 'Grayscale',
	'TAGS_LABEL_COLOUR_INFO'						=> 'Info',
	'TAGS_LABEL_COLOUR_NONE'						=> 'None',
	'TAGS_LABEL_COLOUR_PRIMARY'						=> 'Primary',
	'TAGS_LABEL_COLOUR_SECONDARY'					=> 'Secondary',
	'TAGS_LABEL_COLOUR_SUCCESS'						=> 'Success',
	'TAGS_LABEL_COLOUR_WARNING'						=> 'Warning',
	'TAGS_MAX_TAGS'									=> 'Maximum number of tags',
	'TAGS_MAX_TAGS_EXPLAIN'							=> 'Set to 0 to have unlimited number of tags',
	'TAGS_RELATED_CONTENTS_ALL_TYPES'				=> 'Related Contents (all)',
	'TAGS_RELATED_CONTENTS_ALL_TYPES_EXPLAIN'		=> 'Related topics across all content types based on Tags',
	'TAGS_RELATED_CONTENTS_SAME_TYPE'				=> 'Related Contents (type)',
	'TAGS_RELATED_CONTENTS_SAME_TYPE_EXPLAIN'		=> 'Related topics in this content type based on Tags',
));
