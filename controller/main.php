<?php
/**
 *
 * @package phpBB Extension - GPC Whitelist
 * @copyright (c) 2014 Robet Heim
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace gpc\whitelist\controller;

class main
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\auth\auth */
	protected $auth;

	protected $request;

	protected $db;

	protected $phpEx;

	protected $phpbb_root_path;

	protected $table_prefix;

	protected $pagination;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\controller\helper			$helper
	 * @param \phpbb\template\template			$template
	 * @param \phpbb\user						$user
	 * @param \phpbb\auth\auth					$auth
	 * @param \phpbb\request\request			$request
	 * @param \phpbb\db\driver\driver_interface	$db
	 * @param string							$php_ext	phpEx
	 * @param string							$root_path	phpBB root path
	 * @param string							$table_prefix
	 * @param \phpbb\pagination					$pagination
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request $request, \phpbb\db\driver\driver_interface $db, $php_ext, $root_path, $table_prefix, $pagination)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->db = $db;
		$this->phpEx = $php_ext;
		$this->phpbb_root_path = $root_path;
		$this->table_prefix = $table_prefix;
		$this->pagination = $pagination;
	}

	/**
	 * Demo controller for route /whitelist
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function show()
	{
		// user permissions: only not ANONYMOUS and no Bots will see rate-block
		// Can this user view profiles/memberlist?
		if ($this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			$this->template->assign_var('S_VIEW_RATE_BLOCK', true);
		}
		$mode = $this->request->variable('mode', '');

		if ($mode == 'rate_trade')
		{
			// user permissions: only not ANONYMOUS and no Bots!
			// Can this user view profiles/memberlist?
			if (!$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
			{
				if ($this->user->data['user_id'] != ANONYMOUS)
				{
					trigger_error('NO_VIEW_USERS');
				}
				login_box('', $this->user->lang('LOGIN'));
			}
			// try to rate

			$this->template->assign_var('S_RATED', true);

			$rating = $this->request->variable('rating', '');
			$comment = utf8_normalize_nfc($this->request->variable('comment', '', true));

			if (!in_array($rating, array('pos', 'neut', 'neg')))
			{
				$this->template->assign_var('ERROR_MSG', $this->user->lang('USE_FORM'));
			}
			else
			{
				if ($rating == 'neg' && $comment == "")
				{
					$this->template->assign_var('ERROR_MSG', $this->user->lang('NEED_COMMENT_ON_NEG_RATING'));
				}
				else
				{
					// user_id des tradepartners aus USERS_TABLE
					$tradepartner = $this->request->variable('tradepartner', '', true);
					$result = $this->db->sql_query("SELECT user_id, username
							FROM " .USERS_TABLE. "
							WHERE UCASE(username_clean) = UCASE('". $this->db->sql_escape(utf8_clean_string($tradepartner)) ."')
							LIMIT 1; ");
					$row = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);
					if (!$row)
					{
						// trade-partner does not exist
						$this->template->assign_var('ERROR_MSG', $this->user->lang('TRADE_PARTNER_DOES_NOT_EXIST', $tradepartner));
					}
					else
					{
						$tradepartner_id = (int) $row['user_id'];

						// keine Eigenwertung:
						if ($tradepartner_id == $this->user->data['user_id'])
						{
							$this->template->assign_var('ERROR_MSG', $this->user->lang('NO_SELF_RATING'));
						}
						else
						{
							// kein flood-rating:
							$time_out = 604800; // 7 days = 7*60*60*24
							$max_rates_in_time = 2; // 2 ratings for the same user within $time_out

							$result = $this->db->sql_query("SELECT COUNT(rater_id) AS rates_in_time
									FROM {$this->table_prefix}whitelist_ratings
									WHERE rater_id = ". $this->db->sql_escape((int)$this->user->data['user_id']) .'
											AND trader_id = '. $this->db->sql_escape((int)$tradepartner_id) .'
													AND rate_time > '.(int)(time()-$time_out).';');
							$rates_in_time = (int) $this->db->sql_fetchfield('rates_in_time');
							$this->db->sql_freeresult($result);

							if ($rates_in_time >= $max_rates_in_time)
							{
								// 60*60*24 = 86400
								$this->template->assign_var('ERROR_MSG', $this->user->lang('NO_TRADE_FLOODING', $max_rates_in_time, $time_out/86400));
							}
							else
							{
								// rating-column
								$col = 'positiv';
								$rating_num = 0;
								$rating_word = "";
								switch ($rating)
								{
									case 'pos':
										$col = 'whitelist_positiv';
										$rating_num = 1;
										$rating_word = $this->user->lang('POSITIV');
										$rating = '<span style="color: green">'.$rating_word.'</span>';
										$rating_bbcode = '[color=green]'.$rating_word.'[/color]';
										break;
									case 'neg':
										$col = 'whitelist_negativ';
										$rating_num = -1;
										$rating_subject = $this->user->lang('NEGATIV');
										$rating = '<span style="color: red">'.$rating_word.'</span>';
										$rating_bbcode = '[color=red]'.$rating_word.'[/color]';
										break;
									case 'neut':
									default:
										$col = 'whitelist_neutral';
										$rating_num = 0;
										$rating_subject = $this->user->lang('NEUTRAL');
										$rating = '<span style="color: grey">'.$rating_word.'</span>';
										$rating_bbcode = '[color=grey]'.$rating_word.'[/color]';
										break;
								}


								// try updating
								$sql = "UPDATE ".USERS_TABLE."
								SET $col=$col+1
								WHERE user_id = ". $this->db->sql_escape((int)$tradepartner_id) .';';
								$result = $this->db->sql_query($sql);

								if (!$result)
								{
									$team_link = append_sid("memberlist.".$this->phpEx."?mode=team");
									$this->template->assign_var('ERROR_MSG', $this->user->lang('TRADE_RATED_FAIL', $tradepartner, $team_link));
								}
								else
								{
									// user is not self-rating, not flooding and we rated the trade.
									// now safe rating protocol:
									$this->db->sql_query("INSERT INTO {$this->table_prefix}whitelist_ratings (rater_id, trader_id, rating, rate_time, comment)
									VALUES (". $this->db->sql_escape((int)$this->user->data['user_id']) .',
											'. $this->db->sql_escape((int)$tradepartner_id) .',
													'. (int)$rating_num .',
															'. (int)time() .',
																	\''. $this->db->sql_escape($comment) .'\');');

									// and notify PM to recipient of rating:
									require_once($this->phpbb_root_path . 'includes/functions_privmsgs.' . $this->phpEx);

									// note that multibyte support is enabled here
									$my_subject = utf8_normalize_nfc($this->user->lang('WHITELIST_PM_GOT_ENTRY_SUBJECT', $rating_word));
									$my_text = utf8_normalize_nfc($this->user->lang('WHITELIST_PM_GOT_ENTRY_MSG', $this->user->data['username'], $rating_word));

									// variables to hold the parameters for submit_pm
									$uid = $bitfield = $options = '';
									generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
									generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);

									$data = array(
											'address_list'      => array ('u' => array((int)$tradepartner_id => 'to')),
											'from_user_id'      => (int)$this->user->data['user_id'],
											'from_username'     => $this->user->data['username'],
											'icon_id'           => 0,
											'from_user_ip'      => $this->user->data['user_ip'],

											'enable_bbcode'     => true,
											'enable_smilies'    => true,
											'enable_urls'       => true,
											'enable_sig'        => true,

											'message'           => $my_text,
											'bbcode_bitfield'   => $bitfield,
											'bbcode_uid'        => $uid,
									);

									submit_pm('post', $my_subject, $data, false);
										
									// template
									$this->template->assign_vars(array(
											'S_RATED_SUCCESS' => true,
											'TRADE_RATED_SUCCESS_MSG' => $this->user->lang('TRADE_RATED_SUCCESS', $tradepartner, $rating),
									));
								}
							}
						}
					}
				}
			}
		}

		if ($mode == 'view_rating')
		{
			$this->template->assign_var('S_VIEW_RATING', true);
			// get user data by trader_id oder username_clean
			$trader_id = $this->request->variable('trader_id', 0);
			$where="";
			if ($trader_id != 0)
			{
				// user data by trader_id
				$where="WHERE user_id = ". $this->db->sql_escape((int)$trader_id);
			}
			else
			{
				// user data by username_clean
				$trader = $this->request->variable('trader', '', true);
				$where="WHERE UCASE(username_clean) = UCASE('". $this->db->sql_escape(utf8_clean_string($trader)) ."')";
			}
			$result = $this->db->sql_query("SELECT user_id, username, whitelist_positiv, whitelist_neutral, whitelist_negativ
					FROM " .USERS_TABLE. "
					$where
					LIMIT 1; ");
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if (!$row)
			{
				// user does not exist
				$this->template->assign_var('ERROR_MSG', $this->user->lang('TRADE_PARTNER_DOES_NOT_EXIST', $trader));
			}
			else
			{
				$trader_id = $row['user_id'];
				$trader = $row['username'];
				$trader_data = $row;
					
				// gesamt Anzahl der Ratings anzeigen
				$this->template->assign_vars(array(
						'S_TRADER_EXISTS'		=> true,
						'VIEW_RATING_USER_ID'	=> $trader_data['user_id'],
						'VIEW_RATING_USERNAME'	=> $trader,
						'VIEW_RATING_PROFILE'	=> append_sid("{$this->phpbb_root_path}memberlist.".$this->phpEx."?mode=viewprofile&amp;u=".$trader_data['user_id']),
						'VIEW_RATING_POSITIV'	=> $trader_data['whitelist_positiv'],
						'VIEW_RATING_NEUTRAL'	=> $trader_data['whitelist_neutral'],
						'VIEW_RATING_NEGATIV'	=> $trader_data['whitelist_negativ'],
				));
				// ratings auflisten
				$sql = 'SELECT wr.*, u.username AS rater
						FROM '. USERS_TABLE ." u, {$this->table_prefix}whitelist_ratings wr
						WHERE wr.trader_id = {$trader_data['user_id']}
				AND wr.rater_id = u.user_id
				ORDER BY wr.rate_time DESC";
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					switch ($row['rating'])
					{
						case '1':
							$row['rating'] = '<span style="color: green">+1</span>';
							break;
						case '0':
							$row['rating'] = '<span style="color: grey"> 0</span>';
							break;
						case '-1':
							$row['rating'] = '<span style="color: red">-1</span>';
							break;
						default:
							$row['rating'] = '<span style="color: grey">?</span>';
					}
					$delete_link = $this->helper->route("gpc_whitelist_controller",	array(
							'mode' => 'delete_rating',
							'trade_id' => $row['trade_id'],
					));
					$delete_rating_link = '<a  onclick="return confirm(\''.$this->user->lang('DELETE_CONFIRM').'\')" href="'.$delete_link.'" />'.$this->user->lang('DELETE').'</a>';

					$this->template->assign_block_vars('ratings', array(
							'TIME'				=> $this->user->format_date($row['rate_time']),
							'RATERNAME'			=> $row['rater'],
							'TRADERNAME'		=> $trader,
							'RATING'			=> $row['rating'],
							'COMMENT'			=> $row['comment'],
							'PROFILE_FROM'		=> append_sid("{$this->phpbb_root_path}memberlist.".$this->phpEx."?mode=viewprofile&amp;u=".$row['rater_id']),
							'PROFILE_TRADER'	=> append_sid("{$this->phpbb_root_path}memberlist.".$this->phpEx."?mode=viewprofile&amp;u=".$trader_data['user_id']),
							'ACTIONS'			=> $delete_rating_link,
					));
				}
			}
		}

		//if (($this->auth->acl_get('a_') || $this->auth->acl_getf_global('m_')) && $mode == 'delete_rating')
		if ($this->auth->acl_get('a_') && $mode == 'delete_rating')
		{
			$this->template->assign_var('S_DELETE_RATING', true);
				
			$trade_id = $this->request->variable('trade_id', 0);
				
			if ($trade_id == 0)
			{
				$this->template->assign_var('DELETE_RATING_ERROR_MSG', $this->user->lang('TRADE_DOES_NOT_EXIST', $trade_id));
			}
			else
			{
				// Trade-Infos aus DB holen
				$result = $this->db->sql_query("SELECT *
						FROM {$this->table_prefix}whitelist_ratings
						WHERE trade_id = ". $this->db->sql_escape((int)$trade_id) ."
								LIMIT 1;");
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);
				if (!$row)
				{
					// trade does not exist
					$this->template->assign_var('DELETE_RATING_ERROR_MSG', $this->user->lang('TRADE_DOES_NOT_EXIST', $trade_id));
				}
				else
				{
					// rating-column
					$cols = Array(-1=>'whitelist_negativ', 0=>'whitelist_neutral', 1=>'whitelist_positiv');

					if (!array_key_exists((int)($row['rating']), $cols))
					{
						$this->template->assign_var('DELETE_RATING_ERROR_MSG', $this->user->lang('TRADE_DELETE_FAIL').' (col_error)');
					}
					else
					{
						$col = $cols[(int)$row['rating']];

						// delete from whitelist_ratings
						$result = $this->db->sql_query("DELETE FROM {$this->table_prefix}whitelist_ratings
						WHERE trade_id = ". $this->db->sql_escape((int)$trade_id) ."
								LIMIT 1;");
						if (!$result)
						{
							$this->template->assign_var('DELETE_RATING_ERROR_MSG', $this->user->lang('TRADE_DELETE_FAIL'));
						}
						else
						{
							// and update whitelist_users
							$result = $this->db->sql_query("UPDATE ".USERS_TABLE."
									SET $col=$col-1
									WHERE user_id = ". $this->db->sql_escape((int)$row['trader_id']) .';');
							if (!$result)
							{
								$this->template->assign_var('DELETE_RATING_ERROR_MSG', $this->user->lang('TRADE_DELETE_FAIL'));
							}
							else
							{
								$this->template->assign_vars(array(
										'S_DELETE_RATING_SUCCESS' => true,
										'DELETE_RATING_SUCCESS_MSG' => $this->user->lang('TRADE_DELETE_SUCCESS'),
								));
							}
						}
					}
				}
			}
		}

		$this->template->assign_vars(array(
				'RATE_TRADE_ACTION' => $this->helper->route("gpc_whitelist_controller",	array(
						'mode' => 'rate_trade',
				)),
				'U_FIND_USERNAME' => append_sid("./memberlist.".$this->phpEx."?mode=searchuser&amp;form=rate_trade_form&amp;field=user&amp;select_single=1"),
				'VIEW_RATING_ACTION' => $this->helper->route("gpc_whitelist_controller",	array(
						'mode' => 'view_rating',
				)),
				'U_FIND_TRADER' => append_sid("./memberlist.".$this->phpEx."?mode=searchuser&amp;form=show_rating_form&amp;field=trader&amp;select_single=1"),
				'LINK_LAST_RATINGS' => $this->helper->route("gpc_whitelist_controller",	array(
						'mode' => 'last_ratings',
				)),
				'LINK_SHOW_WHITELIST' => $this->helper->route("gpc_whitelist_controller",	array(
						'mode' => 'show_whitelist',
				)),
		));

		// last ratings
		if (in_array($mode, array('', 'rate_trade', 'last_ratings')))
		{
			$this->template->assign_var('S_LAST_RATINGS', true);
				
			$last_ratings_limit = 10;
			$last_ratings_start = ($mode=='last_ratings') ? $this->request->variable('start', 0) : 0;

			// get total_ratings
			$sql = "SELECT COUNT(*) AS total_ratings
					FROM {$this->table_prefix}whitelist_ratings";
			$result = $this->db->sql_query($sql);
			$total_ratings = (int) $this->db->sql_fetchfield('total_ratings');
			$this->db->sql_freeresult($result);

			$pagination = $this->pagination;
			$base_url = $this->helper->route("gpc_whitelist_controller",	array(
							'mode' => 'last_ratings',
					));
			$pagination->generate_template_pagination($base_url, 'PAGINATION_LAST_RATINGS', 'start', $total_ratings, $last_ratings_limit, $last_ratings_start);
			$this->template->assign_vars(array(
					'ON_PAGE_LAST_RATINGS'      => $pagination->on_page($total_ratings, $last_ratings_limit, $last_ratings_start),
			));

			$sql = 'SELECT wr.*, u.username AS trader, u.whitelist_positiv, u.whitelist_neutral, u.whitelist_negativ, u2.username AS rater
					FROM '. USERS_TABLE .' u, '. USERS_TABLE ." u2, {$this->table_prefix}whitelist_ratings wr
					WHERE wr.trader_id = u.user_id
					AND wr.rater_id = u2.user_id
					ORDER BY wr.rate_time DESC
					LIMIT $last_ratings_start, $last_ratings_limit";
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				switch ($row['rating'])
				{
					case '1':
						$row['rating'] = '<span style="color: green">+1</span>';
						break;
					case '0':
						$row['rating'] = '<span style="color: grey"> 0</span>';
						break;
					case '-1':
						$row['rating'] = '<span style="color: red">-1</span>';
						break;
					default:
						$row['rating'] = '<span style="color: grey">?</span>';
				}
				$this->template->assign_block_vars('last_ratings', array(
						'TIME'				=> $this->user->format_date($row['rate_time']),
						'RATERNAME'			=> $row['rater'],
						'TRADERNAME'		=> $row['trader'],
						'RATING'			=> $row['rating'],
						'COMMENT'			=> $row['comment'],
						'PROFILE_FROM'		=> append_sid("{$this->phpbb_root_path}memberlist.".$this->phpEx."?mode=viewprofile&amp;u=".$row['rater_id']),
						'PROFILE_TRADER'	=> append_sid("{$this->phpbb_root_path}memberlist.".$this->phpEx."?mode=viewprofile&amp;u=".$row['trader_id']),
						'POSITIV'			=> $row['whitelist_positiv'],
						'NEUTRAL'			=> $row['whitelist_neutral'],
						'NEGATIV'			=> $row['whitelist_negativ'],
				));
			}
		}

		// get whitelist
		if (in_array($mode, array('', 'show_whitelist')))
		{
			$this->template->assign_var('S_SHOW_WHITELIST', true);
				
			$limit = $this->config['topics_per_page'];
			//$start = ($mode=='show_whitelist') ? $this->request->variable('start', 0) : 0;
			$start = $this->request->variable('start', 0);

			// only normal users and founder but not ignored (bots, anonymous) nor inactive
			$sql_where = "WHERE user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ")";

			// get total_users
			$sql = "SELECT COUNT(user_id) AS total_users
					FROM ".USERS_TABLE."
					$sql_where";
			$result = $this->db->sql_query($sql);
			$total_users = (int) $this->db->sql_fetchfield('total_users');
			$this->db->sql_freeresult($result);

			$base_url = $this->helper->route("gpc_whitelist_controller",	array(
							'mode' => 'show_whitelist',
					));
			$pagination = $this->pagination;
			$pagination->generate_template_pagination($base_url, 'PAGINATION', 'start', $total_users, $limit, $start);
			$this->template->assign_vars(array(
					'ON_PAGE'      => $pagination->on_page($total_users, $limit, $start),
			));

			$sql = 'SELECT user_id, username, whitelist_positiv, whitelist_neutral, whitelist_negativ
					FROM '. USERS_TABLE ."
					$sql_where
					ORDER BY username_clean
					LIMIT $start, $limit";
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('whitelist', array(
						'USER_ID'	=> $row['user_id'],
						'USERNAME'	=> $row['username'],
						'PROFILE'	=> append_sid("{$this->phpbb_root_path}memberlist.".$this->phpEx."?mode=viewprofile&amp;u=".$row['user_id']),
						'POSITIV'	=> $row['whitelist_positiv'],
						'NEUTRAL'	=> $row['whitelist_neutral'],
						'NEGATIV'	=> $row['whitelist_negativ'],
				));
			}
		}
		return $this->helper->render('whitelist.html');
	}
}
