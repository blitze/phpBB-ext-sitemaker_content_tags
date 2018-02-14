<?php
/**
 *
 * SiteMaker Content Tags. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Daniel A., https://github.com/blitze
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace blitze\tags\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * SiteMaker Content Tags Field Event listener.
 */
class field implements EventSubscriberInterface
{
	/** @var \blitze\tags\services\tags */
	protected $tags;

	/**
	 * Constructor
	 *
	 * @param \blitze\tags\services\tags	$tags
	 */
	public function __construct(\blitze\tags\services\tags $tags)
	{
		$this->tags = $tags;
	}

	/**
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'blitze.content.fields.set_values'			=> 'set_topics_tags',
			'blitze.content.builder.set_field_values'	=> 'set_form_field_values',
			'core.delete_post_after'					=> 'cleanup',
		);
	}

	/**
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function set_topics_tags(\phpbb\event\data $event)
	{
		$tags_fields = $this->get_tag_fields($event['view_mode_fields']);

		if (sizeof($tags_fields) && sizeof($event['db_fields']))
		{
			$field_tags = array();
			foreach ($tags_fields as $field)
			{
				$event['db_fields'] = array_replace_recursive($event['db_fields'], $this->tags->get_field_tags_by_topic(array_keys($event['db_fields']), $field));
			}
		}
	}

	/**
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function set_form_field_values(\phpbb\event\data $event)
	{
		$tags_fields = $this->get_tag_fields($event['entity']->get_field_types());
		foreach ($tags_fields as $field)
		{
			$tags = $this->tags->get_field_tags_by_topic(array($event['topic_id']), $field);

			if (isset($tags[$event['topic_id']]))
			{
				$fields_data = $event['fields_data'];
				$tags = array_shift($tags)[$field];

				foreach ($tags as $row)
				{
					$fields_data[$field]['field_value'][] = $row['tag_name'];
				}

				$event['fields_data'] = $fields_data;
			}
		}
	}

	/**
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function cleanup(\phpbb\event\data $event)
	{
		if ($event['post_mode'] === 'delete_topic' && !$event['is_soft'])
		{
			$this->tags->delete_tags_data($event['topic_id']);
			$this->tags->delete_unused_tags();
		}
	}

	/**
	 * @param array $fields
	 * @return array
	 */
	protected function get_tag_fields(array $fields)
	{
		return array_keys($fields, 'tags');
	}
}
