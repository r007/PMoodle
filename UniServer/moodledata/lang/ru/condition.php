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
 * Strings for component 'condition', language 'ru', branch 'MOODLE_22_STABLE'
 *
 * @package   condition
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcompletions'] = 'Добавить проверок выполнения элементов - {no}';
$string['addgrades'] = 'Добавить проверок оценок - {no}';
$string['availabilityconditions'] = 'Ограничить доступ';
$string['availablefrom'] = 'Разрешить доступ с';
$string['availablefrom_help'] = 'Даты "Разрешить доступ с / Запретить доступ после" определяют, когда студенты могут получить доступ к элементу курса по ссылку со страницы курса.

Разница между этими параметрами и параметром "Доступность" является то, что вне указанного диапазона дат студенты смогут видеть описание элемента, тогда как параметр "Доступность" предотвращают доступ полностью.';
$string['availableuntil'] = 'Запретить доступ после';
$string['badavailabledates'] = 'Некорректные даты. Если Вы устанавливаете оба условия, то дата "Разрешить доступ с" должна быть перед датой "Запретить доступ после".';
$string['completion_complete'] = 'элемент должен быть отмечен как выполненный';
$string['completioncondition'] = 'Проверка выполнения элемента';
$string['completioncondition_help'] = 'Эти настройки определяют, какие условия должны выполняться для получения доступа к данному элементу. Помните, что для установки проверок нужно предварительно включить отслеживание выполнения элементов.

Можно установить несколько условий, но в этом случае доступ к элементу будет дан только при выполнении <strong>всех</strong> условий.';
$string['completion_fail'] = 'элемент должен быть завершён с оценкой ниже проходного балла';
$string['completion_incomplete'] = 'элемент не должен быть отмечен как выполненный';
$string['completion_pass'] = 'элемент должен быть завершён с оценкой выше проходного балла';
$string['configenableavailability'] = 'При включении этого параметра Вы сможете ограничивать доступ к элементам курса в зависимости от даты, оценок или выполнения других элементов.';
$string['enableavailability'] = 'Включить ограничение доступа в зависимости от условий';
$string['grade_atleast'] = 'Оценка должна быть как минимум';
$string['gradecondition'] = 'Проверка оценки';
$string['gradecondition_help'] = 'Эти настройки определяют, какие условия, связанные с оценками пользователя, должны выполняться, чтобы он получил доступ к элементу.

Можно установить несколько условий, но в этом случае доступ к элементу будет разрешён только при выполнении <strong>всех</strong> условий.';
$string['grade_upto'] = 'и меньше, чем';
$string['none'] = '(нет)';
$string['notavailableyet'] = 'Еще не доступно';
$string['requires_completion_0'] = 'Не доступно, если элемент <strong>{$a}</strong> не завершён.';
$string['requires_completion_1'] = 'Не доступно, пока элемент <strong>{$a}</strong> не отмечен завершённым.';
$string['requires_completion_2'] = 'Не доступно, пока элемент <strong>{$a}</strong> не завершён с проходным баллом.';
$string['requires_completion_3'] = 'Не доступно, если элемент <strong>{$a}</strong> завершён с оценкой ниже проходного балла.';
$string['requires_date'] = 'Доступно с {$a}.';
$string['requires_date_before'] = 'Доступно до {$a}.';
$string['requires_date_both'] = 'Доступно с {$a->from} до {$a->until}.';
$string['requires_grade_any'] = 'Не доступно, пока у Вас нет оценки в <strong>{$a}</strong>.';
$string['requires_grade_max'] = 'Не доступно, пока Вы не получите подходящую оценку в <strong>{$a}</strong>.';
$string['requires_grade_min'] = 'Не доступно, пока Вы не получите необходимую оценку в <strong>{$a}</strong>.';
$string['requires_grade_range'] = 'Не доступно, пока Вы не получите оценку в <strong>{$a}</strong>.';
$string['showavailability'] = 'Прежде, чем появится доступ к элементу курса';
$string['showavailability_hide'] = 'Полностью скрыть этот элемент';
$string['showavailability_show'] = 'Отображать элемент в затенённом виде с информацией об ограничении';
$string['userrestriction_hidden'] = 'Ограничено (полностью скрыто, без сообщения): "{$a}"';
$string['userrestriction_visible'] = 'Ограничено: "{$a}"';
