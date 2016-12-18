<?php

class UserCommandController extends CommonCommandController {
    public function defaultLoading() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_list']);

        $this->data['users'] = UserViewModel::getList();
        $this->setTemplate('user/list.html.twig');
    }

    public function create() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_create']);
        $form = new UserFormHelper();

        if(!empty($this->params['post'])) {
            try {
                if($this->params['files']['image']['error'] != 4) {
                    $image = $this->params['files']['image'];
                }

                $passwordData = [
                    'password' => $this->params['post']['password'],
                    'password_confirmation' => $this->params['post']['password_confirmation'],
                ];

                $form->loadValues($this->params['post']);
                $id = UserViewModel::add($form->getValues(), $passwordData, isset($image) ? $image : false);

                MessageController::addFlashMessage('success', 'User successfully created with id "' . $id .'"');
            }
            catch(Exception $e) {
                ExceptionHandler::renderFlashException($e);
            }

            $this->redirect('user');
        }

        $this->data['formValues'] = $form->getValues();
        $this->setTemplate('user/create.html.twig');
    }

    public function read() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_read', 'user_'.$this->params['path']['id'].'_change_password']);

        $this->data['user'] = UserViewModel::getBy('id', $this->params['path']['id']);
        $this->setTemplate('user/read.html.twig');
    }

    public function edit() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_edit', 'user_'.$this->params['path']['id'].'_edit']);

        $user = UserViewModel::getBy('id', $this->params['path']['id']);
        $form = new UserFormHelper($user);
        $form->removeField('username');

        if(!empty($this->params['post'])) {
            try {
                if($this->params['files']['image']['error'] != 4) {
                    $image = $this->params['files']['image'];
                }

                $form->loadValues($this->params['post']);
                UserViewModel::updateBy('id', $user['id'], $form->getValues(), isset($image) ? $image : false);

                MessageController::addFlashMessage('success', 'User "' . $form->getValues()['username'] . '" successfully updated');
            }
            catch(Exception $e) {
                ExceptionHandler::renderFlashException($e);
            }

            $this->redirect('user/' . $user['id'] . '/edit');
        }

        $this->data['formValues'] = $form->getValues();
        $this->data['user'] = $user;

        $this->setTemplate('user/edit.html.twig');
    }

    public function remove() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_remove']);
        $ids = strpos($this->params['path']['id'], '-') ? explode('-', $this->params['path']['id']) : [$this->params['path']['id']];

        try {
            foreach($ids as $id) {
                UserViewModel::removeBy('id', $id);
            }

            MessageController::addFlashMessage('success', "Users ".explode(', ', $ids)." successfully removed");
        }
        catch(Exception $e) {
            ExceptionHandler::renderFlashException($e);
        }

        $this->redirect('user');
    }

    public function changePassword() {
        $this->denyAccessWithoutOneOfPermissions(['user_management', 'user_' . $this->params['path']['id'] . '_change_password']);

        $userId = $this->params['path']['id'];
        $this->data['user'] = UserViewModel::getBy('id', $userId);

        if(!empty($this->params['post'])) {
            try {
                $form = new PasswordFormHelper($this->params['post']);
                UserViewModel::updatePasswordBy('id', $userId, $form->getValues());

                MessageController::addFlashMessage('success', 'password successfully updated');
            }
            catch(Exception $e) {
                ExceptionHandler::renderFlashException($e);
            }

            $this->redirect('user/'.$userId.'/changePassword');
        }

        $this->setTemplate('user/changePassword.html.twig');
    }

    public function resetAPIKey() {
        $this->denyAccessWithoutOneOfPermissions(['user_' . $this->params['path']['id'] . '_reset_api_key']);
        $userId = $this->params['path']['id'];

        try {
            UserViewModel::resetAPIKeyBy('id', $userId);
            MessageController::addFlashMessage('success', 'Api key successfully reset');
        }
        catch(Exception $e) {
            ExceptionHandler::renderFlashException($e);
        }

        $this->redirect('user/profile');
    }

    public function profile() {
        $currentUser = UserViewModel::getBy('id', $this->user['uid']);

        $this->data['currentUser'] = $currentUser;
        $this->data['permissions'] = UserHelper::getPermissionsForCurrentUserOrUid();
        $this->data['allPermissions'] = PermissionViewModel::getList();

        $this->setTemplate('user/profile.html.twig');
    }
}

//formHelper
class UserFormHelper extends FormHelper {
    protected function defineFields() {
        $this->fields = ['username', 'firstname', 'lastname', 'email', 'role_id'];
    }
}

class PasswordFormHelper extends FormHelper {
    protected function defineFields() {
        $this->fields = ['password', 'password_confirmation'];
    }
}