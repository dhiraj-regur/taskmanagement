<?php

class Isd_Controller_Plugin_InitModule extends Zend_Controller_Plugin_Abstract
{
    const _moduleInit = '_moduleInit';

    private function getMainBootstrap() {
        $frontController = Zend_Controller_Front::getInstance();
        return $frontController->getParam('bootstrap');
    }

    private function getModuleBootstrap($appBootstrap, $activeModuleName) {
        $moduleList = $appBootstrap->getResource('Modules');
        if(isset($activeModuleName) && isset($moduleList[$activeModuleName])) {
            $activeModule = $moduleList[$activeModuleName];
        } else {
            $activeModule = $appBootstrap;
        }
        return $activeModule;
    }

    private function getModuleResourcesFromBootstrap($bootstrap) {
        $prefixLength = strlen(self::_moduleInit);
        $bootstrapMethodNames = get_class_methods($bootstrap);
        $response = array();
        foreach ($bootstrapMethodNames as $key => $method) {
            if (strlen($method) > $prefixLength && self::_moduleInit == substr($method, 0, $prefixLength)) {
                $resource = strtolower(substr($method, $prefixLength));
                if ($bootstrap->hasResource($resource)) {
                    throw new Zend_Controller_Exception("Application-Level Resource $resource is already defined");
                }
                $response[$resource] = $method;
            }
        }
        return $response;
    }

    private function processExtendedModuleBootstrap($bootstrap) {
        $resources = $this->getModuleResourcesFromBootstrap($bootstrap);
        foreach ($resources as $resourceName => $method) {
            $resource = call_user_func(array($bootstrap, $method));
            if ($resource !== null) {
                $bootstrap->getContainer()->{$resourceName} = $resource;
            }
        }
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        $activeModuleName = $request->getModuleName();

        $appBootstrap = $this->getMainBootstrap();
        $activeModuleBootstrap = $this->getModuleBootstrap($appBootstrap, $activeModuleName);
        
        $this->processExtendedModuleBootstrap($activeModuleBootstrap);
    }
}
