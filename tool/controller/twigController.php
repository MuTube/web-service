<?php

require 'vendor/twig/twig/lib/Twig/Autoloader.php';

class TwigController {
    function __construct($dir) {
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem($dir);
        $this->twig = new Twig_Environment($loader, array('cache' => false));

        $this->addTwigFunctions();
    }

    private $twig;

    public function renderTemplateWithPath($templatePath, $data) {
        try {
            $tempate = $this->twig->loadTemplate($templatePath);
            echo $tempate->render($data);
        }
        catch(Exception $e) {
            throw new HardException("Twig Error :", $e->getMessage());
        }
    }

    public function getRenderReadyTemplate($templatePath, $data) {
        try {
            $template = $this->twig->loadTemplate($templatePath);
            return $template->render($data);
        }
        catch(Exception $e) {
            throw new HardException("Twig Error :", $e->getMessage());
        }
    }

    protected function addTwigFunctions() {
        //selector function
        $this->addSelectorFunctions();

        //link function
        $this->addLinkFunctions();

        //permission function
        $this->twig->addFunction(new Twig_SimpleFunction('hasAccess', function($permissions) {
            $permissions = is_array($permissions) ? $permissions : [$permissions];
            $isAllowed = false;

            foreach($permissions as $permission) {
                foreach(UserHelper::getPermissionsForCurrentUserOrUid() as $userPermission) {
                    if($userPermission['name'] == $permission) $isAllowed = true;
                }
            }

            return $isAllowed;
        }));

        //format whois phone number function
        $this->twig->addFunction(new Twig_SimpleFunction('formatWhoIsPhone', function($phoneNumber) {
            $countryCode = substr($phoneNumber, 1, abs(1 - strpos($phoneNumber, '.')));
            $onlyPhoneNumber = substr($phoneNumber, strpos($phoneNumber, '.') + 1);

            return '+' . (strlen($countryCode) == 1 ? '0'.$countryCode : $countryCode) . ' ' . $onlyPhoneNumber;
        }));
    }

    protected function addSelectorFunctions() {
        $functions = [
            'roleSelector' => ['viewModel' => 'role'],
            'permissionSelector' => ['viewModel' => 'permission']
        ];

        foreach($functions as $name => $baseOptions) {
            $this->twig->addFunction(new Twig_SimpleFunction($name, function($options = []) use ($baseOptions) {
                $defaultOptions = [
                    'selected' => [],
                    'emptyValue' => false,
                    'multiple' => false,
                    'htmlAttribs' => [
                        'id' => '',
                        'name' => ''
                    ]
                ];

                $options = array_merge($defaultOptions, $options);

                $viewModelName = ucfirst($baseOptions['viewModel']) . "ViewModel";
                $selectOptions = $viewModelName::getSelectorData();

                $options['selectOptions'] = $selectOptions;
                $options['selected'] = is_array($options['selected']) ? $options['selected'] : [$options['selected']];

                return $this->getRenderReadyTemplate('common/twigSelector.html.twig', $options);
            }));
        }
    }

    protected function addLinkFunctions() {
        $functions = [
            'userLink' => ['viewModel' => 'user']
        ];

        foreach($functions as $name => $baseOptions) {
            $this->twig->addFunction(new Twig_SimpleFunction($name, function($id = -1) use ($baseOptions) {
                if($id == -1) throw new HardException('Twig Function Error :', 'Entity id is not defined');

                $viewModelName = ucfirst($baseOptions['viewModel']) . "ViewModel";
                $entity = $viewModelName::getBy('id', $id);
                $entityType = $baseOptions['viewModel'];

                //define displayed values
                if($entityType == 'user') $entity['displayedName'] = $entity['username'];
                elseif($entityType == 'contact') $entity['displayedName'] = $entity['firstname'] . ' ' . $entity['lastname'];

                return $this->getRenderReadyTemplate('common/twigLink.html.twig', [
                    'entityType' => $entityType,
                    'entity' => ['id' => $entity['id'], 'name' => $entity['displayedName']]
                ]);
            }));
        }
    }
}