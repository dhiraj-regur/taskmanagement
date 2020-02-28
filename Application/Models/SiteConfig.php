<?php

class Models_SiteConfig extends LMVC_ActiveRecord {

    public $tableName = "site_config";
    private $configs = array();
    public $configKey = '';
    public $configValue = '';
	public static $editableSiteSettings = array(           
          
    		'take_a_lead_allocation_order'=>array(
    			'configKey' => 'take_a_lead_allocation_order',
    			'configValue' => NULL,
    			'configLabel' => 'Lead allocation oreder (Take A Lead)',
    			'inputType' => 'dropdown',
    			'dropdownOptions' => array('desc'=>'Descending (Newest leads allocated first)','asc'=>'Ascending (Oldest leads allocated first)'),
    			'helpText' => ""
    				
    		)

    );
	
    
    public static $editableDeveloperSettings = array(
            'staging_sites_receiver_urls' => array(
                    'configKey' => 'staging_sites_receiver_urls',
                    'configValue' => NULL,
                    'configLabel' => 'Staging sites receiver urls',
                    'inputType' => 'text',
                    'dropdownOptions' => array(),
                    'helpText' => "Enter the staging site urls where you want to submit live leads. You can enter multiple URLs comman (,) separated."
            ),
            
            'staging_sites_last_lead' => array(
                    'configKey' => 'staging_sites_last_lead',
                    'configValue' => NULL,
                    'configLabel' => 'Staging site last lead',
                    'inputType' => 'text',
                    'dropdownOptions' => array(),
                    'helpText' => "System will submit leads to staging sites from this Lead onwards"
            )
    );
    
    public $dbIgnoreFields = array('configs', 'editableSiteSettings', 'editableDeveloperSettings');

    public function init() {
        $result = $this->getAll(array('configKey', 'configValue'), null, null, null, null, null, DB_FETCHMODE_ASSOC);
        
        foreach ($result as $row) {
            $this->configs[$row['configKey']] = $row['configValue'];

            if(array_key_exists($row['configKey'], self::$editableSiteSettings)){
                    self::$editableSiteSettings[$row['configKey']]['configValue'] = $row['configValue'];
            }
            if(array_key_exists($row['configKey'], self::$editableDeveloperSettings)){
                    self::$editableDeveloperSettings[$row['configKey']]['configValue'] = $row['configValue'];
            }
        }

    }

    public function get($_configKey) {
        if (array_key_exists($_configKey, $this->configs)) {
            return $this->configs[$_configKey];
        }
    }

    public function updateValue($_configKey, $_configValue) {
        if (array_key_exists($_configKey, $this->configs)) {


            $fields = array('configValue');

            $this->configValue = $_configValue;

            $where_clause = "configKey='$_configKey'";

            $this->execute($this->tableName, $fields, DB_AUTOQUERY_UPDATE, $where_clause);
        }
    }

}

?>