<?php
/**
 *  @Copyright
 *
 *  @package	VCNT - Visitorcounter Backend Info for Joomla! 2.5
 *  @author     Viktor Vogel {@link http://joomla-extensions.kubik-rubik.de/}
 *  @version	Version: 2.5-2 - 22-Aug-2012
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
echo '<!-- VCNT - Visitorcounter Backend Info - Kubik-Rubik Joomla! Extensions - Viktor Vogel -->';
?>
<div class="visitorcounter<?php echo $moduleclass_sfx ?>">
    <?php if($vcnt_installed) : ?>
        <?php if(!$horizontal) : ?>
            <ul>
                <?php if($s_today) : ?>
                    <li>
                        <span class="floatleft"><?php echo $today ?></span><span class="floatright"><?php echo $today_visitors ?></span><br />
                    </li>
                <?php endif; ?>
                <?php if($s_yesterday) : ?>
                    <li>
                        <span class="floatleft"><?php echo $yesterday ?></span><span class="floatright"><?php echo $yesterday_visitors ?></span><br />
                    </li>
                <?php endif; ?>
                <?php if($s_week) : ?>
                    <li>
                        <span class="floatleft"><?php echo $x_week ?></span><span class="floatright"><?php echo $week_visitors ?></span><br />
                    </li>
                <?php endif; ?>
                <?php if($s_month) : ?>
                    <li>
                        <span class="floatleft"><?php echo $x_month ?></span><span class="floatright"><?php echo $month_visitors ?></span><br />
                    </li>
                <?php endif; ?>
                <?php if($s_all) : ?>
                    <li>
                        <span class="floatleft"><?php echo $all ?></span><span class="floatright"><?php echo $all_visitors ?></span><br class="clearboth" />
                    </li>
                <?php endif; ?>
            </ul>
        <?php
        elseif($horizontal == 1) :
            $numbers = $s_today + $s_yesterday + $s_week + $s_month + $s_all - 1;
            ?>
            <?php if($hor_text) : ?>
                <strong><?php echo $hor_text ?></strong>
            <?php endif; ?>
            <?php if($s_today) : ?>
                <?php echo $today.' '.$today_visitors ?>
            <?php endif; ?>
            <?php if($numbers AND $s_today) : ?>
                <?php echo $separator ?>
                <?php $numbers-- ?>
            <?php endif; ?>
            <?php if($s_yesterday) : ?>
                <?php echo $yesterday.' '.$yesterday_visitors ?>
            <?php endif; ?>
            <?php if($numbers AND $s_yesterday) : ?>
                <?php echo $separator ?>
                <?php $numbers-- ?>
            <?php endif; ?>
            <?php if($s_week) : ?>
                <?php echo $x_week.' '.$week_visitors ?>
            <?php endif; ?>
            <?php if($numbers AND $s_week) : ?>
                <?php echo $separator ?>
                <?php $numbers-- ?>
            <?php endif; ?>
            <?php if($s_month) : ?>
                <?php echo $x_month.' '.$month_visitors ?>
            <?php endif; ?>
            <?php if($numbers AND $s_month) : ?>
                <?php echo $separator ?>
                <?php $numbers-- ?>
            <?php endif; ?>
            <?php if($s_all) : ?>
                <?php echo $all.' '.$all_visitors ?>
            <?php endif; ?>
        <?php elseif($horizontal == 2) : ?>
            <?php if($hor_text) : ?>
                <strong><?php echo $hor_text ?></strong>
            <?php endif; ?>
            <?php echo $all_visitors.' ('.($today_visitors).')' ?>
        <?php endif; ?>
        <?php if($whoisonline == 1 OR $whoisonline == 2) : ?>
            <?php $guest = JText::plural('MOD_VCNT_BACKEND_INFO_GUESTS', $users_online['guest']); ?>
            <?php $member = JText::plural('MOD_VCNT_BACKEND_INFO_MEMBERS', $users_online['user']); ?>
            <div><?php echo JText::sprintf('MOD_VCNT_BACKEND_INFO_USERONLINE', $guest, $member); ?></div>
                <?php if($whoisonline == 2 AND !empty($users_online['usernames'])) : ?>
                <ul>
                    <?php foreach($users_online['usernames'] as $user_online) : ?>
                        <li class="members">
                                <?php if(!empty($whoisonline_linknames)) : ?>
                                <a href="index.php?option=com_users&task=user.edit&id=<?php echo (int) $user_online['userid']; ?>">
                                <?php echo $user_online['username']; ?>
                                </a>
                            <?php else : ?>
                                <?php echo $user_online['username']; ?>
                        <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endif; ?>
    <?php else : ?>
        <?php echo JTEXT::_('MOD_VCNT_BACKEND_INFO_NOMAINPLUGIN'); ?>
    <?php endif; ?>
</div>