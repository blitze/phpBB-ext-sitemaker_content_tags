<?php
/**
 *
 * SiteMaker Content Ratings. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Daniel A., https://github.com/blitze
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace blitze\tags\blocks;

/**
 * SiteMaker Content Tags Tag Cloud block.
 */
class cloud extends \blitze\sitemaker\blocks\wordgraph
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var\phpbb\language\language */
	protected $language;

	/** @var string */
	protected $tags_table;

	/** @var string */
	protected $tags_data_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface			$db     			Database connection
	 * @param \blitze\sitemaker\services\forum\data		$forum				Forum Data object
	 * @param string									$phpbb_root_path	phpBB root path
	 * @param string									$php_ext			phpEx
	 * @param integer									$cache_time			Cache results for given time
	 * @param \phpbb\controller\helper					$helper				Controller helper class
	 * @param \phpbb\language\language					$language			Language Object
	 * @param string									$tags_table			Tags Table
	 * @param string									$tags_data_table	Tags Data Table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \blitze\sitemaker\services\forum\data $forum, $phpbb_root_path, $php_ext, $cache_time, \phpbb\controller\helper $helper, \phpbb\language\language $language, $tags_table, $tags_data_table)
	{
		parent::__construct($db, $forum, $phpbb_root_path, $php_ext, $cache_time);

		$this->helper = $helper;
		$this->language = $language;
		$this->tags_table = $tags_table;
		$this->tags_data_table = $tags_data_table;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_config(array $settings)
	{
		return array(
			'legend1'			=> 'SETTINGS',
			'show_word_count'	=> array('lang' => 'TAGS_SHOW_TOPICS_COUNT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => false, 'default' => 0),
			'max_num_words'		=> array('lang' => 'TAGS_MAX_TAGS', 'validate' => 'int:0:255', 'type' => 'number:0:255', 'maxlength' => 2, 'explain' => false, 'default' => 15),
			'max_word_size'		=> array('lang' => 'TAGS_MAX_SIZE', 'validate' => 'int:0:55', 'type' => 'number:0:55', 'maxlength' => 2, 'explain' => false, 'default' => 25, 'append' => 'PIXEL'),
			'min_word_size'		=> array('lang' => 'TAGS_MIN_SIZE', 'validate' => 'int:0:20', 'type' => 'number:0:20', 'maxlength' => 2, 'explain' => false, 'default' => 9, 'append' => 'PIXEL'),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function display(array $bdata, $edit_mode = false)
	{
		$this->language->add_lang('common', 'blitze/tags');

		$block = parent::display($bdata, $edit_mode);

		$block['title'] = 'TAG_CLOUD';

		return $block;
	}

	/**
	 * @param array $settings
	 * @return array
	 */
	protected function get_custom_sql_array(array $settings)
	{
		return array(
			'SELECT'	=> array('g.tag_name as word_text, COUNT(d.topic_id) as word_count'),
			'FROM'		=> array(
				$this->tags_table	=> 'g',
				TOPICS_TABLE		=> 't',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->tags_data_table	=> 'd'),
					'ON'	=> 'd.tag_id = g.tag_id AND t.topic_id = d.topic_id',
				),
			),
			'GROUP_BY'	=> 'g.tag_name',
			'ORDER_BY'	=> 'word_count DESC'
		);
	}

	/**
	 * @inheritdoc
	 */
	protected function get_url($tag)
	{
		return $this->helper->route('blitze_content_filter', array(
			'filter_type'	=> 'tag',
			'filter_value'	=> urlencode($tag),
		));
	}
}
