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
 * Strings for component 'webservice', language 'ru', branch 'MOODLE_22_STABLE'
 *
 * @package   webservice
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accessexception'] = 'Исключительная ситуация контроля доступа';
$string['actwebserviceshhdr'] = 'Активные протоколы веб-служб';
$string['addaservice'] = 'Добавить службу';
$string['addcapabilitytousers'] = 'Проверить права пользователей';
$string['addcapabilitytousersdescription'] = 'Пользователи должны иметь два права - "webservice:createtoken" и право, связанное с используемым протоколом, например, "webservice/rest:use", "webservice/soap:use". Для достижения этой цели создайте роль "web services", дайте ей соответствующие права и назначьте её пользователю "web services" как глобальную роль.';
$string['addfunction'] = 'Добавить функцию';
$string['addfunctionhelp'] = 'Выберите функцию, добавляемую к службе.';
$string['addfunctions'] = 'Добавить функции';
$string['addfunctionsdescription'] = 'Выберите необходимые функции для вновь созданной службы.';
$string['addrequiredcapability'] = 'Назначение (отмена назначения) необходимых прав';
$string['addservice'] = 'Добавить новую службу: {$a->name} (id: {$a->id})';
$string['addservicefunction'] = 'Добавить функции службы "{$a}"';
$string['allusers'] = 'Все пользователи';
$string['amftestclient'] = 'Клиент для тестирования AMF';
$string['arguments'] = 'Аргументы';
$string['authmethod'] = 'Способ аутентификации';
$string['checkusercapability'] = 'Проверить права пользователя';
$string['checkusercapabilitydescription'] = 'У пользователя должны быть соответствующие права, связанные с используемыми протоколами, например "webservice/rest:use", "webservice/soap:use". Для этого создайте роль "web services", дайте ей соответствующие права и назначьте её пользователю "web services" как глобальную роль.';
$string['configwebserviceplugins'] = 'По соображениям безопасности следует включать только те протоколы, которые действительно используются.';
$string['context'] = 'Контекст';
$string['createservicedescription'] = 'Задайте набор функций веб-службы. Это позволит пользователю получить доступ к новой службе. На странице <strong>Добавить службу</strong> отметьте параметры "Включено" и " Только авторизованные пользователи". Выберите вариант "Никакие дополнительные права не требуются".';
$string['createserviceforusersdescription'] = 'Задайте набор функций веб-службы. Это позволит пользователям получить доступ к новой службе. На странице <strong>Добавить службу</strong> отметьте параметр "Включена". НЕ отмечайте параметр "Только авторизованные пользователи" . Выберите вариант "Никакие дополнительные права не требуются".';
$string['createtoken'] = 'Создать ключ';
$string['createtokenforuser'] = 'Создать ключ для пользователя';
$string['createtokenforuserauto'] = 'Автоматически создать ключ для пользователя';
$string['createtokenforuserdescription'] = 'Создать ключ для пользователя веб-службы';
$string['createuser'] = 'Создать специального пользователя';
$string['createuserdescription'] = 'Для представления системы, которая будет управлять Moodle требуется специальный пользователь.';
$string['default'] = 'По умолчанию - "{$a}"';
$string['deleteaservice'] = 'Удалить службу';
$string['deleteservice'] = 'Удалить службу: {$a->name} (id: {$a->id})';
$string['deleteserviceconfirm'] = 'Удаление службы приведёт к удалению всех ключей, связанных с этой службой. Вы действительно хотите удалить внешнюю службу "{$a}"?';
$string['deletetokenconfirm'] = 'Вы действительно хотите удалить ключ пользователя <strong>{$a->user}</strong> для веб-службы <strong>"{$a->service}"</strong>?';
$string['disabledwarning'] = 'Все протоколы веб-служб отключены. Параметр "Включить веб-службы" можно на странице "Администрирование - Расширенные возможности".';
$string['doc'] = 'Документация';
$string['docaccessrefused'] = 'Вам не разрешено просматривать документацию, свзанную с этим ключом.';
$string['documentation'] = 'документация веб-службы';
$string['editaservice'] = 'Редактировать службу';
$string['editservice'] = 'Редактировать службу: {$a->name} (id: {$a->id})';
$string['enabled'] = 'Включена';
$string['enabledocumentation'] = 'Включить информацию для разработчиков';
$string['enabledocumentationdescription'] = 'Для включенных протоколов доступна подробная документация по использованию веб-службах.';
$string['enablemobilewsoverview'] = 'Перейдите на страницу управления "{$a->manageservicelink}", отметьте параметр "{$a->enablemobileservice}" и Сохраните. В этом случае все пользователи сайта смогут использовать официальное приложения Moodle. Текущий состояние: {$a->wsmobilestatus}.';
$string['enableprotocols'] = 'Включить протоколы';
$string['enableprotocolsdescription'] = 'Необходимо включить хотя бы один протокол. По соображениям безопасности следует включать только те протоколы, которые будут использоваться.';
$string['enablews'] = 'Включить веб-службы';
$string['enablewsdescription'] = 'Необходимо включить веб-службы на странице "Администрирование-Расширенные возможности".';
$string['entertoken'] = 'Введите ключ безопасности:';
$string['error'] = 'Ошибка: {$a}';
$string['errorcatcontextnotvalid'] = 'Вы не можете выполнять функции в контексте категории (id категории:{$a->catid}). Сообщение об ошибке контекста: {$a->message}';
$string['errorcodes'] = 'Сообщение об ошибке';
$string['errorcoursecontextnotvalid'] = 'Вы не можете выполнять функции в контексте курса (id курса:{$a->catid}). Сообщение об ошибке контекста: {$a->message}';
$string['errorinvalidparam'] = 'Недопустимый параметр "{$a}".';
$string['errornotemptydefaultparamarray'] = 'Параметр с именем "{$a}" в описании веб-службы является одиночной или множественной структурой. По умолчанию может использоваться только пустой массив. Сверьтесь с документацией веб-службы.';
$string['erroroptionalparamarray'] = 'Параметр с именем "{$a}" в описании веб-службы является одиночной или множественной структурой. Он не может быть установлен в виде VALUE_OPTIONAL. Проверьте описание веб-службы.';
$string['execute'] = 'Выполнить';
$string['executewarnign'] = 'ВНИМАНИЕ: Если Вы нажмёте "Выполнить", то база данных будет изменена и изменения не смогут быть отменены автоматически!';
$string['externalservice'] = 'Внешняя служба';
$string['externalservicefunctions'] = 'Функции внешних служб';
$string['externalservices'] = 'Внешние службы';
$string['externalserviceusers'] = 'Пользователи внешней службы';
$string['failedtolog'] = 'Не удалось войти';
$string['forbiddenwsuser'] = 'Нельзя создать ключ для неподтверждённой, удалённой, заблокированной или гостевой учётной записи.';
$string['function'] = 'Функция';
$string['functions'] = 'Функции';
$string['generalstructure'] = 'Основная структура';
$string['information'] = 'Информация';
$string['installexistingserviceshortnameerror'] = 'Веб-служба с кратким названием "{$a}" уже существует. Невозможно установить/обновить другую веб-службу с этим кратким названием.';
$string['installserviceshortnameerror'] = 'Ошибка кодирования: краткое название службы "{$a}" должно содержать только цифры, буквы и знаки "_", "-", "..".';
$string['invalidiptoken'] = 'Ключ недействителен - этот IP-адрес не разрешён';
$string['invalidtimedtoken'] = 'Ключ недействителен - ключ истёк';
$string['invalidtoken'] = 'Ключ недействителен - ключ не найден.';
$string['invalidtokensession'] = 'Сеанс с использованием ключа недействителен - сеанс не найден или истёк.';
$string['iprestriction'] = 'Ограничение IP-адресов';
$string['iprestriction_help'] = 'Пользователю нужно будет вызвать веб-службу с перечисленных IP-адресов.';
$string['key'] = 'Ключ';
$string['keyshelp'] = 'Ключи используются для доступа из внешних приложений к Вашему аккаунту Moodle.';
$string['manageprotocols'] = 'Управление протоколами';
$string['managetokens'] = 'Управление ключами';
$string['missingcaps'] = 'Отсутствуют права';
$string['missingcaps_help'] = 'Список необходимых прав для использования сервиса, которые отсутствуют у выбранного пользователя. Недостающие права должны быть добавлены к роли пользователя, чтобы он мог использовать службу.';
$string['missingpassword'] = 'Отсутствует пароль';
$string['missingusername'] = 'Отсутствует логин';
$string['missingversionfile'] = 'Ошибка в исходном коде: отсутствует файл version.php для компонента "{$a}"';
$string['mobilewsdisabled'] = 'Отключено';
$string['mobilewsenabled'] = 'Включено';
$string['norequiredcapability'] = 'Никакие дополнительные права не требуются';
$string['notoken'] = 'Список ключей безопасности пуст';
$string['onesystemcontrolling'] = 'Одна система с ключом безопасности контролирует Moodle';
$string['operation'] = 'Операция';
$string['optional'] = 'Необязательно';
$string['print'] = 'Печатать всё';
$string['protocol'] = 'Протокол';
$string['requiredcapability'] = 'Необходимо иметь право';
$string['requiredcapability_help'] = 'Если параметр установлен, то доступ к службе смогут получить только пользователи, имеющие указанное здесь право.';
$string['requiredcaps'] = 'Необходимые права';
$string['restrictedusers'] = 'Только авторизованные пользователи';
$string['restrictedusers_help'] = 'Этот параметр определяет, смогут ли все пользователи с правом создания ключа веб-службы создвать ключ для этой службы на странице управления ключами безопасности, или же только авторизованные пользователи смогут это делать.';
$string['securitykey'] = 'Ключ безопасности';
$string['securitykeys'] = 'Ключи безопасности';
$string['selectauthorisedusers'] = 'Выберите авторизованных пользователей';
$string['selectservice'] = 'Выбрать службу';
$string['step'] = 'Шаг';
$string['validuntil_help'] = 'Если параметр установлен, то для этого пользователя служба будет отключена после указанной даты';
$string['webservice'] = 'Веб-служба';
$string['webservices'] = 'Веб-службы';
$string['webservicesoverview'] = 'Обзор';
$string['webservicetokens'] = 'Ключи веб-службы';
$string['wsaccessusersuspended'] = 'В доступе к веб-службе отказано, так как учётная запись заблокирована: {$a}';
