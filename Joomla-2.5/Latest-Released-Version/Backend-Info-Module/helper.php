<?php
/**
 *  @Copyright
 *
 *  @package	VCNT - Visitorcounter Backend Info for Joomla! 2.5
 *  @author     Viktor Vogel {@link http://joomla-extensions.kubik-rubik.de/}
 *  @version	Version: 2.5-1 - 24-Jun-2012
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

class mod_vcnt_backend_infoHelper extends JObject
{
    protected $_db;

    function __construct()
    {
        $this->set('_db', JFactory::getDbo());
    }

    function isEnabled()
    {
        $query = "SELECT COUNT(*) FROM ".$this->_db->nameQuote('#__modules')." WHERE ".$this->_db->nameQuote('module')." = ".$this->_db->quote('mod_vcnt')." AND ".$this->_db->nameQuote('published')." = 1";
        $this->_db->setQuery($query);
        $result = $this->_db->loadResult();

        return $result;
    }

    function read()
    {
        $siteOffset = JFactory::getApplication()->getCfg('offset');
        date_default_timezone_set($siteOffset);

        $day = date('d');
        $month = date('m');
        $year = date('Y');
        $daystart = mktime(0, 0, 0, $month, $day, $year);
        $monthstart = mktime(0, 0, 0, $month, 1, $year);
        $weekday = date('N');
        $weekday--;

        if($weekday < 0)
        {
            $weekday = 7;
        }

        $weekday = $weekday * 24 * 60 * 60;
        $weekstart = $daystart - $weekday;
        $yesterdaystart = $daystart - (24 * 60 * 60);
        $preset = 0;

        $query = "SELECT CNT FROM ".$this->_db->nameQuote('#__vcnt_pc');
        $this->_db->setQuery($query);
        $pre2 = $this->_db->loadResult();

        $query = "SELECT COUNT(*) FROM ".$this->_db->nameQuote('#__vcnt');
        $this->_db->setQuery($query);
        $all_visitors = $this->_db->loadResult();
        $all_visitors += $preset;
        $all_visitors += $pre2;

        $query = "SELECT COUNT(*) FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('tm').">".$this->_db->quote($daystart);
        $this->_db->setQuery($query);
        $today_visitors = $this->_db->loadResult();

        $query = "SELECT COUNT(*) FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('tm').">".$this->_db->quote($yesterdaystart)." AND ".$this->_db->nameQuote('tm')."<".$this->_db->quote($daystart);
        $this->_db->setQuery($query);
        $yesterday_visitors = $this->_db->loadResult();

        $query = "SELECT COUNT(*) FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('tm').">=".$this->_db->quote($weekstart);
        $this->_db->setQuery($query);
        $week_visitors = $this->_db->loadResult();

        $query = "SELECT COUNT(*) FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('tm').">=".$this->_db->quote($monthstart);
        $this->_db->setQuery($query);
        $month_visitors = $this->_db->loadResult();

        $ret = array($all_visitors, $today_visitors, $yesterday_visitors, $week_visitors, $month_visitors);

        return ($ret);
    }

    function whoIsOnline($whoisonline_session)
    {
        $users_online = array();
        $guest = 0;
        $user = 0;
        $whoisonline_session = time() - $whoisonline_session * 60;

        $query = "SELECT ".$this->_db->nameQuote('guest').", ".$this->_db->nameQuote('usertype').", ".$this->_db->nameQuote('client_id')." , ".$this->_db->nameQuote('username').", ".$this->_db->nameQuote('userid')." FROM ".$this->_db->nameQuote('#__session')." WHERE ".$this->_db->nameQuote('client_id')." = 0 AND ".$this->_db->nameQuote('time')." > ".$this->_db->quote($whoisonline_session);
        $this->_db->setQuery($query);
        $sessions = (array) $this->_db->loadObjectList();

        if(!empty($sessions))
        {
            foreach($sessions as $session)
            {
                if($session->guest == 1 AND empty($session->usertype))
                {
                    $guest++;
                    continue;
                }
                elseif($session->guest == 0)
                {
                    $user++;
                    $username = array('username' => $session->username, 'userid' => $session->userid);
                    $users_online['usernames'][] = $username;
                }
            }
        }

        $users_online['guest'] = $guest;
        $users_online['user'] = $user;

        return $users_online;
    }
}
