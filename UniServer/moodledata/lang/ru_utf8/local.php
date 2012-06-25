<?php //file for incremental_backup_strings.

$string['incrementalbackups'] = 'Инкрементные резервные копии';
$string['backupincrementalshelp'] = 'Позволить создавать инкрементные резервные копии с помощью ручного архивирования';
$string['backupscheincrementalshelp'] = 'Запускать инкрементное резервирование как задачу по расписанию через cron';
$string['modules'] = 'Модули';
$string['backupincmoduleshelp'] = 'Какие модули включить в автоматическое архивирование';
$string['incrementalcourses'] = 'Инкрементные курсы';
$string['addincrementalcoursenote'] = 'Какие курсы включить в автоматические инкрементное архивирование';
$string['manualincrementals'] = 'Ручные инкременты';
$string['scheduledincrementals'] = 'Инкременты по расписанию';
$string['pathtoxdelta'] = 'Путь xdelta'; 
$string['configpathtoxdelta'] = 'Путь к Xdelta - обычно /usr/bin/xdelta на linux, или любой путь на windows';
$string['incserver'] ='Сервер обновлений';
$string['configincserver'] ='Адрес страницы обновления на сервере обновления для вашего сайта, например: http://localhost/moodle/backup/send_incremental.php';
$string['incrementalclient'] ='Инкрементный клиент';
$string['generateincrementals'] = 'Генерировать инкременты';
$string['uptodate'] ='Ваш курс уже обновлен!';
$string['updatecourse'] ='Обновить курс';
$string['updatecoursesuccess'] ='Этот курс успешно обновлён';
$string['restorethisfile'] = 'Восстановить этот файл';
$string['coursenotlinked'] = 'Этот курс не связан с главным сервером. - вы должны скачать новый курс и затем загрузить его здесь.<br/> Замечание: это удалит весь существующий контент в вашем курсе.'; 
$string['autofailed'] = 'Ошибка автоматического скачивания - вы должны обновить вручную';
$string['xdeltanotinstalled'] = 'XDELTA не установлен -  инкрементное резервирование не может быть запущено';
$string['errornobackup'] ='Нет текущих резервных копий этого курса для экспортирования. Попытка архивирования...';
$string['exportcourse'] = 'Экспорт курса';
$string['export'] = 'Экспорт';
$string['privateuserdata'] = 'Данные пользовательского профиля';
$string['backupalluserdata'] = 'Архивировать все пользовательские данные';
$string['hideprivatedata'] = 'Скрыть личные данные';
$string['privateuserdatahelp'] = 'Эта настройка устанавливает, включать ли такие вещи, как пароли и другую личную информацию в резервную копию';
$string['incrementalclientkeep'] ='Сохранять старые архивы';
$string['incrementalclientkeepinfo'] ='Установка этого значения позволит всем загруженным резервным копиям хранится локально';
$string['offlinemoodle'] = 'Мобильный Moodle';
$string['coursenotchanged'] = '<strong>Внимание: Этот курс не изменялся с момента последнего резервирования, так что эта резервная копия будет автоматически удалена.</strong>';
$string['existingcourseupdating'] ='Курс уже существует, обновляем';
$string['currentcourseupdating'] ='Текущий курс, обновляем';
?>