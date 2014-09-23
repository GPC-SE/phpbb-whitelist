<?php
/**
*
* @package phpBB Extension - GPC Whitelist
* @copyright (c) 2014 Robet Heim
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(
    'WHITELIST'						=> 'Whitelist',
	'POSITIV'						=> 'positive',
	'NEUTRAL'						=> 'neutral',
	'NEGATIV'						=> 'negative',
	'RATE_TRADE'					=> 'Rate trade',
	'TRADE_PARTNER'					=> 'Tradepartner',
	'RATING'						=> 'Rating',
	'SEARCH_TRADEPARTNER'			=> 'Search Tradepartner',
	'SHOW'							=> 'Show',
	'HIDE'							=> 'Hide',
	'WHATS_THAT'					=> 'What\'s that?',
	'WHATS_THAT_DESC'				=> 'The whitelist is a user-rating which gives you information about the reliability of a radepartner.<br/>
										Note: The ratings are form users for users - we as the forum-team will only supervise atings on rare occasions.
										<br/><br/>Use the form below (&quot;Rate trade&quot;) to rate a trade.',
	'RULES_HEAD'					=> 'Rules',
	'RULES'							=> '<ol>
											<li>Every trade gets only <u><strong>ONE</strong></u> rating - no matter how many pens you traded!<br/></li>
											<li>Trades on gatherings: Only <u><strong>ONE</strong></u> rating per tradepartner.</li>
											<li>You may only rate trades, which are connected to the GPC. This means:
												<ul>
													<li><span style="color: red">wrong: Trades with ... friends in school / siblings / users on private meetings / ...</span></li>
													<li><div style="color: green">right:<ul>
															<li>Trades that are organized in the <a href="http://localhost/phpBB3/viewforum.php?f=27">Pen-Trading-Section</a>.</li>
															<li>Trades on gatherings, that are organized in the <a href="http://localhost/phpBB3/viewforum.php?f=25">Treffen / Gatherings-Section</a>.</li></ul>
														</div>
													</li>
												</ul>
											<li>Incorrect ratings and false information can decrement positive ratings, increment negative ratings and could even lead to a ban.</li>
											<li>If you think that you got a unfair rating, you should contact a moderator of our <a href="http://forum.penspinning.de/memberlist.php?mode=leaders" class="postlink" rel="nofollow"><span style="text-decoration: underline">team</span></a> and your tradepartner.</li>
											<li>Rate meanings:
												<ul><li><span style="color: green">positive:</span> Everything good. No Problems. - Good Tradepartner</li>
													<li><span style="color: grey">neutrale:</span> Disagreements, but everything has been clarified.</li>
													<li><span style="color: red">negative:</span> Articles not received - although you tried to clarify things <strong>multiple times</strong>.</li>
												</ul>
											</li>
										</ol>',

	'RATED'							=> 'Trade has been rated.',
	'TRADE_RATED_SUCCESS'			=> 'The trade with <strong>%s</strong> was successfully rated %s.',
	'TRADE_RATED_FAIL'				=> 'Sorry, there was a problem and the trade with <strong>%s</strong> has NOT been rated. Please contact someone from our <a href="%s">forum-team</a>.',
	'TRADE_PARTNER_DOES_NOT_EXIST'	=> 'The user <strong>%s</strong> does not exist. Please be sure to spell the user correct.',
	'USE_FORM'						=> 'You have to fill the complete form to rate a trade.',
	'VIEW_RATING_OF_USER'			=> 'Show user-rating',
	'TRADER'						=> 'Trader',
	'VIEW_RATING'					=> 'Show rating',
	'RATING_OF'						=> 'Rating of',
	'NO_SELF_RATING'				=> 'You are not able to rate yourself!',
	'NO_TRADE_FLOODING'				=> 'You can only rate a user %s times in %s days.',
	'LAST_RATINGS'					=> 'Newest ratings',
	'COMMENT'						=> 'Comment',
	'WHITELIST_RATING'				=> 'Whitelist',
	'WHITELIST_SHOW'				=> 'Show ratings',
	'ACTIONS'						=> 'Actions',
	'DELETE_CONFIRM'				=> 'Are you sure, that you want to DELETE the rating?',
	'TRADE_DOES_NOT_EXIST'			=> 'The trade with ID <strong>%s</strong> does not exist.',
	'TRADE_DELETE_FAIL'				=> 'There was a problem and the rating was not deleted. Please contact the administrator.',
	'TRADE_DELETE_SUCCESS'			=> 'The rating was successfully deleted.',
	'NEED_COMMENT_ON_NEG_RATING'	=> 'You must give a comment on negativ ratings!',
	'WHITELIST_PM_GOT_ENTRY_SUBJECT'	=> 'You got an %s whitelist-entry!',
	'WHITELIST_PM_GOT_ENTRY_MSG'	=> "Hi,\n\n%s gave you a %s whitelist-entry.\n\nThis message was auto-generated.\n\nBest regards\nGPC-Team",
));
?>
