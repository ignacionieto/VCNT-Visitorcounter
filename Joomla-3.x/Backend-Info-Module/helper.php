<?php
/**
 * @Copyright
 *
 * @package    VCNT - Visitorcounter Backend Info for Joomla! 3
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

class ModVisitorcounterBackendInfoHelper extends JObject
{
    protected $_db;

    function __construct()
    {
        $this->set('_db', JFactory::getDbo());
    }

    function isEnabled()
    {
        $query = "SELECT COUNT(*) FROM ".$this->_db->quoteName('#__modules')." WHERE ".$this->_db->quoteName('module')." = ".$this->_db->quote('mod_visitorcounter')." AND ".$this->_db->quoteName('published')." = 1";
        $this->_db->setQuery($query);
        $result = $this->_db->loadResult();

        return $result;
    }

    /**
     * Reads the current numbers from the database
     *
     * @param JRegistry $params
     *
     * @return array    All needed information for the visitorcounter
     */
    function read($params)
    {
        // Set the correct timezone offset
        $site_offset = JFactory::getApplication()->getCfg('offset');
        date_default_timezone_set($site_offset);

        // Calculate the needed time intervalls
        $day = date('d');
        $month = date('m');
        $year = date('Y');
        $daystart = mktime(0, 0, 0, $month, $day, $year);
        $monthstart = mktime(0, 0, 0, $month, 1, $year);
        $weekstart = $daystart - ((date('N') - 1) * 24 * 60 * 60);
        $yesterdaystart = $daystart - (24 * 60 * 60);

        // Create queries for the database call
        $queries = array();

        $queries['query_all'] = "SELECT count(*) FROM ".$this->_db->quoteName('#__vcnt');
        $queries['query_today'] = "SELECT count(*) FROM ".$this->_db->quoteName('#__vcnt')." WHERE ".$this->_db->quoteName('tm')." > ".$this->_db->quote($daystart);
        $queries['query_yesterday'] = "SELECT count(*) FROM ".$this->_db->quoteName('#__vcnt')." WHERE ".$this->_db->quoteName('tm')." > ".$this->_db->quote($yesterdaystart)." AND ".$this->_db->quoteName('tm')." < ".$this->_db->quote($daystart);
        $queries['query_week'] = "SELECT count(*) FROM ".$this->_db->quoteName('#__vcnt')." WHERE ".$this->_db->quoteName('tm')." >= ".$this->_db->quote($weekstart);
        $queries['query_month'] = "SELECT count(*) FROM ".$this->_db->quoteName('#__vcnt')." WHERE ".$this->_db->quoteName('tm')." >= ".$this->_db->quote($monthstart);

        // Add the number from the cleaned database table
        $clean_db = $params->get('clean_db');

        if(!empty($clean_db))
        {
            $queries['query_clean_db'] = "SELECT ".$this->_db->quoteName('cnt')." FROM ".$this->_db->quoteName('#__vcnt_pc');
        }

        $queries_string = implode(' UNION ALL ', $queries);
        $this->_db->setQuery($queries_string);
        $result = $this->_db->loadRowList();

        $all_visitors = $result[0][0];

        // Add the preset number
        $preset = $params->get('preset');

        if(!empty($preset))
        {
            $all_visitors += $preset;
        }

        if(!empty($clean_db))
        {
            $all_visitors += $result[5][0];
        }

        $today_visitors = $result[1][0];
        $yesterday_visitors = $result[2][0];
        $week_visitors = $result[3][0];
        $month_visitors = $result[4][0];

        $ret = array($all_visitors, $today_visitors, $yesterday_visitors, $week_visitors, $month_visitors);

        return $ret;
    }

    /**
     * Checks the session table and creates a list with all guests and registered user who have an entry in the database in the specified session time
     *
     * @param int $whoisonline_session
     *
     * @return array $users_online All online visitors in the specified session time
     */
    function whoIsOnline($whoisonline_session)
    {
        $users_online = array();
        $guest = 0;
        $user = 0;
        $whoisonline_session = time() - $whoisonline_session * 60;

        $query = "SELECT ".$this->_db->quoteName('guest').", ".$this->_db->quoteName('client_id')." , ".$this->_db->quoteName('username').", ".$this->_db->quoteName('userid')." FROM ".$this->_db->quoteName('#__session')." WHERE ".$this->_db->quoteName('client_id')." = 0 AND ".$this->_db->quoteName('time')." > ".$this->_db->quote($whoisonline_session);
        $this->_db->setQuery($query);
        $sessions = (array)$this->_db->loadObjectList();

        if(!empty($sessions))
        {
            foreach($sessions as $session)
            {
                if($session->guest == 1)
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
