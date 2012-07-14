<?php
/**
 *  @Copyright
 *
 *  @package	VCNT for Joomla! 2.5
 *  @author     Viktor Vogel {@link http://joomla-extensions.kubik-rubik.de/}
 *  @version	Version: 2.5-2 - 05-Jun-2012
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

class mod_vcntHelper extends JObject
{
    protected $_db;

    function __construct()
    {
        $this->set('_db', JFactory::getDbo());
    }

    function count($params)
    {
        $locktime = $params->get('locktime', 60);
        $nobots = $params->get('nobots');
        $botslist = $params->get('botslist');
        $noip = $params->get('noip');
        $ipslist = $params->get('ipslist');
        $sql = $params->get('sql');

        $locktime = $locktime * 60;
        $now = time();
        $ip = $_SERVER['REMOTE_ADDR'];

        $bot = 0;

        if($nobots)
        {
            if(isset($_SERVER['HTTP_USER_AGENT']))
            {
                $agent = $_SERVER['HTTP_USER_AGENT'];
                $bots_array = explode(",", $botslist);

                foreach($bots_array as $e)
                {
                    if(preg_match('/'.trim($e).'/i', $agent))
                    {
                        $bot = 1;
                    }
                }
            }
            else
            {
                $bot = 1;
            }
        }

        $iplock = 0;

        if($noip)
        {
            if(isset($_SERVER['REMOTE_ADDR']))
            {
                $agent = $_SERVER['REMOTE_ADDR'];
                $bots_array = explode(",", $ipslist);
                foreach($bots_array as $e)
                {
                    if(preg_match('/'.trim($e).'/i', $agent))
                    {
                        $iplock = 1;
                    }
                }
            }
            else
            {
                $iplock = 1;
            }
        }

        if($sql)
        {
            $query = "CREATE TABLE IF NOT EXISTS ".$this->_db->nameQuote('#__vcnt')." (".$this->_db->nameQuote('tm')." INT NOT NULL, ".$this->_db->nameQuote('ip')." VARCHAR(16) NOT NULL DEFAULT '0.0.0.0')";
            $this->_db->setQuery($query);
            $this->_db->query();
        }

        // Prüfen, ob IP bereits vorhanden und Reloadsperre abgelaufen
        $query = "SELECT count(*) FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('ip')." = ".$this->_db->quote($ip)." AND (".$this->_db->nameQuote('tm')." + ".$this->_db->quote($locktime).") > ".$this->_db->quote($now);
        $this->_db->setQuery($query);
        $result = $this->_db->query();
        $items = $this->_db->loadResult();

        if(empty($items) AND ($bot == 0) AND ($iplock == 0))
        {
            $query = "INSERT INTO ".$this->_db->nameQuote('#__vcnt')." (".$this->_db->nameQuote('tm').", ".$this->_db->nameQuote('ip').") VALUES (".$this->_db->quote($now).", ".$this->_db->quote($ip).")";
            $this->_db->setQuery($query);
            $this->_db->query();
        }
    }

    function read($params)
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
        $preset = $params->get('preset');

        $query = "SELECT CNT FROM ".$this->_db->nameQuote('#__vcnt_pc');
        $this->_db->setQuery($query);
        $pre2 = $this->_db->loadResult();

        $query = "SELECT count(*) FROM ".$this->_db->nameQuote('#__vcnt');
        $this->_db->setQuery($query);
        $all_visitors = $this->_db->loadResult();
        $all_visitors += $preset;
        $all_visitors += $pre2;

        $query = "SELECT count(*) FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('tm').">".$this->_db->quote($daystart);
        $this->_db->setQuery($query);
        $today_visitors = $this->_db->loadResult();

        $query = "SELECT count(*) FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('tm').">".$this->_db->quote($yesterdaystart)." AND ".$this->_db->nameQuote('tm')."<".$this->_db->quote($daystart);
        $this->_db->setQuery($query);
        $yesterday_visitors = $this->_db->loadResult();

        $query = "SELECT count(*) FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('tm').">=".$this->_db->quote($weekstart);
        $this->_db->setQuery($query);
        $week_visitors = $this->_db->loadResult();

        $query = "SELECT count(*) FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('tm').">=".$this->_db->quote($monthstart);
        $this->_db->setQuery($query);
        $month_visitors = $this->_db->loadResult();

        $ret = array($all_visitors, $today_visitors, $yesterday_visitors, $week_visitors, $month_visitors);

        return ($ret);
    }

    function clean($params)
    {
        $sql = $params->get('sql');

        $siteOffset = JFactory::getApplication()->getCfg('offset');
        date_default_timezone_set($siteOffset);

        $month = date('m');
        $year = date('Y');
        $monthstart = mktime(0, 0, 0, $month, 1, $year);

        // Prüfen, ob MOD_VCNT_SQL Tabelle bereits erstellt wurde
        if($sql)
        {
            $query = "CREATE TABLE IF NOT EXISTS ".$this->_db->nameQuote('#__vcnt_pc')." (".$this->_db->nameQuote('cnt')." INT NOT NULL NOT NULL DEFAULT '0')";
            $this->_db->setQuery($query);
            $this->_db->query();
        }

        $query = "SELECT count(*) FROM ".$this->_db->nameQuote('#__vcnt_pc');
        $this->_db->setQuery($query);
        $numrows = $this->_db->loadResult();

        if(!$numrows)
        {
            $query = "INSERT INTO ".$this->_db->nameQuote('#__vcnt_pc')." VALUES(0)";
            $this->_db->setQuery($query);
            $this->_db->query();
        }

        $cleanstart = $monthstart - (8 * 24 * 60 * 60);
        $query = "SELECT count(*) FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('tm')."<".$this->_db->quote($cleanstart);
        $this->_db->setQuery($query);
        $oldrows = $this->_db->loadResult();

        if($oldrows)
        {
            $query = "UPDATE ".$this->_db->nameQuote('#__vcnt_pc')." SET ".$this->_db->nameQuote('cnt')."=".$this->_db->nameQuote('cnt')."+".$this->_db->quote($oldrows);
            $this->_db->setQuery($query);
            $this->_db->query();

            $query = "DELETE FROM ".$this->_db->nameQuote('#__vcnt')." WHERE ".$this->_db->nameQuote('tm')."<".$this->_db->quote($cleanstart);
            $this->_db->setQuery($query);
            $this->_db->query();
        }

        return;
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

    function popupSqueeze(&$params, $cwsession)
    {
        $document = JFactory::getDocument();
        JHTML::_('behavior.mootools');
        JHTML::_('behavior.modal');

        $cwsession = !$params->get('cwsession');
        $url = $params->get('squeeze_url'); // Link eingeben - URL oder Grafik
        $relativetoroot = $params->get('squeeze_relativetoroot');
        $width = $params->get('squeeze_width');
        $height = $params->get('squeeze_height');
        $cookietime = $params->get('squeeze_time'); // Cookie gesetzt für Dauer (Minuten)
        $cOverlayOpacity = $params->get('squeeze_opacity');
        $ckey = ''; // Letzte Änderung des Inhaltes - Format: %d-%m-%Y
        $enableonchange = 0; // Popup bei Änderungsdatum anzeigen
        $cnocookies = 0; // Anzeigen, wenn Cookies deaktiviert - 1 = ja, 0 = nein
        $cswf = 0; // SWF Film
        $cookiename = 'vcnt'; // Cookiename

        if(!$enableonchange)
        {
            $ckey = "yes";
        }

        if($relativetoroot)
        {
            $url = JURI::base().$url;
        }

        $html = '<script type="text/javascript">
                <!--
                function getCookie(c_name)
                {
                   if (document.cookie.length>0)
                     {
                     c_start=document.cookie.indexOf(c_name + "=");
                     if (c_start!=-1)
                       {
                       c_start=c_start + c_name.length+1;
                       c_end=document.cookie.indexOf(";",c_start);
                       if (c_end==-1) c_end=document.cookie.length;
                       return unescape(document.cookie.substring(c_start,c_end));
                       }
                     }
                   return "";
                }
                function setCookie(name,value,minutes)
                {
                   if (minutes) {
                      var date = new Date();
                      date.setTime(date.getTime()+(minutes*1000*60));
                      var expires = "; expires="+date.toGMTString();
                   }
                   else var expires = "";
                   document.cookie = name+"="+value+expires+"; path=/";
                }
                function checkCookie()
                {
                   showrightpane=getCookie(\''.$cookiename.'\');
                   if ((showrightpane==null) || (showrightpane=="")) {
                     setCookie(\''.$cookiename.'\',\'no\','.$cookietime.'); //set the default cookie value here
                   }
                }
                function showV() {
                        '.($cswf ? '
                        var myel = new Element("div").adopt(
                                new Element("div",{"id":"squeeze_swf_pop"}).adopt(
                                        new Element("h1").appendText("To watch this video... please get suitable Flash Player!"),
                                        new Element("br"),
                                        new Element("a",{"href":"http://www.adobe.com/go/getflashplayer"}).adopt(
                                                new Element ("img",{"src":"http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif", "alt":"Get Adobe Flash player"})
                                        )
                                )
                        );
                        ' :
                        'var myel = new Element(\'a\',{\'href\':\''.$url.'\'});'
                ).'
                        SqueezeBox.fromElement(myel,{
                                size: {x: '.$width.', y: '.$height.'},
                                overlayOpacity: '.((double) $cOverlayOpacity) / 100.0.',
                                handler: \''.($cswf ? 'adopt' : 'iframe').'\',
                                iframePreload:true,
                                onOpen: function() {
                                        '.($cswf ? '
                                        swfobject.embedSWF("'.$url.'", "squeeze_swf_pop", '.$width.', '.$height.', "9.0.0");
                                        ' : '').'
                                        if (window.ie6) {
                                          window.scrollTo(0,0);
                                          var t=$(\'sbox-btn-close\'); var g = t.getStyle(\'background-image\');
                                          if (g!="none") {
                                            g = g.replace("url(\"","").replace("\")","");
                                            t.setStyle("filter", \'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="\' + g + \'",sizingMethod="crop")\');
                                            t.setStyle("background","none");
                                                        t.setStyle("cursor","pointer");
                                          }
                                        }
                                },
                                onClose: function(){
                                        setCookie(\''.$cookiename.'\',\''.$ckey.'\','.$cookietime.');
                                }'.($cswf ? ',
                                onClose: function(){
                                        $(\'squeeze_swf_pop\').StopPlay();
                                }' : '').'
                        });
                }
                function closeV() {
                        SqueezeBox.close();
                }
                checkCookie();
                window.addEvent(\'domready\', function() {
                    v = getCookie(\''.$cookiename.'\');
                    '.($cwsession ? 'showV();' : '
                    if(!('.($cnocookies ? '' : '(v==null) || (v == "") || ').'(v=="'.$ckey.'")))
                    {
                            showV();
                    }
                    ').'
                 });
                 //-->
                 </script>';
        $document->addCustomTag($html);
    }

    function popupJSAlert($params, $all_visitors, $counterwinner, $cwnumber, $cwsession)
    {
        if(!isset($_SESSION['cwsessioncookie']))
        {
            $_SESSION['cwsessioncookie'] = 0;
        }

        if(!$cwsession)
        {
            $_SESSION['cwsessioncookie'] = 0;
        }

        if(empty($_SESSION['cwsessioncookie']))
        {
            $head = '<script type="text/javascript">alert("'.JText::_('MOD_VCNT_JSALERT').'");</script>';
            $document = JFactory::getDocument();
            $document->addCustomTag($head);
        }

        if($cwsession)
        {
            $_SESSION['cwsessioncookie'] = 1;
        }
    }

    function showAllowedUser($params)
    {
        $user = JFactory::getUser();
        $allowedusergroup = false;

        $filtergroups = (array) $params->get('filter_groups', 1);
        $usergroup = JAccess::getGroupsByUser($user->id);

        foreach($usergroup as $value)
        {
            foreach($filtergroups as $filtergroupsvalue)
            {
                if($value == $filtergroupsvalue)
                {
                    $allowedusergroup = true;
                }

                // If Super User or Group Public - always true
                if($value == 8 OR $filtergroupsvalue == 1)
                {
                    $allowedusergroup = true;
                }
            }
        }

        return $allowedusergroup;
    }
}
