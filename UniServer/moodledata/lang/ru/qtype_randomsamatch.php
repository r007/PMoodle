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
 * Strings for component 'qtype_randomsamatch', language 'ru', branch 'MOODLE_22_STABLE'
 *
 * @package   qtype_randomsamatch
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addingrandomsamatch'] = 'Добавление "Случайного вопроса на соответствие"';
$string['editingrandomsamatch'] = 'Редактирование "Случайного вопроса на соответствие"';
$string['nosaincategory'] = 'В выбранной категории \'{$a->catname}\' нет вопросов типа "Короткий ответ". Выберите другую категорию или создайте несколько вопросов в этой категории.';
$string['notenoughsaincategory'] = 'В выбранной категории  \'{$a->catname}\' есть только {$a->nosaquestions} вопроса(ов) типа "Короткий ответ". Выберите другую категорию,  создайте еще несколько вопросов в этой категории или сократите количество выбранных вопросов.';
$string['randomsamatch'] = 'Случайный вопрос на соответствие';
$string['randomsamatch_help'] = 'Для студента данный вопрос выглядит так же, как вопрос на соответствие. Разница в том, что список соответствий составлен произвольным образом из вопросов типа "Короткий ответ", находящихся в выбранной категории. В этой категории должно быть достаточное количество неиспользованных вопросов типа "Короткий ответ", в противном случае будет отображаться сообщение об ошибке.';
$string['randomsamatchsummary'] = 'Вопрос подобен "Вопросу на соответствие", но создается из взятых случайным образом вопросов "Короткий ответ" из той или иной категории.';
