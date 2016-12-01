<?php

class UserCommandController extends CommonCommandController {
    public function defaultLoading() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_list']);

        $userTable = DbController::getTable('user');
        $this->data['users'] = $userTable->getList();

        $this->setTemplate('user/list.html.twig');
    }

    public function create() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_create']);

        $userTable = DbController::getTable('user');
        $form = new UserFormHelper();

        if(!empty($this->params['post'])) {
            try {
                $form->loadValues($this->params['post']);

                if($userTable->getForUsername($form->getValues()['usrname'])) throw new SoftException('Username already exists');

                $id = $userTable->create($form->getValues());
                $userTable->updatePasswordForUid($id, $this->params['post']['pswd']);
                $userTable->updateAPIKeyForUid($id, SessionController::generateAPIKey());

                if($this->params['files']['image']['error'] != 4) {
                    $image = $this->params['files']['image'];
                    $newFileName = $id . '_' . $image['name'];

                    FileManager::processUserImage($image, null, $newFileName);
                    $userTable->updateById($id, ['image_name' => $newFileName]);
                }

                MessageController::addFlashMessage('success', 'User successfully created with id "' . $id .'"');
            }
            catch(Exception $e) {
                ExceptionHandler::renderSoftException($e);
            }

            $this->redirect('user');
        }

        $this->data['formValues'] = $form->getValues();
        $this->setTemplate('user/create.html.twig');
    }

    public function read() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_read', 'user_'.$this->params['path']['id'].'_change_password']);

        $userTable = DbController::getTable('user');
        $this->data['user'] = $userTable->getById($this->params['path']['id']);

        $this->setTemplate('user/read.html.twig');
    }

    public function edit() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_edit', 'user_'.$this->params['path']['id'].'_edit']);

        $userTable = DbController::getTable('user');
        $user = $userTable->getById($this->params['path']['id']);
        $form = new UserFormHelper($user);

        if(!empty($this->params['post'])) {
            try {
                $form->loadValues($this->params['post']);
                $userTable->updateById($user['id'], $form->getValues());

                if($this->params['files']['image']['error'] != 4) {
                    $image = $this->params['files']['image'];
                    $newFileName = $user['id'] . '_' . $image['name'];

                    FileManager::processUserImage($image, $user['image_name'], $newFileName);
                    $userTable->updateById($user['id'], ['image_name' => $newFileName]);
                }

                MessageController::addFlashMessage('success', 'User "' . $form->getValues()['usrname'] . '" successfully updated');
            }
            catch(Exception $e) {
                ExceptionHandler::renderSoftException($e);
            }

            $this->redirect('user/' . $user['id'] . '/edit');
        }

        $this->data['formValues'] = $form->getValues();
        $this->data['userId'] = $user['id'];
        $this->data['userImageName'] = $user['image_name'];

        $this->setTemplate('user/edit.html.twig');
    }

    public function remove() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_remove']);
        $ids = strpos($this->params['path']['id'], '-') ? explode('-', $this->params['path']['id']) : [$this->params['path']['id']];

        try {
            DbController::getTable('user')->removeWithIds($ids);
            MessageController::addFlashMessage('success', "Users ".explode(', ', $ids)." successfully removed");
        }
        catch(Exception $e) {
            ExceptionHandler::renderSoftException($e);
        }

        $this->redirect('user');
    }

    public function changePassword() {
        $userTable = DbController::getTable('user');
        $userId = $this->params['path']['id'];

        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_'.$userId.'_change_password']);

        $this->data['user'] = $userTable->getById($userId);

        if(!empty($this->params['post'])) {
            try {
                $form = new PasswordFormHelper($this->params['post']);

                $userTable->validatePasswordReset($form->getValues());
                $userTable->updatePasswordForUid($userId, $form->getValues()['newPassword']);

                MessageController::addFlashMessage('success', 'password successfully updated');
            }
            catch(Exception $e) {
                ExceptionHandler::renderSoftException($e);
            }

            $this->redirect('user/'.$userId.'/read');
        }

        $this->setTemplate('user/changePassword.html.twig');
    }

    public function resetAPIKey() {
        $userTable = DbController::getTable('user');
        $userId = $this->params['path']['id'];

        $this->denyAccessWithoutOneOfPermissions(['user_'.$userId.'_reset_api_key']);

        $this->data['user'] = $userTable->getById($userId);

        try {
            $userTable->updateAPIKeyForUid($userId, SessionController::generateAPIKey());
            MessageController::addFlashMessage('success', 'Api key sucessfully reset');
        }
        catch(Exception $e) {
            ExceptionHandler::renderSoftException($e);
        }

        $this->redirect('user/profile');
    }

    public function profile() {
        $userTable = DbController::getTable('user');
        $currentUser = $userTable->getById($this->user['uid']);

        $this->data['currentUser'] = $currentUser;
        $this->data['permissions'] = UserHelper::getPermissionsForCurrentUserOrUid();
        $this->data['allPermissions'] = DbController::getTable('permission')->getList();

        $this->setTemplate('user/profile.html.twig');
    }
}

//formHelper
class UserFormHelper extends FormHelper {
    protected function defineFields() {
        $this->fields = ['usrname', 'firstname', 'lastname', 'email', 'role_id'];
    }
}

class PasswordFormHelper extends FormHelper {
    protected function defineFields() {
        $this->fields = ['currentPassword', 'newPassword', 'retypeNewPassword'];
    }
}