<?php
class Admin_ErrorController extends LMVC_Controller {

    public function init() {
        $page_tile = "PinLocal.com :: Exception";
        $this->setViewVar('page_title', $page_tile);
    }

    public function errorAction() {

        $e = LMVC_Front::getException();
        $info = $e->dump;
        if (ENV == "development" || ENV == "staging") {
            $this->setViewVar('info', nl2br($info));
        } else {
            $this->setViewVar('info', "An error has occurred and the technical team has been informed about this error");
        }
        Helpers_Mailer::sendMail("alert@pinlocal.com", DEBUG_EMAIL, "Pinlocal Error Occurred", nl2br($info));
    }

}

?>