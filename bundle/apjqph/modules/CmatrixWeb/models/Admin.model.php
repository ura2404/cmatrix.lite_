<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class Admin extends Comlogin implements web\iModel {
    public function getData(){
        
        return arrayMergeReplace(parent::getData(),[
            'app' => [
                'name' => 'Admin`ка'
            ],
            'blocks' => $this->getMyBlocks(),
            
            'url' => [
                'dictionaries' => CM_WHOME.'/admin/dictionaries',
                'tables' => CM_WHOME.'/admin/tables',
                'messages' => CM_WHOME.'/admin/messages',
                'setup' => CM_WHOME.'/admin/setup'
            ]
        ]);
    }
    
    // --- --- --- --- ---
    private function getMyBlocks(){
        return [
            [
                'enable' => true,
                'visible' => true,
                'code' => 'data',
                'name' => 'Данные (Data managment)',
                'icon' => 'fab fa-elementor',
                'info' => 'Управление данными, учитываемыми системой',
                'url' => CM_WHOME.'/admin/data',
            ],
            [
                'enable' => false,
                'visible' => true,
                'code' => 'table',
                'name' => 'Таблицы',
                'icon' => 'fas fa-table',
                'info' => 'Пользовательские таблицы',
                'url' => CM_WHOME.'/admin/tables',
            ],
            [
                'enable' => false,
                'visible' => true,
                'code' => 'task',
                'name' => 'Задания',
                'icon' => 'fas fa-tasks',
                'info' => 'Управление заданиями',
                'url' => CM_WHOME.'/admin/tasks',
            ],
            [
                'enable' => false,
                'visible' => true,
                'code' => 'message',
                'name' => 'Сообщения',
                'icon' => 'far fa-comments',
                'info' => 'Управление чатами и сообщениями',
                'url' => CM_WHOME.'/admin/messages',
            ],
            [
                'enable' => false,
                'visible' => true,
                'code' => 'project',
                'name' => 'Проекты',
                'icon' => 'fas fa-user-friends',
                'info' => 'Управление проектами, группами пользователей с локальнымы чатом и заданиями',
                'url' => CM_WHOME.'/admin/messages',
            ],
            [
                'enable' => false,
                'visible' => true,
                'code' => 'file',
                'name' => 'Мои файлы',
                'icon' => 'far fa-copy',
                'info' => 'Управление файлами',
                'url' => CM_WHOME.'/admin/files',
            ],
            [
                'enable' => false,
                'visible' => true,
                'code' => 'setup',
                'name' => 'Настройки',
                'icon' => 'fas fa-cogs',
                'info' => 'Пользовательские настройки системы',
                'url' => CM_WHOME.'/admin/setup',
            ],
            /*[
                'code' => 'setup',
                'name' => 'Настройки',
                'icon' => 'fas fa-cogs',
                'info' => 'Пользовательские настройки системы',
                'url' => CM_WHOME.'/admin/setup',
            ],
            [
                'code' => 'setup',
                'name' => 'Настройки',
                'icon' => 'fas fa-cogs',
                'info' => 'Пользовательские настройки системы',
                'url' => CM_WHOME.'/admin/setup',
            ],
            [
                'code' => 'setup',
                'name' => 'Настройки',
                'icon' => 'fas fa-cogs',
                'info' => 'цуйцуйцуйцу йцуйцу йцжд ойцой    лоцудуццрукро цоукрлцуркуц клдоцу лоуцклрцуркуцр лоцу клрцлку rthrthrh rthrthreherherhth rerth rth h',
                'url' => CM_WHOME.'/admin/setup',
            ]*/
        ];
    }
}
?>