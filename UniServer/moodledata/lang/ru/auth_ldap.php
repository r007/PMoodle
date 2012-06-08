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
 * Strings for component 'auth_ldap', language 'ru', branch 'MOODLE_22_STABLE'
 *
 * @package   auth_ldap
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['auth_ldap_bind_dn'] = 'Если Вы хотите связанного пользователя для поиска пользователей, укажите это здесь. Например, \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Пароль для связывания пользователя.';
$string['auth_ldap_changepasswordurl_key'] = 'Адрес страницы смены пароля';
$string['auth_ldap_contexts'] = 'Список контекстов, где расположены пользователи . Отделите различные контексты \';\'. Например: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Если Вы разрешили создание пользователей при подтверждении по почте, определите контекст, в который будут заводиться пользователи. Этот контекст должен отличаться от других, чтобы предотвратить проблемы безопасности. Нет необходимости добавлять, название контекста к ldap_context-переменным, система будет искать пользователей от этого контекста автоматически.';
$string['auth_ldap_creators'] = 'Список групп, членам которых разрешается создавать новые курсы. Список групп разделяйте с помощью \';\'. Например,\'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldapdescription'] = 'Этот метод обеспечивает аутентификацию с помощью LDAP-сервера. Если данный логин и пароль имеют силу, создаётся новая пользовательская учётная запись в базе данных системы. Этот модуль может читать поля от LDAP-сервера и заполнять требуемые области (поля) в системе. В дальнейшем проверяются только логин и пароль';
$string['auth_ldapextrafields'] = 'Эти поля дополнительные. Вы можете выбрать предварительное заполнение некоторых полей пользовательских данных от полей LDAP, указанного здесь. <p>Не заполняйте это поле, для того чтобы не переносить данные с LDAP.</p><p>В любом случае, пользователь сможет редактировать все поля после того, как он зайдет в систему.</p>';
$string['auth_ldap_host_url'] = 'Укажите сервер LDAP в формате URL, например \'ldap://ldap.myorg.com/\' или \'ldaps://ldap.myorg.com/\'. Для обеспечения бесперебойной работы можно указать несколько серверов, разделив их знаком ";".';
$string['auth_ldap_memberattribute'] = 'Определите пользовательский атрибут, определяющий принадлежность пользователя к группе. Обычно \'участник\'';
$string['auth_ldap_search_sub'] = 'Укажите значения <> 0 если Вам нравится искать пользователей по субконтекстам.';
$string['auth_ldap_update_userinfo'] = 'Обновляйте пользовательскую информацию (Имя, Фамилию, адрес ..) от LDAP до системы. Смотрите /auth/ldap/attr_mappings.php для того, чтобы отобразить информацию.';
$string['auth_ldap_user_attribute'] = 'Атрибут, используемый для имя/поиск. Обычно, \'cn\'.';
$string['auth_ldap_version'] = 'Версия LDAP протокола, которую использует Ваш сервер.';
$string['noemail'] = 'Отправить вам письмо не удалось!';
$string['pluginname'] = 'Сервер LDAP';
