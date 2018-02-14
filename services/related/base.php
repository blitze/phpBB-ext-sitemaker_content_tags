<?php
/**
 *
 * SiteMaker Content Tags. An extension for the phpBB Forum Software package.
 * @copyright (c) 2018 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\tags\services\related;

abstract class base implements \blitze\content\services\topic\driver\block_interface
{
	/** @var\phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/* @var \blitze\content\services\fields */
	protected $fields;

	/** @var \blitze\sitemaker\services\forum\data */
	protected $forum;

	/** @var string */
	protected $tags_data_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\language\language					$language			Language Object
	 * @param \phpbb\template\template					$template			Template object
	 * @param \blitze\content\services\fields			$fields				Content fields object
	 * @param \blitze\sitemaker\services\forum\data		$forum				Forum Data object
	 * @param string									$tags_data_table	Tags Data Table
	*/
	public function __construct(\phpbb\language\language $language, \phpbb\template\template $template, \blitze\content\services\fields $fields, \blitze\sitemaker\services\forum\data $forum, $tags_data_table)
	{
		$this->language = $language;
		$this->template = $template;
		$this->fields = $fields;
		$this->forum = $forum;
		$this->tags_data_table = $tags_data_table;
	}

	/**
	 * @param \blitze\content\model\entity\type $entity
	 * @param array $topic_data
	 * @param array $post_data
	 * @param array $user_cache
	 * @return void
	 */
	public function show_block(\blitze\content\model\entity\type $entity, array $topic_data, array $post_data, array $user_cache)
	{
		$this->language->add_lang('common', 'blitze/tags');

		$this->build_query($topic_data);
		$topics_data = $this->forum->get_topic_data($this->max_topics);
		$posts_data = $this->forum->get_post_data('first');
		$users_cache = $this->forum->get_posters_info();
		$topic_tracking_info = $this->forum->get_topic_tracking_info($topic_data['forum_id']);

		$image_field = array();
		$this->template->assign_block_vars('topic_blocks', array(
			'TITLE'		=> $this->get_title($entity),
			'TOPICS'	=> $this->get_topics($entity, $topics_data, $posts_data, $users_cache, $topic_tracking_info, $image_field),
			'TPL_NAME'	=> '@blitze_tags/related_contents.html',
			'HAS_IMG'	=> (bool) $image_field,
		));
	}

	/**
	 * @param \blitze\content\model\entity\type $entity
	 * @return array
	 */
	protected function get_image_field(\blitze\content\model\entity\type $entity)
	{
		$field_types = $entity->get_field_types();
		$image_field = array_slice(array_keys($field_types, 'image'), 0, 1);

		return array_intersect_key($field_types, array_flip($image_field));
	}

	/**
	 * @param int $topic_id
	 * @return string
	 */
	protected function get_sql_array($topic_id)
	{
		return array(
			'FROM'		=> array(
				$this->tags_data_table => 'd1',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->tags_data_table	=> 'd2'),
					'ON'	=> 'd2.tag_id = d1.tag_id AND d2.topic_id <> d1.topic_id',
				),
			),
			'GROUP_BY'	=> 'topic_id',
			'WHERE'		=> array(
				'd2.topic_id = t.topic_id',
				'd1.topic_id = ' . (int) $topic_id
			),
		);
	}
}
