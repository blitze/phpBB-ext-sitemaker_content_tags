<?php
/**
 *
 * @package blitze
 * @copyright (c) 2018 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\tags\controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package SiteMaker Content Tags Controller
 */
class search
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var string */
	protected $tags_table;

	/** @var string */
	protected $tags_data_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface			$db					Database connection
	 * @param \phpbb\request\request_interface			$request			Request object
	 * @param string									$tags_table			Tags Table
	 * @param string									$tags_data_table	Tags Data Table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\request\request_interface $request, $tags_table, $tags_data_table)
	{
		$this->db = $db;
		$this->request = $request;
		$this->tags_table = $tags_table;
		$this->tags_data_table = $tags_data_table;
	}

	/**
	 * @param string $action
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle($action)
	{
		if ($this->request->is_ajax() === false)
		{
			redirect(generate_board_url(), $this->return_url);
		}

		$term = $this->request->variable('term', '', true);

		$sql = 'SELECT * 
			FROM ' . $this->tags_table . '
			WHERE tag_name ' . $this->db->sql_like_expression($this->db->get_any_char() . $term . $this->db->get_any_char());
		$result = $this->db->sql_query($sql);

		$tags = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$tags[] = $row['tag_name'];
		}
		$this->db->sql_freeresult($result);

		return new JsonResponse($tags);
	}
}
