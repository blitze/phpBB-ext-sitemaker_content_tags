<?php
/**
 *
 * SiteMaker Content Tags. An extension for the phpBB Forum Software package.
 * @copyright (c) 2018 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\tags\services\related;

class all_types extends base
{
	/** @var \blitze\content\services\types */
	protected $types;

	/**
	 * Constructor
	 *
	 * @param \phpbb\language\language					$language			Language Object
	 * @param \phpbb\template\template					$template			Template object
	 * @param \blitze\content\services\fields			$fields				Content fields object
	 * @param \blitze\sitemaker\services\forum\data		$forum				Forum Data object
	 * @param string									$tags_data_table	Tags Data Table
	 * @param \blitze\content\services\types			$types				Content types object
	*/
	public function __construct(\phpbb\language\language $language, \phpbb\template\template $template, \blitze\content\services\fields $fields, \blitze\sitemaker\services\forum\data $forum, $tags_data_table, \blitze\content\services\types $types)
	{
		parent::__construct($language, $template, $fields, $forum, $tags_data_table);

		$this->types = $types;
	}

	/**
	 * @inheritdoc
	 */
	public function get_name()
	{
		return 'tags_all_types';
	}

	/**
	 * @inheritdoc
	 */
	public function get_langname()
	{
		return 'TAGS_RELATED_CONTENTS_ALL_TYPES';
	}

	/**
	 * @inheritdoc
	 */
	protected function get_title(\blitze\content\model\entity\type $entity)
	{
		return $this->language->lang('TAGS_RELATED_CONTENTS');
	}

	/**
	 * @inheritdoc
	 */
	protected function get_topics(\blitze\content\model\entity\type $entity, $topics_data, $posts_data, $users_cache, $topic_tracking_info, array &$image_fields)
	{
		$this->fields->prepare_to_show($entity, array_keys($topics_data), $image_fields, '', 'summary');

		$attachments = $topics = $update_count = array();
		foreach ($topics_data as $topic_id => $topic_data)
		{
			$content_type = $this->types->get_forum_type($topic_data['forum_id']);
			if (!$content_type || !($entity = $this->types->get_type($content_type)))
			{
				continue;
			}

			$this->init_fields($entity, $image_fields);
			$post_data = array_shift($posts_data[$topic_id]);
			$topics[] = $this->fields->show($content_type, $topic_data, $post_data, $users_cache, $attachments, $update_count, $topic_tracking_info);
		}
		$image_fields = array_filter($image_fields);

		return $topics;
	}

	/**
	 * @inheritdoc
	 */
	protected function build_query(array $topic_data)
	{
		$this->forum->query()
			->fetch_custom($this->get_sql_array($topic_data['topic_id']))
			->build(true, true, false);
	}

	/**
	 * @param \blitze\content\model\entity\type $entity
	 * @param array $image_fields
	 * @return void
	 */
	protected function init_fields(\blitze\content\model\entity\type $entity, array &$image_fields)
	{
		$fields = $this->get_image_field($entity);

		$this->fields->set_content_type($entity->get_content_name());
		$this->fields->set_form_fields($fields);
		$this->fields->set_content_fields($fields, $entity->get_content_fields());

		$image_fields += $fields;
	}
}
