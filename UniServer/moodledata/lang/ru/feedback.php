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
 * Strings for component 'feedback', language 'ru', branch 'MOODLE_22_STABLE'
 *
 * @package   feedback
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['autonumbering_help'] = 'Включает или отключает автоматизированную нумерацию каждого вопроса.';
$string['choosefile'] = 'Выберите файл';
$string['depending_help'] = 'Зависимые элементы позволяют Вам указать элементы, зависящие от значения других элементов.<br />
<strong>Здесь приведены примеры их использования:</strong><br />
<ul>
<li>Сначала создайте элемент, от значения которого зависят другие элементы.</li>
<li>Затем добавьте разрыв страницы.</li>
<li>Затем добавьте элемент, зависящий от значения прежнего элемента<br />
Выберите в форме создания элемент в списке "зависит от элемента" и вставьте нужное значение в текстовое поле "зависит от значения".</li>
</ul>
<strong>Структура должна выглядеть следующим образом:</strong>
<ol>
<li>Вопрос элемента 1: У Вас есть автомобиль?
Ответ: да/нет</li>
<li>Разрыв страницы</li>
<li>Вопрос элемента 2: Какого цвета Ваш автомобиль?<br />
(этот элемент зависит от элемента 1 со значением=да)</li>
<li>Вопрос элемента 3: почему у Вас нет автомобиля?<br />
(этот элемент зависит от элемента 1 со значением=нет)</li>
<li> ... другие элементы</li>
</ol>
Вот и всё. Развлекайтесь!';
$string['description'] = 'Описание';
$string['edit_item'] = 'Редактировать вопрос';
$string['edit_items'] = 'Редактировать вопросы';
$string['email_notification'] = 'Рассылать уведомления по электронной почте';
$string['emailnotification_help'] = 'При включенном параметре администраторы получают уведомление электронной почты о представлении ответов Обратной связи.';
$string['entries_saved'] = 'Ваши ответы были сохранены. Спасибо.';
$string['export_to_excel'] = 'Экспорт в Excel';
$string['feedback:viewreports'] = 'Просматривать отчеты';
$string['insufficient_responses_help'] = 'Недостаточно ответов для этой группы.

Для поддержания ответов анонимными  должно быть представлено не менее 2 ответов.';
$string['item_label'] = 'Пояснение';
$string['label'] = 'Пояснение';
$string['mapcourse_help'] = 'По умолчанию, формы обратной связи, созданные на главной странице Вашего сайта, появятся во всех курсах, использующих блок "Обратная связь". Вы можете принудительно отображать форму обратной связи, делая блок закреплённым или ограничить отображения формы обратной связи только для определённых курсов.';
$string['mapcourses_help'] = 'После поиска и выбора соответствующего курса(ов) Вы можете связать их с этой Обратной связью, используя карту курсов. Несколько курсов можно выбрать, щёлкая на названиях курсов при нажатой клавише Ctrl или Apple. Курс может быть отделён от Обратной связи в любое время.';
$string['messageprovider:message'] = 'Напоминания о необходимости заполнения элементов типа "Обратная связь"';
$string['messageprovider:submission'] = 'Уведомления о получении ответов в элементах типа "Обратная связь"';
$string['modulename'] = 'Обратная связь';
$string['modulename_help'] = 'Модуль "Обратная связь" позволяет создавать собственные анкеты.';
$string['modulenameplural'] = 'Обратная связь';
$string['multichoice'] = 'Множественный выбор';
$string['multiplesubmit_help'] = 'Если включено для анонимных анкет, пользователи могут отправлять сообщения неограниченное число раз.';
$string['no_itemlabel'] = 'Нет пояснения';
$string['pluginadministration'] = 'Управление обратной связью';
$string['pluginname'] = 'Обратная связь';
$string['preview'] = 'Предварительный просмотр';
$string['preview_help'] = 'В режиме предварительного просмотра Вы можете изменять порядок вопросов.';
$string['questions'] = 'Вопросы';
$string['ready_feedbacks'] = 'Готовые отзывы';
$string['searchcourses_help'] = 'Поиск курса(ов) (по коду или названию), которые Вы хотите соединить с этой Обратной связью.';
$string['started'] = 'начало';
$string['timeclose_help'] = 'Вы можете указать время, когда Обратная связь доступна для ответа на вопросы. Если флажок не установлен - ограничения нет.';
$string['timeopen_help'] = 'Вы можете указать время, когда Обратная связь доступна для ответа на вопросы. Если флажок не установлен - ограничения нет.';
$string['url_for_continue_help'] = 'По умолчанию, после ответа на Обратную связь кнопка "Продолжить" переводит на страницу курса. Вы можете задать здесь другой адрес для перехода при нажатии кнопки "Продолжить".';
$string['viewcompleted_help'] = 'Вы можете просмотреть заполненные формы Обратной связи, доступен поиск по курсу и/или вопросу. Ответы могут быть экспортированы в Excel.';
