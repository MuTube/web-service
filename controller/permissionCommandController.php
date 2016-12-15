<?php

class PermissionCommandController extends CommonCommandController {
    public function updateRolePermissions() {
        $this->denyAccessWithoutOneOfPermissions(['settings_management', 'settings_edit', 'settings_permissions_edit']);
        $form = new roleUpdatePermissionsFormHelper([]);
        $roleId = $this->params['path']['id'];

        if(!empty($this->params['post'])) {
            try {
                $form->loadValues($this->params['post']);
                RoleViewModel::updatePermissionsBy('id', $roleId, $form->getValues()['permission_ids']);

                MessageController::addFlashMessage('success', 'Role [' . $roleId . '] successfully updated');
            }
            catch(Exception $e) {
                ExceptionHandler::renderSoftException($e);
            }
        }

        $this->redirect('settings?w=permissions');
    }

    public function newRole() {
        $this->denyAccessWithoutOneOfPermissions(['settings_management', 'settings_edit', 'settings_permissions_edit']);
        $form = new roleCreateFormHelper([]);

        if(!empty($this->params['post'])) {
            try {
                $form->loadValues($this->params['post']);
                RoleViewModel::add($form->getValues());

                MessageController::addFlashMessage('success', 'Role ['.$form->getValues()['name'].'] successfully created');
            }
            catch(Exception $e) {
                ExceptionHandler::renderSoftException($e);
            }
        }

        $this->redirect('settings?w=permissions');
    }

    public function removeRole() {
        $this->denyAccessWithoutOneOfPermissions(['settings_management', 'settings_edit', 'settings_permissions_edit']);
        $roleId = $this->params['path']['id'];

        try {
            RoleViewModel::removeBy('id', $roleId);
            MessageController::addFlashMessage('success', 'Role [' . $roleId . '] sucessfully removed');
        }
        catch(Exception $e) {
            ExceptionHandler::renderSoftException($e);
        }

        $this->redirect('settings?w=permissions');
    }
}

class roleUpdatePermissionsFormHelper extends FormHelper {
    protected function defineFields() {
        $this->fields = ['permission_ids'];
    }
}

class roleCreateFormHelper extends FormHelper {
    protected function defineFields() {
        $this->fields = ['name', 'permission_ids'];
    }
}