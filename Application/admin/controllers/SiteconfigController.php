<?php

class Admin_SiteconfigController extends LMVC_Controller{
        public function init()
        {
                $this->setTitle('Site Config');                
        }
        
        public function indexAction(){                
                $editableSiteSettings = Models_SiteConfig::$editableSiteSettings;
                $editableDeveloperSettings = Models_SiteConfig::$editableDeveloperSettings;
                
                if($this->isPost()){
                        
                        $postData = $this->getRequest()->getPost();
                       
                        array_pop($postData); // remove value of submit button from array
                        
                        foreach($postData as $key => $value){
                                if(empty($value)){
                                        if($postData['settingType'] == "developerSettings")
                                                $this->addError("Please enter " . $editableDeveloperSettings[$key]['configLabel']);
                                        
                                        else if($postData['settingType'] == "siteSettings")
                                                $this->addError("Please enter " . $editableSiteSettings[$key]['configLabel']);
                                }
                        }                        
                        
                        if($this->hasErrors()){
                                $this->setViewVar("errors", $this->getErrors());
                        }
                        else{
                                foreach($postData as $key => $value){
                                        $siteSetting = new Models_SiteConfig();
                                        $siteSetting->updateValue($key, $value);
                                        
                                        if($postData['settingType'] == "developerSettings")
                                                $editableDeveloperSettings[$key]['configValue'] = $value;                                                
                                        else if($postData['settingType'] == "siteSettings")
                                                $editableSiteSettings[$key]['configValue'] = $value;
                                }
                                
                                unset($editableDeveloperSettings['settingType']);
                                unset($editableSiteSettings['settingType']);
                                
                                $successMessage = "Site Setting values updated successfully";
                                $this->setViewVar("successMessage", $successMessage);
                        }
                }
                
                $this->setViewVar("siteSettings", $editableSiteSettings);
                $this->setViewVar("developerSettings", $editableDeveloperSettings);
        }
}

?>