<?php
/**
*
* @package phpBB Extension - GPC Whitelist
* @copyright (c) 2014 Robet Heim
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace gpc\whitelist\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.page_header'						=> 'add_page_header_link',
			'core.memberlist_prepare_profile_data'	=> 'memberlist_prepare_profile_data',
		);
	}

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper	$helper		Controller helper object
	* @param \phpbb\template			$template	Template object
	*/
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template)
	{
		$this->helper = $helper;
		$this->template = $template;
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'gpc/whitelist',
			'lang_set' => 'whitelist',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function add_page_header_link($event)
	{
		$this->template->assign_vars(array(
			'U_WHITELIST'	=> $this->helper->route('gpc_whitelist_controller', array('action' => 'show')),
		));
	}

	public function memberlist_prepare_profile_data($event) {
		$data = $event->get_data();
		$template_data = $event['template_data'];

		$template_data['WHITELIST_POSITIV']			= $data['data']['whitelist_positiv'];
		$template_data['WHITELIST_NEUTRAL']			= $data['data']['whitelist_neutral'];
		$template_data['WHITELIST_NEGATIV']			= $data['data']['whitelist_negativ'];
		$template_data['WHITELIST_RATINGS_LINK']	= append_sid("app.php/whitelist?mode=view_rating&trader_id=".$data['data']['user_id']);

		$event['template_data'] = $template_data;
	}
}
