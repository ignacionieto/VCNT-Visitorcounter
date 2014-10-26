<?php
/**
 *  @Copyright
 *
 *  @package	VCNT - Visitorcounter Backend Info for Joomla! 3
 *  @author     Viktor Vogel {@link http://joomla-extensions.kubik-rubik.de/}
 *  @version    3-2 - 2013-12-15
 *  @link       Project Site {@link http://joomla-extensions.kubik-rubik.de/vcnt-visitorcounter}
 *
 *  @license GNU/GPL
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

require_once (dirname(__FILE__).'/helper.php');

$start = new ModVisitorcounterBackendInfoHelper;

// Check whether the main module Visitorcounter VCNT is installed and enabled
$vcnt_installed = $start->isEnabled();

if($vcnt_installed)
{
    $today = $params->get('today', JText::_('MOD_VISITORCOUNTER_BACKEND_INFO_TODAY'));
    $yesterday = $params->get('yesterday', JText::_('MOD_VISITORCOUNTER_BACKEND_INFO_YESTERDAY'));
    $all = $params->get('all', JText::_('MOD_VISITORCOUNTER_BACKEND_INFO_ALL'));
    $x_month = $params->get('month', JText::_('MOD_VISITORCOUNTER_BACKEND_INFO_MONTH'));
    $x_week = $params->get('week', JText::_('MOD_VISITORCOUNTER_BACKEND_INFO_WEEK'));
    $s_today = $params->get('s_today');
    $s_yesterday = $params->get('s_yesterday');
    $s_all = $params->get('s_all');
    $s_week = $params->get('s_week');
    $s_month = $params->get('s_month');
    $horizontal = $params->get('horizontal');
    $separator = $params->get('separator');
    $hor_text = $params->get('hor_text');
    $moduleclass_sfx = $params->get('moduleclass_sfx', '');
    $whoisonline = $params->get('whoisonline');
    $whoisonline_linknames = $params->get('whoisonline_linknames');
    $whoisonline_session = $params->get('whoisonline_session');

    if(!empty($whoisonline))
    {
        $users_online = $start->whoIsOnline($whoisonline_session);
    }

    list($all_visitors, $today_visitors, $yesterday_visitors, $week_visitors, $month_visitors) = $start->read($params);
}

$document = JFactory::getDocument();
$document->addStyleSheet('modules/mod_visitorcounter_backend_info/mod_visitorcounter_backend_info.css');

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require(JModuleHelper::getLayoutPath('mod_visitorcounter_backend_info', 'default'));
