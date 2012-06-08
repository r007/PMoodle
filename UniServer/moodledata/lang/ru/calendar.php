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
 * Strings for component 'calendar', language 'ru', branch 'MOODLE_22_STABLE'
 *
 * @package   calendar
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['advancedoptions'] = 'Дополнительные параметры';
$string['allday'] = 'Все дни';
$string['calendar'] = 'Календарь';
$string['calendarheading'] = '{$a} Календарь';
$string['calendarpreferences'] = 'Настройки календаря';
$string['clickhide'] = 'нажмите, чтобы скрыть';
$string['clickshow'] = 'нажмите, чтобы показать';
$string['commontasks'] = 'Настройки';
$string['confirmeventdelete'] = 'Вы уверены, что необходимо удалить это событие?';
$string['course'] = 'Курс';
$string['courseevent'] = 'Событие курса';
$string['courseevents'] = 'События курса';
$string['courses'] = 'Курсы';
$string['dayview'] = 'Дневной обзор';
$string['daywithnoevents'] = 'В этот день не было никаких событий.';
$string['default'] = 'По умолчанию';
$string['deleteevent'] = 'Удалить событие';
$string['deleteevents'] = 'Удалить события';
$string['detailedmonthview'] = 'Детальный месячный обзор';
$string['durationminutes'] = 'Продолжительность в минутах';
$string['durationnone'] = 'Без продолжительности';
$string['durationuntil'] = 'До';
$string['editevent'] = 'Редактирование события';
$string['errorbeforecoursestart'] = 'Событие не может быть установлено ранее даты начала курса';
$string['errorinvaliddate'] = 'Неверная дата';
$string['errorinvalidminutes'] = 'Определите продолжительность в минутах, задавая число между 1 и 999.';
$string['errorinvalidrepeats'] = 'Определите количество событий, задавая число между 1 и 99.';
$string['errornodescription'] = 'Требуется описание';
$string['errornoeventname'] = 'Требуется название';
$string['eventdate'] = 'Дата';
$string['eventdescription'] = 'Описание';
$string['eventduration'] = 'Продолжительность';
$string['eventendtime'] = 'Время окончания';
$string['eventinstanttime'] = 'Время';
$string['eventkind'] = 'Тип события';
$string['eventname'] = 'Название';
$string['eventnone'] = 'Нет событий';
$string['eventrepeat'] = 'Повторения';
$string['eventsall'] = 'Все события';
$string['eventsfor'] = 'Событий: {$a}';
$string['eventskey'] = 'Легенда событий';
$string['eventsrelatedtocourses'] = 'События курса';
$string['eventstarttime'] = 'Время начала';
$string['eventtime'] = 'Время';
$string['eventview'] = 'Подробности события';
$string['expired'] = 'Истекли';
$string['explain_site_timeformat'] = 'Вы можете включить вывод времени в 12- или 24-часовом формате на всём сайте. При выборе значения "по умолчанию", формат будет автоматически выбираться в зависимости от используемого языка. Пользователи могут переопределить для себя значение этого параметра.';
$string['export'] = 'Экспортировать';
$string['exportbutton'] = 'Экспорт';
$string['exportcalendar'] = 'Экспортировать события';
$string['for'] = 'за';
$string['fri'] = 'Пт';
$string['friday'] = 'Пятница';
$string['generateurlbutton'] = 'Получить адрес календаря';
$string['global'] = 'Общее';
$string['globalevent'] = 'Общее событие';
$string['globalevents'] = 'Общие события';
$string['gotocalendar'] = 'Перейти к календарю';
$string['group'] = 'Группа';
$string['groupevent'] = 'Групповое событие';
$string['groupevents'] = 'Групповые события';
$string['hidden'] = 'скрыто';
$string['invalidtimedurationminutes'] = 'Вы ввели неверное значение длительности в минутах. Введите значение больше 0 или не указывайте значение.';
$string['invalidtimedurationuntil'] = 'Установленный период для события оказывается раньше времени его начала. Пожалуйста, исправьте это перед продолжением.';
$string['iwanttoexport'] = 'Экспорт';
$string['manyevents'] = 'Событий: {$a}';
$string['mon'] = 'Пн';
$string['monday'] = 'Понедельник';
$string['monthlyview'] = 'Месячный обзор';
$string['monthnext'] = 'Следующий месяц';
$string['monththis'] = 'Этот месяц';
$string['newevent'] = 'Новое событие';
$string['noupcomingevents'] = 'Нет предстоящих событий';
$string['oneevent'] = '1 событие';
$string['preferences'] = 'Настройки';
$string['preferences_available'] = 'Ваши личные настройки';
$string['pref_lookahead'] = 'Интервал отображения предстоящих событий';
$string['pref_lookahead_help'] = 'Этот параметр устанавливает период в днях, которых будет использоваться при выводе предстоящих событий. События, наступающие позже указанного инетравала, не будут отображаться в списке приближающихся событий. Учтите, <strong>нет никаких гарантий</strong>, что будут выводиться все события, наступающие в указанный период времени. Если событий слишком много (больше, чем значение параметра "Максимальное число наступающих событий"), то будут отображаться только наиболее близкие события.';
$string['pref_maxevents'] = 'Максимальное число предстоящих событий';
$string['pref_maxevents_help'] = 'Этот параметр отвечает за максимальное число отображаемых предстоящих событий. Если указать большое число, то не исключено, что наступающие события будут занимать много места на странице.';
$string['pref_persistflt'] = 'Запомнить настройки фильтра';
$string['pref_persistflt_help'] = 'При включении данного параметра Moodle будет помнить ваши настройки фильтра последних событий и автоматически восстанавливать их при каждой Вашей авторизации.';
$string['pref_startwday'] = 'Первый день недели';
$string['pref_startwday_help'] = 'Недели в календаре будут начинаться с выбранного здесь дня.';
$string['pref_timeformat'] = 'Формат времени';
$string['pref_timeformat_help'] = 'Вы можете выбрать формат отображения времени: 12- или 24-часовой. Если вы выберете настройку "по умолчанию", то формат будет автоматически выбираться в зависимости от используемого на сайте языка.';
$string['quickdownloadcalendar'] = 'Быстрая загрузка/подписка на календарь';
$string['recentupcoming'] = 'Сегодня и ближайшие 60 дней';
$string['repeatedevents'] = 'Повторяющиеся события';
$string['repeateditall'] = 'Сохранить изменения для всех повторяющихся событий этой серии';
$string['repeateditthis'] = 'Сохранить изменения только для этого события';
$string['repeatevent'] = 'Повторять это событие';
$string['repeatnone'] = 'Не повторять';
$string['repeatweeksl'] = 'Повторять еженедельно, создавать для всех';
$string['repeatweeksr'] = 'события';
$string['sat'] = 'Сб';
$string['saturday'] = 'Суббота';
$string['shown'] = 'показано';
$string['spanningevents'] = 'События в стадии реализации';
$string['sun'] = 'Вс';
$string['sunday'] = 'Воскресенье';
$string['thu'] = 'Чт';
$string['thursday'] = 'Четверг';
$string['timeformat_12'] = '12-часовой';
$string['timeformat_24'] = '24-часовой';
$string['today'] = 'Сегодня';
$string['tomorrow'] = 'Завтра';
$string['tt_deleteevent'] = 'Удалить событие';
$string['tt_editevent'] = 'Редактировать событие';
$string['tt_hidecourse'] = 'События курса отображаются (нажмите, чтобы скрыть)';
$string['tt_hideglobal'] = 'Общие события отображаются (нажмите, чтобы скрыть)';
$string['tt_hidegroups'] = 'Групповые события отображаются (нажмите, чтобы скрыть)';
$string['tt_hideuser'] = 'События пользователя отображаются (нажмите, чтобы скрыть)';
$string['tt_showcourse'] = 'События курса скрыты (нажмите, чтобы показать)';
$string['tt_showglobal'] = 'Общие события скрыты (нажмите, чтобы показать)';
$string['tt_showgroups'] = 'Групповые события скрыты (нажмите, чтобы показать)';
$string['tt_showuser'] = 'События пользователя скрыты (нажмите, чтобы показать)';
$string['tue'] = 'Вт';
$string['tuesday'] = 'Вторник';
$string['typecourse'] = 'События курса';
$string['typegroup'] = 'Событие группы';
$string['typesite'] = 'Событие сайта';
$string['typeuser'] = 'Событие пользователя';
$string['upcomingevents'] = 'Предстоящие события';
$string['urlforical'] = 'Ссылка для экспорта календаря, для подписки на календарь';
$string['user'] = 'Пользователь';
$string['userevent'] = 'Событие пользователя';
$string['userevents'] = 'События пользователя';
$string['wed'] = 'Ср';
$string['wednesday'] = 'Среда';
$string['weeknext'] = 'Следующая неделя';
$string['weekthis'] = 'Эта неделя';
$string['yesterday'] = 'Вчера';
$string['youcandeleteallrepeats'] = 'Это событие входит в серию повторяющихся событий. Вы можете удалить только это событие или сразу все события ({$a}) этой серии.';
