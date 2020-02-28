<?php

final class Loader {

    public function __construct() {
        spl_autoload_register(array($this, 'loadClass'), true);
        spl_autoload_register(array($this, 'loadClassLowerCased'), true);
    }

    private function loadClass($_className) {
        if (!class_exists($_className)) {

            $classFileName = str_replace("_", "/", $_className) . ".php";
            @include($classFileName);
        }
    }

    private function loadClassLowerCased($_className) {
        if (!class_exists($_className)) {
            $p = explode("_", $_className);
            if (count($p) > 0) {
                $className = array_pop($p);
                $classFileName = "";
                foreach ($p as $val) {
                    $classFileName .= strtolower($val) . "/";
                }
                $classFileName .= $className . ".php";
                @include($classFileName);
            }
        }
    }

}

?>