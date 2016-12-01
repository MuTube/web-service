<?php

class SettingCommandController extends CommonCommandController {
    public function defaultLoading() {
        $this->denyAccessWithoutOneOfPermissions(['settings_management', 'settings_list']);

        $this->data['openWindow'] = array_key_exists('w', $this->params['get']) ? $this->params['get']['w'] : false;

        $this->setTemplate('setting/settings.html.twig');
    }

    public function getWindow() {
        $twigController = new TwigController('template');
        $setting = $this->params['post']['setting'];
        $methodName = 'get'.ucfirst($setting).'Data';

        $data = method_exists($this, $methodName) ? $this->$methodName() : [];
        $data['requestPermissions'] = ['settings_management', 'settings_'.$setting.'_edit'];
        $data['userPermissions'] = $this->data['userPermissions'];

        $output = $twigController->getRenderReadyTemplate('setting/settingWindows/' . $setting . 'SettingWindow.html.twig', $data);

        $this->isAjaxCall() ? $this->renderAjax($output) : $this->setNoContent();
    }

    protected function getPermissionsData() {
        return ['roles' => DbController::getTable('userRole')->getListWithPermissionIds()];
    }
}