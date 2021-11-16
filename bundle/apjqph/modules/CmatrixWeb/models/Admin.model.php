<?php
namespace CmatrixWeb\Models;
use \CmatrixCore as co;
use \CmatrixWeb as web;

class Admin extends AdminCommon implements web\iModel {
    public function getData(){
        
        return arrayMergeReplace(parent::getData(),[
            'app' => [
                'module' => 'Admin`ка'
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
                'visible' => co\Sysuser::instance()->isMyGroups('coAdmins','coSupervisors'),
                'code' => 'data',
                'name' => 'Данные (Data managment)',
                'icon' => 'fab fa-elementor',
                'info' => 'Управление данными, учитываемыми системой',
                'url' => CM_WHOME.'/admin/data',
            ],
            [
                'enable' => false,
                'visible' => co\Sysuser::instance()->isMyGroups('coAdmins','coSupervisors'),
                'code' => 'sysuser',
                'name' => 'Пользователи',
                'icon' => 'fas fa-user',
                'info' => 'Управление пользователями и группами',
                'url' => CM_WHOME.'/admin/data',
            ],
            [
                'enable' => false,
                'visible' => co\Sysuser::instance()->isMyGroups('coAdmins','coSupervisors'),
                'code' => 'sysrole',
                'name' => 'Роли пользователей',
                'icon' => 'fas fa-user-tag',
                'info' => 'Управление ролями пользователей',
                'url' => CM_WHOME.'/admin/data',
            ],
            [
                'enable' => false,
                'visible' => true,
                'code' => 'module',
                'name' => 'Модули',
                'icon' => 'far fa-object-ungroup',
                'info' => 'Управление модулями системы',
                'url' => CM_WHOME.'/admin/nodules',
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
                'visible' => co\Sysuser::instance()->isMyGroups('coProjects','coSupervisors','coProjects'),
                'code' => 'project',
                'name' => 'Проекты',
                'icon' => 'fas fa-user-friends',
                'info' => 'Управление проектами, группами пользователей с локальнымы чатом и заданиями',
                'url' => CM_WHOME.'/admin/messages',
            ],
            [
                'enable' => false,
                'visible' => true,
                'code' => 'table',
                'name' => 'Мои таблицы',
                'icon' => 'fas fa-table',
                'info' => 'Пользовательские таблицы',
                'url' => CM_WHOME.'/admin/tables',
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