<?php
/**
 *
 * @package blitze
 * @copyright (c) 2018 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\tags\services;

/**
 * @package SiteMaker Content Tags Service
 */
class tags
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $tags_table;

	/** @var string */
	protected $tags_data_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface			$db					Database connection
	 * @param string									$tags_table			Tags Table
	 * @param string									$tags_data_table	Tags Data Table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, $tags_table, $tags_data_table)
	{
		$this->db = $db;
		$this->tags_table = $tags_table;
		$this->tags_data_table = $tags_data_table;
	}

	/**
	 * @param int $topic_id
	 * @param array $tag_names
	 * @return void
	 */
	public function save($topic_id, array $tag_names)
	{
		// delete all tags for current topic_id
		$this->delete_tags_data($topic_id);

		// get ids of existing tags
		$tags = $this->get_tags_by_name($tag_names);

		// find non-existing tags
		$new_tag_names = array_diff($tag_names, $tags);

		// add non-existing tags
		$this->add_tags($new_tag_names);

		// get ids of newly added tags
		$tags += $this->get_tags_by_name($new_tag_names);

		// add tags for current topic_id
		$this->add_tags_data(array_keys($tags), $topic_id);
	}

	/**
	 * @param array $tag_names
	 * @return array
	 */
	public function get_tags_by_name(array $tag_names)
	{
		$tags = array();
		if (sizeof($tag_names))
		{
			$sql = 'SELECT * FROM ' . $this->tags_table . ' WHERE ' . $this->db->sql_in_set('tag_name', $tag_names);
			$result = $this->db->sql_query($sql);
	
			while ($row = $this->db->sql_fetchrow($result))
			{
				$tags[$row['tag_id']] = $row['tag_name'];
			}
			$this->db->sql_freeresult($result);
		}

		return $tags;
	}

	/**
	 * @param array $topic_ids
	 * @param string $field_name
	 * @return array
	 */
	public function get_field_tags_by_topic(array $topic_ids, $field_name)
	{
		$result = $this->db->sql_query($this->db->sql_build_query('SELECT', array(
			'SELECT'	=> 'd.tag_id, d.topic_id, t.tag_name, t.tag_colour',
			'FROM'		=> array(
				$this->tags_data_table	=> 'd',
				$this->tags_table		=> 't',
			),
			'WHERE'		=> 't.tag_id = d.tag_id
				AND ' . $this->db->sql_in_set('d.topic_id', array_map('intval', $topic_ids))
		)));

		$db_fields = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$db_fields[$row['topic_id']][$field_name][$row['tag_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return $db_fields;
	}

	/**
	 * Add new tags
	 * @param array $tag_names
	 * @return void
	 */
	public function add_tags(array $tag_names)
	{
		$sql_data = array();
		foreach ($tag_names as $tag)
		{
			$sql_data[] = array(
				'tag_name'		=> strtolower($tag),
				'tag_colour'	=> $this->get_color($tag),
			);
		}

		$this->insert($this->tags_table, $sql_data);
	}

	/** Delete specified tag ids
	 * @param array $tag_ids
	 * @return void
	 */
	public function delete_tags(array $tag_ids)
	{
		if (sizeof($tag_ids))
		{
			$this->db->sql_query('DELETE FROM ' . $this->tags_table . ' WHERE ' . $this->db->sql_in_set('tag_id', $tag_ids));
		}
	}

	/**
	 * Remove tags data for a particular topic
	 * @param int $topic_id
	 * @return void
	 */
	public function delete_tags_data($topic_id)
	{
		$this->db->sql_query('DELETE FROM ' . $this->tags_data_table . ' WHERE topic_id = ' . (int) $topic_id);
	}

	/**
	 * Find and remove tags that are not linked to any topic
	 * @return void
	 */
	public function delete_unused_tags()
	{
		$sql = $this->db->sql_build_query('SELECT', array(
			'SELECT'	=> 't.tag_id',
			'FROM'		=> array(
				$this->tags_table    => 't',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->tags_data_table => 'd'),
					'ON'	=> "d.tag_id = t.tag_id",
				)
			),
			'WHERE'		=> "d.tag_id IS NULL"
		));
		$result = $this->db->sql_query($sql);

		$tag_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$tag_ids[] = (int) $row['tag_id'];
		}
		$this->db->sql_freeresult($result);

		$this->delete_tags($tag_ids);
	}

	/**
	 * @param string $tag
	 * @return string
	 */
	public function get_color($tag)
	{
		return substr(md5($tag), 0, 6);
	}

	/**
	 * @param array $tags
	 * @param int $topic_id
	 * @return void
	 */
	protected function add_tags_data(array $tags, $topic_id)
	{
		$sql_data = array();
		foreach ($tags as $tag_id)
		{
			$sql_data[] = array(
				'tag_id'		=> $tag_id,
				'topic_id'		=> $topic_id,
			);
		}

		$this->insert($this->tags_data_table, $sql_data);
	}

	/**
	 * @param string $table
	 * @param array $data
	 * @return void
	 */
	protected function insert($table, array $data)
	{
		if (sizeof($data))
		{
			$this->db->sql_multi_insert($table, $data);
		}
	}
}
