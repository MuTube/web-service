<?php

class PermissionCommandController extends CommonCommandController {
    public function updateRolePermissions() {
        $this->denyAccessWithoutOneOfPermissions(['settings_management', 'settings_edit', 'settings_permissions_edit']);
        $form = new roleUpdatePermissionsFormHelper([]);

        if(!empty($this->params['post'])) {
            try {
                $form->loadValues($this->params['post']);
                $roleName = DbController::getTable('userRole')->getById($this->params['path']['id'])['name'];

                DbController::getTable('userRole')->updatePermissions($this->params['path']['id'], $form->getValues()['permission_ids']);

                MessageController::addFlashMessage('success', 'Role ['.$roleName.'] successfully updated');
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
                $roleTable = DbController::getTable('userRole');
                $roleId = DbController::getTable('userRole')->create(['name' => $form->getValues()['name']]);
                $roleTable->updatePermissions($roleId, $form->getValues()['permission_ids']);

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
            $roleName = DbController::getTable('userRole')->getById($roleId)['name'];
            DbController::getTable('userRole')->removeWithId($roleId);
            DbController::getTable('user')->removeDeletedRoleFromUsers($roleId);

            MessageController::addFlashMessage('success', 'Role ['.$roleName.'] sucessfully removed');
        }
        catch(Exception $e) {
            ExceptionHandler::renderSoftException($e);
        }

        $this->redirect('settings?w=permissions');
    }

    // ? HANDLE ADMIN PAGE -> ADD permissions
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