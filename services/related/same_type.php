<?php
/**
 *
 * SiteMaker Content Tags. An extension for the phpBB Forum Software package.
 * @copyright (c) 2018 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\tags\services\related;

class same_type extends base
{
	/**
	 * @inheritdoc
	 */
	public function get_name()
	{
		return 'tags_same_type';
	}

	/**
	 * @inheritdoc
	 */
	public function get_langname()
	{
		return 'TAGS_RELATED_CONTENTS_SAME_TYPE';
	}

	/**
	 * @inheritdoc
	 */
	protected function get_title(\blitze\content\model\entity\type $entity)
	{
		return $this->language->lang('TAGS_RELATED_CONTENT_TYPE', $entity->get_content_langname());
	}

	/**
	 * @inheritdoc
	 */
	protected function get_topics(\blitze\content\model\entity\type $entity, $topics_data, $posts_data, $users_cache, $topic_tracking_info, array &$image_field)
	{
		$content_type = $entity->get_content_name();
		$image_field = $this->get_image_field($entity);

		$this->fields->prepare_to_show($entity, array_keys($topics_data), $image_field, '', 'summary');

		$attachments = $topics = $update_count = array();
		foreach ($topics_data as $topic_id => $topic_data)
		{
			$post_data	= array_shift($posts_data[$topic_id]);
			$topics[] = $this->fields->show($content_type, $topic_data, $post_data, $users_cache, $attachments, $update_count, $topic_tracking_info);
		}

		return $topics;
	}

	/**
	 * @inheritdoc
	 */
	protected function build_query(array $topic_data)
	{
		$this->forum->query()
			->fetch_forum($topic_data['forum_id'])
			->fetch_custom($this->get_sql_array($topic_data['topic_id']))
			->build(true, true, false);
	}
}
