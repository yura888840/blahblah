<?php
/**
 * Created by PhpStorm.
 * User: Kostja
 * Date: 14.10.15
 * Time: 11:45
 */
namespace Crm\Permissions;

use Phalcon\Acl;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Role;


class SecurityPlugin extends Plugin
{
    private function getUserRole()
    {
        $identity = \Phalcon\DI::getDefault()->get('auth')->getIdentity();
        // default role
        $role = 'guest';
        $availableRoles = \Phalcon\DI::getDefault()->get('config')['permissions']['availableRoles']->toArray();
        if (!empty($identity) && is_array($identity) && array_key_exists('profile', $identity)
            && in_array($identity['profile'], $availableRoles)
        ) {
            $role = $identity['profile'];
        }
        return $role;
    }

    //все запросы проходят через этот метод
    //тут делаем все проверки на разрешение пользователям(ролям) использовать ресурсы
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        if (!\Phalcon\DI::getDefault()->get('config')['permissions']['check']) {
            return true;
        };
        $role = $this->getUserRole();

        $moduleName = $dispatcher->getModuleName();
        if ($moduleName=='admin') {
            // Получаем список ACL для админ модуля, если идет запрос к админке
            $nameAclService = 'aclCrmAdmin';
            $nameConfigAcl = 'config_permissions_admin_module.php';
            $acl = $this->getAcl($nameAclService, $nameConfigAcl);
        } else {
            // Получаем список ACL для доступа ко всем контроллерам, кроме админки
            $nameAclService = 'aclCrm';
            $nameConfigAcl = 'config_permissions.php';
            $acl = $this->getAcl($nameAclService, $nameConfigAcl);
        }
        $controllerName = $dispatcher->getControllerName();
        $actionName = $dispatcher->getActionName();

        //всех гостей перенаправляем на авторизацию
        //нечего им в CRM делать
        if ($role == 'guest' and $controllerName!='auth') {
            $response = new \Phalcon\Http\Response;
            $response->redirect('login');
            $response->send();
            return true;
        }

        //пока проверка только на доступ к просмотру
        //@todo доделать проверку доступов на создание и изменение
        $allowController = $acl->isAllowed($role, $controllerName, "view");
        $allowAction = $acl->isAllowed($role, $controllerName.'-'.$actionName, "view");

        if ($allowController && $allowAction) {//доступ разрешен
            //ничего особо не делаем просто пропускаем запрос дальше на выполнение
        } else {//доступ запрещен
            $request = new \Phalcon\Http\Request();
            if ($request->isAjax()) {//обработка Ajax ответа 403
                $this->view->disable();
                header('HTTP/1.0 403 Forbidden');
                header('Content-type: application/json');
                echo json_encode(['success' => false,
                    'reason' => 'You don\'t have access to this module: ' . $controllerName . ':' . $actionName]);
                return false;
            }
            //перенаправляем на login
            if ($controllerName!='auth'){
                $response = new \Phalcon\Http\Response;
                $response->redirect('');
                $response->send();
                return true;
            }
        }
        return true;
    }

    public function checkDbFieldsAccess($event, $model)
    {
        $modelName = get_class($model);
        $modelNameShort = strtolower(substr($modelName,11));
        $eventType = $event->getType();
        $role = $this->getUserRole();
        //
        if ($eventType == 'beforeSave') {
            $nameAclService = 'aclCrmCollection';
            $nameConfigAcl = 'config_permissions_db.php';
            $acl = $this->getAcl($nameAclService, $nameConfigAcl);
            //перебор полей документа и проверка разрешений на операцию с полем
            foreach ($model as $fieldName => $newValue) {
                if (substr($fieldName,0,1) != '_') {
                    //no access
                    if (!$acl->isAllowed($role, $modelNameShort.'-'.$fieldName, "update")) {
                        $modelSavedInDb = new $modelName;
                        $modelSavedInDb = $modelSavedInDb::findById((string)$model->getId());
                        //если документ новый
                        if (!$modelSavedInDb) {
                            $model->$fieldName = null;
                            $flashSession = \Phalcon\DI::getDefault()->get('flashSession');
                            $flashSession->error("Field: ".$fieldName." - not saved, access denny");
                        } else {
                            $oldValue = $modelSavedInDb->$fieldName;
                            if ($newValue != $oldValue) {
                                //поле которое запрещено редактировать оставляем как оно было до редактирования
                                $model->$fieldName = $oldValue;
                                $flashSession = \Phalcon\DI::getDefault()->get('flashSession');
                                $flashSession->error("Field: ".$fieldName." - not saved, access denny");
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    //загружает в стандартный ACL класс Phalcon все роли, ресурсы и разрешения/запрещеня доступов ролей к ресурсам
    //загрузка идет из конфигов config_permissions.php или config_permissions_admin_module.php
    //в зависимости от того к админке идет обращение или не к админке
    public function setRolesAndResourcesAndPermissionsFromConfig($nameAclService, $nameConfigAcl)
    {
        $dirConfig = \Phalcon\DI::getDefault()->get('dirConfig');

        $configPermissions = include $dirConfig . "/".$nameConfigAcl;
        $acl = \Phalcon\DI::getDefault()->get($nameAclService);

        // Указываем "запрещено" по умолчанию для тех объектов, которые не были занесены в список контроля доступа
        if ($resourcesPrivate = $configPermissions['permissions']['setDefaultDENY']) {
            $acl->setDefaultAction(\Phalcon\Acl::DENY);
        }

        //load Role from config
        $roles = \Phalcon\DI::getDefault()->get('config')['permissions']['availableRoles'];
        foreach ($roles as $role) {
            $acl->addRole($role);
        }

        //load Resource Public from config
        $resourcesPublic = $configPermissions['permissions']['resources']['public']->toArray();
        foreach ($resourcesPublic as $key => $operation) {
            $customersResource = new Resource($key);
            $acl->addResource($customersResource, $operation);
        }

        //load Resource Private from config
        $resourcesPrivate = $configPermissions['permissions']['resources']['private']->toArray();
        foreach ($resourcesPrivate as $key => $operation) {
            $customersResource = new Resource($key);
            $acl->addResource($customersResource, $operation);
        }

        //load allow from config
        $allows = $configPermissions['permissions']['allow']->toArray();
        foreach ($allows as $allow) {
            $acl->allow($allow[0], $allow[1], $allow[2]);
        }

        //load deny from config
        $denys = $configPermissions['permissions']['deny']->toArray();
        foreach ($denys as $deny) {
            $acl->deny($deny[0], $deny[1], $deny[2]);
        }

        return $acl;
    }

    public function getAcl($nameAclService, $nameConfigAcl)
    {
        //@todo добавить кэширование в Редисе всего $acl
        $acl = $this->setRolesAndResourcesAndPermissionsFromConfig($nameAclService, $nameConfigAcl);
        return $acl;
    }
}