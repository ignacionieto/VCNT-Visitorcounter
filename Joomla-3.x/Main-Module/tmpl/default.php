<?php
/**
 *  @Copyright
 *
 *  @package	VCNT for Joomla! 3
 *  @author     Viktor Vogel {@link http://joomla-extensions.kubik-rubik.de/}
 *  @version	3-2 - 2013-12-15
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
echo '<!-- VCNT - Visitorcounter for Joomla! 3.x - Kubik-Rubik Joomla! Extensions - Viktor Vogel -->';
?>
<div class="visitorcounter <?php echo $moduleclass_sfx ?>">
    <?php if(!$horizontal) : ?>
        <?php if($s_today) : ?>
            <p>
                <?php echo $today ?>
                <span class="floatright"><?php echo $today_visitors ?></span>
            </p>
        <?php endif; ?>
        <?php if($s_yesterday) : ?>
            <p>
                <?php echo $yesterday ?>
                <span class="floatright"><?php echo $yesterday_visitors ?></span>
            </p>
        <?php endif; ?>
        <?php if($s_week) : ?>
            <p>
                <?php echo $x_week ?>
                <span class="floatright"><?php echo $week_visitors ?></span>
            </p>
        <?php endif; ?>
        <?php if($s_month) : ?>
            <p>
                <?php echo $x_month ?>
                <span class="floatright"><?php echo $month_visitors ?></span>
            </p>
        <?php endif; ?>
        <?php if($s_all) : ?>
            <p>
                <?php echo $all ?>
                <span class="floatright"><?php echo $all_visitors ?></span>
            </p>
        <?php endif; ?>
    <?php elseif($horizontal == 1) : ?>
        <p>
            <?php $numbers = $s_today + $s_yesterday + $s_week + $s_month + $s_all - 1; ?>
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
        </p>
    <?php elseif($horizontal == 2) : ?>
        <p>
            <?php if($hor_text) : ?>
                <strong><?php echo $hor_text ?></strong>
            <?php endif; ?>
            <?php echo $all_visitors.' ('.($today_visitors).')' ?>
        </p>
    <?php endif; ?>
    <?php if($counterwinner AND $all_visitors >= $cwnumber AND $cwcode AND $cwemail) : ?>
        <p class="center">
            <strong><?php echo JText::_('MOD_VISITORCOUNTER_WON'); ?></strong>
        </p>
        <?php if($cwtext) : ?>
            <p class="center">
                <?php echo $cwtext; ?>
            </p>
        <?php endif; ?>
        <p class="center">
            <?php echo JText::_('MOD_VISITORCOUNTER_CODEIS'); ?> <strong><?php echo $cwcode; ?></strong>
        </p>
        <p class="center">
            <?php echo JText::_('MOD_VISITORCOUNTER_SENDIN'); ?> <strong><a href="mailto:<?php echo $cwemail; ?>?subject=<?php echo JText::_('MOD_VISITORCOUNTER_CONTEST'); ?>!%20Code:%20<?php echo $cwcode; ?>" title="<?php echo JText::_('MOD_VISITORCOUNTER_CONTEST'); ?>"><?php echo JText::_('MOD_VISITORCOUNTER_JOINNOW'); ?></a></strong>
        </p>
    <?php endif; ?>
    <?php if($whoisonline == 1 OR $whoisonline == 2) : ?>
        <?php $guest = JText::plural('MOD_VISITORCOUNTER_GUESTS', $users_online['guest']); ?>
        <?php $member = JText::plural('MOD_VISITORCOUNTER_MEMBERS', $users_online['user']); ?>
        <p class="whoisonline">
            <?php echo JText::sprintf('MOD_VISITORCOUNTER_USERONLINE', $guest, $member); ?>
        </p>
        <?php if($whoisonline == 2 AND !empty($users_online['usernames'])) : ?>
            <ul class="whoisonline_users">
                <?php foreach($users_online['usernames'] as $user_online) : ?>
                    <li>
                        <?php if(!empty($whoisonline_linknames)) : ?>
                            <?php if($whoisonline_linknames == 1) : ?>
                                <a href="index.php?option=com_users&view=profile&member_id=<?php echo (int)$user_online['userid'].$item_id; ?>">
                                <?php elseif($whoisonline_linknames == 2) : ?>
                                    <a href="index.php?option=com_comprofiler&task=userprofile&user=<?php echo (int)$user_online['userid'].$item_id; ?>">
                                    <?php endif; ?>
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
    <?php if($copy) : ?>
        <p class="small">
            <a href="http://joomla-extensions.kubik-rubik.de/" target="_blank" title="VCNT - Visitorcounter - Joomla! 3 - Kubik-Rubik Joomla! Extensions">Kubik-Rubik Joomla! Extensions</a>
        </p>
    <?php endif; ?>
</div>