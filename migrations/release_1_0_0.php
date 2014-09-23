<?php
/**
*
* @package phpBB Extension - GPC Whitelist
* @copyright (c) 2014 Robet Heim
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace gpc\whitelist\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
                $this->table_prefix . 'users'   => array(
                    'whitelist_positiv'	        => array('UINT', 0),
                    'whitelist_neutral'	        => array('UINT', 0),
                    'whitelist_negativ'	        => array('UINT', 0),
                ),
            ),
			'add_tables'    => array(
	            $this->table_prefix . 'whitelist_ratings' => array(
	                'COLUMNS'	=> array(
	                    'trade_id'	=> array('UINT', NULL, 'auto_increment'),
	                    'rater_id'	=> array('UINT', NULL),
	                    'trader_id'	=> array('UINT', NULL),
	                    'rating'	=> array('TINT:2', NULL),
	                    'rate_time'	=> array('TIMESTAMP', NULL),
	                    'comment'	=> array('VCHAR:255', ''),
	                ),
	                'PRIMARY_KEY'	=> 'trade_id',
	            ),
			),
		);
	}

	public function revert_schema()
	{
	    return array(
			'drop_columns'	=> array(
                $this->table_prefix . 'users' => array(
                    'whitelist_positiv',
                    'whitelist_neutral',
                    'whitelist_negativ',
                ),
            ),
			'drop_tables'    => array(
				$this->table_prefix . 'whitelist_ratings'
			),
		);
	}
}
