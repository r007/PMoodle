<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'tool_bloglevelupgrade', language 'ru', branch 'MOODLE_22_STABLE'
 *
 * @package   tool_bloglevelupgrade
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['bloglevelupgradedescription'] = '<p> Этот сайт был недавно обновлен до Moodle 2.0.</p>
<p> Видимость блогов была упрощена в 2.0, но Ваш сайт все еще использует один из старых типов видимости.</p>
<p> Чтобы сохранить на вашем сайте видимость записей блога, связанных с курсом или группой, вам необходимо запустить следующий скрипт обновления. Он создаст форум специального типа "блог" в каждом курсе, где пользователи оставляли записи в блоге, и скопирует те записи в этот форум.</p>
<p> Блоги затем будут полностью отключены на уровне сайта. Блоги без записей будут удалены.</p>
<p> Вы можете запустить скрипт, посетив <a href="{$a->fixurl}"> страницу обновления уровня блога</a>.</p>';
$string['bloglevelupgradeinfo'] = 'Видимость блога была упрощена в 2.0, но ваш сайт все еще использует один из старых типов видимости. Чтобы сохранить на своём сайте видимость записей блога, связанных с курсом или группой, Вам необходимо запустить следующий скрипт обновления. Он создаст форум специального типа "блог" в каждом курсе, где пользователи оставляли записи в блоге, и скопирует те записи в этот форум. Блоги затем будут полностью отключены на уровне сайта. Блоги без записей будут удалены.';
$string['bloglevelupgradeprogress'] = 'Состояние преобразования: рассмотрено пользователей - {$a->userscount}, преобразовано записей - {$a->blogcount}.';
$string['pluginname'] = 'Обновление видимости блогов';
