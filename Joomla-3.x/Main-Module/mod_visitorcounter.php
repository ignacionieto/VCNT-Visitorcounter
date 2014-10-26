<?php
/**
 * @Copyright
 *
 * @package    VCNT for Joomla! 3
 * @author     Viktor Vogel {@link http://joomla-extensions.kubik-rubik.de/}
 * @version    3-2 - 2013-12-15
 * @link       Project Site {@link http://joomla-extensions.kubik-rubik.de/vcnt-visitorcounter}
 *
 * @license    GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__).'/helper.php');

$today = $params->get('today', JText::_('MOD_VISITORCOUNTER_TODAY'));
$yesterday = $params->get('yesterday', JText::_('MOD_VISITORCOUNTER_YESTERDAY'));
$all = $params->get('all', JText::_('MOD_VISITORCOUNTER_ALL'));
$x_month = $params->get('month', JText::_('MOD_VISITORCOUNTER_MONTH'));
$x_week = $params->get('week', JText::_('MOD_VISITORCOUNTER_WEEK'));
$s_today = $params->get('s_today');
$s_yesterday = $params->get('s_yesterday');
$s_all = $params->get('s_all');
$s_week = $params->get('s_week');
$s_month = $params->get('s_month');
$clean_db = (int)$params->get('clean_db');
$copy = $params->get('copy', 1);
$horizontal = $params->get('horizontal');
$separator = $params->get('separator');
$hor_text = $params->get('hor_text');
$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$counterwinner = $params->get('counterwinner');
$cwnumber = $params->get('cwnumber');
$cwcode = $params->get('cwcode');
$cwemail = $params->get('cwemail');
$cwtext = $params->get('cwtext');
$cwpopup = $params->get('cwpopup');
$cwsession = $params->get('cwsession');
$whoisonline = $params->get('whoisonline');
$whoisonline_linknames = $params->get('whoisonline_linknames');
$whoisonline_session = (int)$params->get('whoisonline_session');
$sql = $params->get('sql');

$start = new ModVisitorcounterHelper();

if($sql)
{
    $start->createSqlTables($clean_db);
}

$start->count($params);

if($clean_db)
{
    $start->clean();
}

if(!empty($whoisonline))
{
    $users_online = $start->whoIsOnline($whoisonline_session);

    if(!empty($whoisonline_linknames))
    {
        $item_id = $start->getItemId($whoisonline_linknames);
    }
}

$show_allowed_user = $start->showAllowedUser($params);

if($show_allowed_user == 1)
{
    list($all_visitors, $today_visitors, $yesterday_visitors, $week_visitors, $month_visitors) = $start->read($params);

    if($counterwinner AND $all_visitors >= $cwnumber)
    {
        if($cwpopup == 1)
        {
            $start->popupSqueeze($params);
        }
        elseif($cwpopup == 2)
        {
            $start->popupJSAlert($cwsession);
        }
    }

    $document = JFactory::getDocument();
    $document->addStyleSheet('modules/mod_visitorcounter/mod_visitorcounter.css');

    $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
    require(JModuleHelper::getLayoutPath('mod_visitorcounter', 'default'));
}
