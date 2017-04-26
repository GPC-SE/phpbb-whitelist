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
	'POSITIV'						=> 'positiv',
	'NEUTRAL'						=> 'neutral',
	'NEGATIV'						=> 'negativ',
	'RATE_TRADE'					=> 'Trade bewerten',
	'TRADE_PARTNER'					=> 'Tradepartner',
	'RATING'						=> 'Bewertung',
	'SEARCH_TRADEPARTNER'			=> 'Suche Tradepartner',
	'SHOW'							=> 'Anzeigen',
	'HIDE'							=> 'Verstecken',
	'WHATS_THAT'					=> 'Was ist das?',
	'WHATS_THAT_DESC'				=> 'Die Whitelist ist eine Userbewertungen die <span style="text-decoration: underline">grundsätzlich</span> Auskunft darüber gibt wie zuverlässlig / vertrauenswürdig ein Tradepartner ist.<br/>
										Beachte, dass die Bewertungen von Usern für User sind und wir als Team des Forums diese Bewertungen nur in Ausnahmefällen kontrollieren.
										<br/><br/>Um einen Trade zu bewerten, benutze das Formular unten &quot;Trade bewerten&quot;.',
	'RULES_HEAD'					=> 'Regeln',
	'RULES'							=> '<ol>
											<li>Ein Trade gibt <u><strong>EINE</strong></u> Bewertung - egal wieviele Stifte getradet wurden!<br/></li>
											<li>Trades auf Gatherings: Nur <u><strong>EINE</strong></u> Bewertung pro Tradepartner.</li>
											<li>Nur Trades bewerten, die in Bezug zur GPC stehen:
												<ul>
													<li><span style="color: red">falsch: Trade mit ... Freund in der Schule / Geschwistern / Usern bei privaten Treffen / ...</span></li>
													<li><div style="color: green">richtig:<ul>
															<li>Über den <a href="community/viewforum.php?f=27">Pen-Trading-Bereich</a> organisierte Trades.</li>
															<li>Trades auf Gatherings, die über den <a href="community/viewforum.php?f=25">Treffen / Gatherings-Bereich</a> organisiert wurden.</li></ul>
														</div>
													</li>
												</ul>
											<li>Falsche Angaben führen von einem Abzug positiver Bewertungen über negativ Einträge bis hin zum Ausschluss.</li>
											<li>Solltest du dich ungerecht bewertet fühlen, so wende dich bitte an einen Moderator des <a href="community/memberlist.php?mode=group&g=3" class="postlink" rel="nofollow"><span style="text-decoration: underline">Teams</span></a> und an deinen Tradepartner.</li>
											<li>Bedeutung der Ratings:
												<ul><li><span style="color: green">positiv:</span> Alles reibungslos. - Guter Tradepartner</li>
													<li><span style="color: grey">neutral:</span> Unstimmigkeiten, sehr lange auf Ware gewartet o.ä., aber alles geklärt.</li>
													<li><span style="color: red">negativ:</span> Ware auch <strong>nach mehrmaligen Klärungsversuchen</strong> nicht erhalten.</li>
												</ul>
											</li>
										</ol>',
	'RATED'							=> 'Trade wurde bewertet',
	'TRADE_RATED_SUCCESS'			=> 'Der Trade mit <strong>%s</strong> wurde erfolgreich %s bewertet.',
	'TRADE_RATED_FAIL'				=> 'Der Trade mit <strong>%s</strong> konnte nicht bewertet werden. Bitte kontaktiere jemanden aus dem <a href="%s">Forums-Team</a>.',
	'TRADE_PARTNER_DOES_NOT_EXIST'	=> 'Der User <strong>%s</strong> existiert nicht. Bitte achte darauf, dass du den User richtig schreibst.',
	'USE_FORM'						=> 'Du musst das gesamte Formular ausfüllen, um einen Trade zu bewerten.',
	'VIEW_RATING_OF_USER'			=> 'Bewertung eines Users anzeigen',
	'TRADER'						=> 'Trader',
	'VIEW_RATING'					=> 'Bewertung anzeigen',
	'TRADER_HAS_NO_ENTRIES'			=> '%s hat noch keine Bewertungen erhalten.',
	'RATING_OF'						=> 'Bewertung von',
	'NO_SELF_RATING'				=> 'Du kannst dich nicht selbst bewerten!',
	'NO_TRADE_FLOODING'				=> 'Du kannst einen User maximal %s mal in %s Tagen bewerten.',
	'LAST_RATINGS'					=> 'Die neusten Bewertungen',
	'COMMENT'						=> 'Kommentar',
	'WHITELIST_RATING'				=> 'Whitelist',
	'WHITELIST_SHOW'				=> 'Bewertungen anzeigen',
	'ACTIONS'						=> 'Aktionen',
	'DELETE_CONFIRM'				=> 'Bist Du Dir sicher, dass Du die Bewertung LÖSCHEN möchtest?',
	'TRADE_DOES_NOT_EXIST'			=> 'Der Trade mit der ID <strong>%s</strong> existiert nicht.',
	'TRADE_DELETE_FAIL'				=> 'Die Bewertung konnte nicht gelöscht werden. Bitte kontaktiere den Administrator.',
	'TRADE_DELETE_SUCCESS'			=> 'Die Bewertung wurde erfolgreich gelöscht.',
	'NEED_COMMENT_ON_NEG_RATING'	=> 'Du musst zu einer negativen Bewertung einen Kommentar angeben!',
	'WHITELIST_PM_GOT_ENTRY_SUBJECT'	=> 'Du hast einen %sen Eintrag in der Whitelist bekommen!',
	'WHITELIST_PM_GOT_ENTRY_MSG'	=> "Hallo,\n\n%s hat dir einen %sen Eintrag in der Whitelist gegeben.\n\nDiese Nachricht wurde automatisch generiert.\n\nViele Grüße\nGPC-Team",
));
