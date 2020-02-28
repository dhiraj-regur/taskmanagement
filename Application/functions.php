<?php
/**
 * 
 * Central place for repeatitive, non-class/object functions
 * 
 */

global $pinlocalContext; 

require('inc/db_functions.php');
require('inc/lead_fields_callback_functions.php');
require('inc/leadtype_filters_display_callback_functions.php');
require('inc/datatablegrid/row_processor_callback_functions.php');
require('inc/leadtype_filters_input_format_callback_functions.php');


/**
 * It will returns register and UI enabled payment method for admin module.  
 * @return:array (payment method key label)
 * iss236 
 */
function getRegisteredPaymentMethods()
{
	global $registeredPaymentMethods;	

	foreach ($registeredPaymentMethods as $paymentMethod => $className)
	{
		$pmObj = Service_PaymentMethodsFactory::getInstance($paymentMethod);
		if($pmObj->isUIEnabled() == 'y')
		{
			$paymentMethodkeyLabels[$paymentMethod] = $pmObj->getPaymentMethodLabel();
		}
	}
	
	return $paymentMethodkeyLabels;	
}

/**
 * It will returns register and Signup enabled payment method for front module.
 * @return:array (payment method key label)
 * iss264
 */

function getSignupEnabledPaymentMethods()
{
	global $registeredPaymentMethods;
	
	foreach ($registeredPaymentMethods as $paymentMethod => $className)
	{
		$pmObj = Service_PaymentMethodsFactory::getInstance($paymentMethod);
		if($pmObj->isSignupEnabled()== 'y')
		{
			$paymentMethodkeyLabels[$paymentMethod] = $pmObj->getPaymentMethodLabel();
		}
	}
	
	return $paymentMethodkeyLabels;
}

/**
 * Auto enabled payment method by default enabled when new company will create in pinlocal system. 
 * @return:array (payment method key label)
 * iss236 
 */

function getAutoEnabledPaymentMethods()
{
	global $registeredPaymentMethods;	
	
	$autoEnabledPaymentMethod = array();
	
	foreach ($registeredPaymentMethods as $paymentMethod => $className)
	{
		$pmObj = Service_PaymentMethodsFactory::getInstance($paymentMethod);
		if($pmObj->isAutoEnabledPaymentMethod() == "y" )
		{
			array_push($autoEnabledPaymentMethod,$paymentMethod);
		}
		
	}
	
	return $autoEnabledPaymentMethod;
	
}

/*
 * used for : Charge invoices Cron
 * @return: ',' seperated string
 * 
 */

function getCronEnabledPaymentMethods()
{
	$registeredPaymentMethods = getRegisteredPaymentMethods();
	
	$cronEnabledPaymentMethod = array();
	
	foreach ($registeredPaymentMethods as $paymentMethod => $className)
	{
		$pmObj = Service_PaymentMethodsFactory::getInstance($paymentMethod);
		if($pmObj->isAutoChargeEnabledPaymentMethod() == "y")
		{
			array_push($cronEnabledPaymentMethod,$paymentMethod);
		}
		
	}
	
	$autoChargePaymentMethod = implode(",", $cronEnabledPaymentMethod);
	
	return $autoChargePaymentMethod;
}

function getAccountTypeLabel($_accountType)
{
	global $accountTypes;
	
	if(array_key_exists($_accountType, $accountTypes))
	{
		return $accountTypes[$_accountType];
	}
	else
	{
		return $_accountType;
	}
}

function getNoReassignReasons()
{
    $donotReassignReasons = unserialize(DONOT_REASSIGN_REASONS);
	return $donotReassignReasons;
}

function getNoReassignReasonLabel($key)
{
    $donotReassignReasons = getNoReassignReasons();
	
	if(array_key_exists($key,$donotReassignReasons))
	{
		return $donotReassignReasons[$key];
	}
	
}

/**
 * #iss495
 * Array defined in application config
 * @return array
 */
function getTaxCodes()
{
    $taxCodes = unserialize(TAX_CODES);
    return $taxCodes;
}

/**
 * iss169
 * Generate summary as per schedule settings to display notes
 * @param object $company, $cps (company pause settings)
 * @return string
 */

function getPauseScheduleSummary($company,$cps)
{
	$summary = '';
	
		
	if($company->scheduleType == 'repeat')
	{
		
		if($cps->scheduleType == 'daily')
		{
			$pDay = "Day";
			$unDay = "Day";	
			
		}
		else if($cps->scheduleType == 'weekly')
		{
			$pDay = $cps->pauseDay;
			$unDay = $cps->reactivationDay; 
		}

		$summary .= "Starting from ".Helpers_DateFormat::toMySqlDateTime($cps->scheduleStartDate,null,'d/m/Y')." Pause company every ".$pDay." at ".Helpers_DateFormat::changeTimeFormat($cps->pauseTime)." Re-activate the following ".$unDay." at ".Helpers_DateFormat::changeTimeFormat($cps->reactivationTime);
		
				
	}
	else if($company->scheduleType != 'repeat')
	{		
		
		if($company->unPauseType == 'auto')
		{
			$text = "Re-activating";
		}
		else if($company->unPauseType == 'remind')
		{
			$text = "Remind"; 
		}
		if($company->scheduleType == 'immediate')
		{
			if($company->unPauseType != 'never')
			{
				
				$summary .= "Paused & $text on : ".Helpers_DateFormat::toMySqlDateTime($company->unPauseDate,null,'d/m/Y')." at ".Helpers_DateFormat::changeTimeFormat($company->unPauseTime);
			}
			else
			{
				$summary .= "Paused: No re-activation date";
			}	
			
		}
			
		else if($company->scheduleType == 'future')
		{			
			if($company->unPauseType != 'never')
			{
				$summary .= "Future pause on : ".Helpers_DateFormat::toMySqlDateTime($company->futurePauseDate,null,'d/m/Y')." at ".Helpers_DateFormat::changeTimeFormat($company->futurePauseTime)." & $text on : ".Helpers_DateFormat::toMySqlDateTime($company->unPauseDate,null,'d/m/Y')." at ".Helpers_DateFormat::changeTimeFormat($company->unPauseTime);
			}
			else
			{
				$summary .= "Future pause on : ".Helpers_DateFormat::toMySqlDateTime($company->futurePauseDate,null,'d/m/Y')." at ".Helpers_DateFormat::changeTimeFormat($company->futurePauseTime)." : No re-activation date";
			}				
					
		}
	}

	return $summary;
}


/**
 * 
 * Salesforce Integration functions #128
 * 
 */

 function sfSyncQueueUpdate($object, $operation, $recordId)
 {
 	//ignore sync queue entry if data update through any API
 	if(getPinlocalContext() != "api-operation") 
 	{
	 	$sfSyncQueue = Service_SfSyncQueueManager::getInstance();
	 	$sfSyncQueue->addInQueue($object, $operation, $recordId);
 	}	
 	
 }


function setPinlocalContext($contextValue) {
	
	global $pinlocalContext;
	
	$pinlocalContext = $contextValue;
	 
}


function getPinlocalContext() {
	
	global $pinlocalContext;
	
	return $pinlocalContext;
	
}

function getPartnerUserRoles() // return array of partner user roles
{
    $partnerUserRoles = array(
        'super_admin' => 'Super Admin',
        'branch_admin' => 'Branch Admin',
        'standard_user' => 'Standard User'
    );

    return $partnerUserRoles;
}

function getPartnerUserRoleLabel($slug) // return Label of partner user role
{
    $array = getPartnerUserRoles();
    $label ='';
    if (array_key_exists($slug, $array)) {
        $label = $array[$slug];
    }
    return $label;
}

function getPartnerPaymentModels() // return array of partner payment model
{
	$partnerPaymentModels = array(
			'1' => 'Pay Per Lead',
			'2' => 'Pay Per Instruction'
	);
	
	return $partnerPaymentModels;
}

function getPartnerPaymentModelLabel($slug) // return Label of partner payment model
{	
	$label = '';
	$ppModel = getPartnerPaymentModel();
	if (array_key_exists($slug, $ppModel)) {
		$label = $ppModel[$slug];
	}
	return $label;
}

function getUserRoles() // return array of user roles
{
    return array(
        "super_admin" => "Super Admin",
      //  'crm_agent' => 'CRM Agent',
        'qlt_agent' => 'Outsourced Agent',
        'credit_agent' => "Credit Agent",
        'consumer_crm_agent' => 'Consumer CRM Agent',
        'consumer_crm_manager' => 'Consumer CRM Manager',
    	'consumer_crm_tl' => 'Consumer CRM Team Leader',
        "partner_manager" => "Partner Manager"
    );
}

function getUserRoleValue($slug) // return value of user role
{	
	$value = '';
    $array = getUserRoles();

    if (array_key_exists($slug, $array)) {
        $value = $array[$slug];
    }
    return $value;
}
/*
 * This function (getDateRangePicker) is used to display the date range.
 * 
 * The getDateRange function will return the toDate and fromDate based on the dateRange passed.
 */
function getDateRangePicker(){
	return array('last60days'=>'Last 60 Days','today'=>'Today','thisweek'=>'This Week (Mon to Sun)','lastweek'=>'Last Week (Mon to Sun)','thismonth'=>'This Month','lastmonth'=>'Last Month','last6months'=>'Last 6 Months','thisyear'=>'This Year','custom'=>'Custom');
}

function getDateRange($dateRange){

	$toDate = "";	
	$fromDate = date('Y-m-d', time());
	
	
	if ($dateRange == "today") {
		
		$toDate = $fromDate;
		
	} elseif ($dateRange == "yesterday") {
	    
	    $toDate = date('Y-m-d', strtotime("-1 days"));
	    $fromDate = date('Y-m-d', strtotime("-1 days"));
	    
	} elseif ($dateRange == "thisweek") {
		
		$weekDay = date('N', time()) - 1;
	
		$fromTimeStamp = strtotime("-$weekDay days", time());
		$toTimeStamp = strtotime("+6 days", $fromTimeStamp);
		$fromDate = date('Y-m-d', $fromTimeStamp);
		$toDate = date('Y-m-d', $toTimeStamp);
		
	} elseif ($dateRange == "lastweek") {
		
		$weekDay = date('N', time()) - 1;
		$weekDay += 7;
		$fromTimeStamp = strtotime("-$weekDay days", time());
		$toTimeStamp = strtotime("+6 days", $fromTimeStamp);
		$fromDate = date('Y-m-d', $fromTimeStamp);
		$toDate = date('Y-m-d', $toTimeStamp);
		
	} elseif ($dateRange == "thismonth") {
		
		$toDate = $fromDate;
		$fromTimeStamp = strtotime("0 month", time());
		$fromDate = date('Y-m-' . 1, $fromTimeStamp);
		
		
	} elseif ($dateRange == "lastmonth") {
		
		#$fromTimeStamp = strtotime("-1 month", time());
		$fromTimeStamp = mktime(0, 0, 0, date("m")-1,1,date("Y")); //mktime() used instead of strtotime() to get last month starting date because strtotime() returns wrong date when current month date is 31
	//	$toTimeStamp = mktime(0, 0, 0, date('m', time()), 1, date('Y', time())) - (24 * 3600);
		$fromDate = date('Y-m-' . 1, $fromTimeStamp);
	//	$toDate = date('Y-m-d', $toTimeStamp);
		$toDate = date('Y-m-d', strtotime('last day of last month'));   #iss420
		
	}
	elseif ($dateRange== "last7days")	{
		
		$toDate = date('Y-m-d');
		$fromDate = date('Y-m-d', strtotime('-6 days'));
		
	}
	elseif ($dateRange == "last30days")	{
		
		$toDate = $fromDate;
		$fromDate = date('Y-m-d', strtotime('-29 days'));
	}
	elseif ($dateRange == "last60days") {
		
		$toDate = $fromDate;
		$fromDate = date('Y-m-d', strtotime('-59 days'));		
		
	}
	elseif ($dateRange == "last3months") {
		
		$fromTimeStamp = strtotime("-3 month", time());
		$toTimeStamp = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
		$fromDate = date('Y-m-' . 1, $fromTimeStamp);
		$toDate = date('Y-m-d', $toTimeStamp);
		
	
	}elseif ($dateRange == "last6months") {
	    
		$fromTimeStamp = strtotime("-6 month", time());
		$toTimeStamp = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
		$fromDate = date('Y-m-' . 1, $fromTimeStamp);
		$toDate = date('Y-m-d', $toTimeStamp); 
		
		
	}elseif ($dateRange == "last12months") {
		
		$fromTimeStamp = strtotime("-12 month", time());
		$toTimeStamp = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
		$fromDate = date('Y-m-' . 1, $fromTimeStamp);
		$toDate = date('Y-m-d', $toTimeStamp);
		
	}
    elseif ($dateRange == "thisyear") {
		
		$year = date('Y', time());
		$fromDate = $year . "-01-01";
		$toDate = $year . "-12-31";
		
	}
		 
	return array($fromDate, $toDate);
	
}

function getDateRangeFromParticularDay($period,$toDateTime ="")
{	
	$fromDateTime = '';
	if(empty($toDateTime))
	{
		$toDateTime = date("Y-m-d H:i:s");
	}	
	if(!empty($period))
	{	
		$fromDateTime = date("Y-m-d H:i:s", strtotime($period,strtotime($toDateTime)));
	}
	return array($fromDateTime, $toDateTime);
	
}

function isClientDemoSystem()
{
	$return = false;
	if($_SERVER['HTTP_HOST'] == "clientdemo.pinlocal.com")
	{
		$return = true;
	}
	
	return $return;
	
}

function getSiteURL()
{
		
	$siteURL = SITE_URL;
	
	if(isset($_SERVER['HTTPS']))
	{
		$siteURL = str_replace('http:', 'https:', $siteURL);
	}
	
	return $siteURL; 
	
}

/*
 * #271
 * Function to check if the number is a valid mobile number on the basis of the initial digits.
 * Used before sending SMS to avoid sending SMS to the landline numbers.
 */
function isValidMobileNumber($number)
{
	$isMobile = false;
	$validInitials = unserialize(VALID_MOBILE_INITIALS);
	foreach( $validInitials as $value){
		$length = strlen($value);
		if (substr($number, 0, $length) == $value){
			$isMobile = true;
		}
	}

	return $isMobile;
}
function getAllCompanyStatus()
{
	global $companyCalculatedStatus;
	global $companyDeactivatedStatus;
	$allStatus = array("all"=>"ALL");
	$calculatedStatus = $companyCalculatedStatus;
	$deactivatedStatus = $companyDeactivatedStatus;
	
	return array_merge($allStatus,$calculatedStatus,$deactivatedStatus);
	
}

function getCompanyDeactivatedStatus()
{
	global $companyDeactivatedStatus;
	return $companyDeactivatedStatus;
}

function getCompanyStatusLabel($slug)
{	
	$companyStatusLabel = '';
	$status = getAllCompanyStatus();
			
	if(array_key_exists($slug,$status))
	{		
		$companyStatusLabel = $status[$slug];
	}
	
	return $companyStatusLabel;
}

function pl_number_format( $value ) 
{
	return number_format( (float) $value,"2",".",",");
}

function getConveyancingFeeKey($label)
{
	return md5(preg_replace('/[^A-Za-z0-9\-_]/', '', $label));
}

function getCompanyStatusLabelCssClassName($slug)
{
	$cssClassName = '';
	$status = array("all"=>"","notactive"=>"label-danger","confirmed"=>"label-primary","intrial"=>"label-info","paying"=>"label-success","paused"=>"label-warning","deactivated"=>"label-danger","junk"=>"label-danger","debtor"=>"label-danger","stopped_trading"=>"label-danger");
	
	if(array_key_exists($slug,$status))
	{
		$cssClassName= $status[$slug];
	}
	
	return $cssClassName;

}

function getSalesforceCompanyUrl($sfid){

	$sfurl =  '';

	if(trim($sfid) != ''){

		$sfurl = "https://eu9.lightning.force.com/one/one.app#/sObject/".$sfid."/view";
	}

	return $sfurl;
}

function getCurrentUIContextCompanyURL($companyId, $sfid){

	if(LMVC_Session::get("ui_context") == 'salesforce'){
		$salesForceUrl = getSalesforceCompanyUrl($sfid);
		
		if($salesForceUrl == ''){
			
			$plCompanyUrl = "/admin/companies/edit/id/".$companyId."/";
			return $plCompanyUrl;
		}
		else{
			return $salesForceUrl;
		}
	}
	else{
		$plCompanyUrl = "/admin/companies/edit/id/".$companyId."/";
		return $plCompanyUrl;
		
	}
}


function getMainLeadStatus()
{
	$leadStatus = array('new'=>"New",'assigned'=>"Assigned",'unassigned'=>"Unassigned",'invalid'=>"Invalid",'duplicate'=>"Duplicate",'hold'=>"Hold",'spam'=>"Spam");
	return $leadStatus;
}

/*
 * Funciton to check quote type is sale or not
 */
function isCNSQuote($quote){

	if(strtolower($quote->quoteType) == "cns"){
		return true;
	}
	return false;
}

/*
 * Funciton to check quote type is purchase or not
 */
function isCNPQuote($quote){
	
	if(strtolower($quote->quoteType) == "cnp"){
		return true;
	}
	return false;
}

/*
 * Funciton to check quote type is remortgage or not
 */
function isCNRQuote($quote){

	if(strtolower($quote->quoteType) == "cnr"){
		return true;
	}
	return false;
}

/*
 * Funciton to check quote type is sale & purchase or not
 */
function isCNSPQuote($quote){

	if(strtolower($quote->quoteType) == "cnsp"){
		return true;
	}
	return false;
}

/*
 * Function to get conveyancing Companies
 */
function getConveyancingCompanies(){
    
    $conveyancingCompanies = array();
    
    $conveyancingLeadTypeCatIds = unserialize(CONVEYANCING_LEAD_TYPE_CATEGORY_IDS);
    foreach($conveyancingLeadTypeCatIds as $id){
        
        $leadTypeCat = new Models_LeadTypeCategory($id);
        $companies = $leadTypeCat->getCompanies();
        
        foreach($companies as $company){
            $conveyancingCompanies[$company["id"]] = trim($company["companyName"]);
        }
    }
    
    natcasesort($conveyancingCompanies);
        
    return $conveyancingCompanies;
}

function getConveyancingLeadFullAddress($addressParts){
    
    $fullAddress = '';
    $parts = unserialize($addressParts);
    
    foreach($parts as $key => $part){
        if($part != ''){
            $fullAddress .= $part.", ";
        }
    }
    
    return trim($fullAddress,", ");
}

if(!function_exists('array_column')){
    
    function array_column(array $input, $columnKey, $indexKey = null) {
        
        $array = array();
        foreach($input as $value){
            if(!array_key_exists($columnKey, $value)){
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if (!array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if (!is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}
 
function getInstructionNotificationBCCAdminEmails($source)
{
    global $siteConfig;
    
    if($source == 'AL')
    {          
        $emailIds = $siteConfig->get('admin_instruction_notification_bcc_emails_al');
        return explode(',', $emailIds);
    }
    elseif($source == 'CI')
    {
        $emailIds = $siteConfig->get('admin_instruction_notification_bcc_emails_ci');
        return explode(',', $emailIds);
    }
    else
    {
        return array();
    }
}

function getRedisInstance(){
    $redisClient = LMVC_RedisClient::getInstance()->getRedisInstance();
    return $redisClient;
}

function redis_store($key,$value){
    
    $redis = getRedisInstance();
    $redis->set($key,$value);
}

function redis_fetch($key){
    
    $redis = getRedisInstance();
    $value = $redis->get($key);
    
    return $value;
}

function redis_exists($key){
    
    $redis = getRedisInstance();
    
    if($redis->exists($key)){
        return true;
    }
    else{
        return false;
    }
}

function redis_delete($key){
    
    $redis = getRedisInstance();
    
    if($redis->exists($key)){
        
        $redis->del($key);
    }

}

function getRotatingPremiumCompanyIds()
{
    $rotatingPremiumCompanyIds = '';
    $result = array();
    $redis = getRedisInstance();

    // get company ids from redis, if ids not found in redis then fetch from the db and set in to the redis.

    if (! empty($redis)) {

        $ids = $redis->GET(REDIS_ROTATING_PREMIUM_COMPANY_IDS_KEY);

        if (! empty($ids)) {

            $rotatingPremiumCompanyIds = trim($ids);
        } else {

            global $siteConfig;

            $ids = unserialize($siteConfig->get('rotating_premium_company_ids'));

            if (! empty($ids)) {

                $companiesIds = array_keys($ids);
                $rotatingPremiumCompanyIds = implode(',', $companiesIds);
                $redis->SET(REDIS_ROTATING_PREMIUM_COMPANY_IDS_KEY, $rotatingPremiumCompanyIds);
            }
        }
    }

    $companyIds = explode(',', $rotatingPremiumCompanyIds);

    if (! empty($companyIds)) {

        rsort($companyIds);

        $result = $companyIds;
    }

    return $result;
}

function getSysNotificaitonType($type){
    
    $sysNotificationTypes = array(
                                    PLSysNotifications::DEBUG       => "Debug",
                                    PLSysNotifications::INFO        => "Info",
                                    PLSysNotifications::NOTICE      => "Notice",
                                    PLSysNotifications::WARNING     => "Warning",
                                    PLSysNotifications::ERROR       => "Error",
                                    PLSysNotifications::CRITICAL    => "Critical",
                                    PLSysNotifications::ALERT       => "Alert",
                                    PLSysNotifications::EMERGENCY   => "Emergency"
                            );
    
    return $sysNotificationTypes[$type];
}

// This function will return static fees label from the
// static classes(/Service/ConveyancingQuote/StaticFees)

function getCNVStaticFeeLabel($feeName)
{
    $label = "";
    
    $className = "Service_ConveyancingQuote_StaticFees_".$feeName;
    
    if( class_exists($className) )
    {
        $label = $className::$label;
    }
    
    return $label;
}

function getAgentAssignableLeadStatuses()
{    
    $agentAssignableLeadStatuses = unserialize(AGENT_ASSIGNABLE_LEAD_STATUSES);    
    return $agentAssignableLeadStatuses;    
}

function getUserRulesLtcIds()
{    
    $userRulesLtcIds = unserialize(USER_RULES_LTC_IDS);    
    return $userRulesLtcIds;    
}

function isTakeALeadPanelEnabledDurationIsExceed()
{
	$panelEnabledDurationIsExceed = true;	
	
	$weekDay = date('w'); //(0 = sunday)
	
	$panelEnableTime = TAKE_A_LEAD_PANEL_ENABLE_TIME; 
	$panelDisableTime = TAKE_A_LEAD_PANEL_DISABLE_TIME;
	
	if($weekDay == 6 || $weekDay == 0) // Saturday and Sunday
	{
		$panelEnableTime =  TAKE_A_LEAD_PANEL_ENABLE_TIME_IN_WEEKEND;
		$panelDisableTime =  TAKE_A_LEAD_PANEL_DISABLE_TIME_IN_WEEKEND;
	}		
		
	$currentDate = date('Y-m-d');
	$currentTime = date('H:i:00');
	
	$dateFormat = new Helpers_DateFormat();
	
	$panelEnableTimeStamp = $dateFormat::getTimestamp($currentDate, $panelEnableTime,'Y-m-d');
	$panelDisableTimeStamp = $dateFormat::getTimestamp($currentDate, $panelDisableTime,'Y-m-d');
	$currentTimeStamp = $dateFormat::getTimestamp($currentDate, $currentTime,'Y-m-d');
	
		
	if($panelEnableTimeStamp <= $currentTimeStamp && $panelDisableTimeStamp >= $currentTimeStamp)
	{
		$panelEnabledDurationIsExceed = false;
	}
	
	
	return $panelEnabledDurationIsExceed;
	
}

function formatMovingDistanceFilterValue($filterValue){
    
    $formatedValue = "";
    if(!empty($filterValue)){
        
        $fVals = json_decode($filterValue,true);
        if(!empty($fVals)){
            
            if($fVals["movingDistanceFilterType"] == "greaterThan"){
                $formatedValue = "Only assign leads with move distance greater than: {$fVals["movingDistanceFilter"]} miles";
            }
            elseif($fVals["movingDistanceFilterType"] == "lessThan"){
                $formatedValue = "Only assign leads with move distance less than: {$fVals["movingDistanceFilter"]} miles";
            }
        }
    }
    
    return $formatedValue;
}

function isLeadAssignmentLogExist($leadId){
    
    $logExists = false;
    $logFilePath = APPLICATION_PATH .'/extras/logs/lead_assignments/';
    $logFile = 'lead_'.$leadId.'.txt';
    
    if(file_exists($logFilePath.$logFile)){
        $logExists = true;
    }
    
    return $logExists;
}


function getLeadRankBasedOnTimetoInstruct($timeToInstruct)
{
	$rank = 10000; // default value
	
	$timeToInstruct = trim(strtolower($timeToInstruct));
	
	/**
	 10000 default value.
	 
	 In below array if add the new value rank must be less than 10000
	 
	 */
	$leadRank = array('asap'=>1,
			'within 1 week'=>1,
			'within 1 month'=>10,
			'within 3 months'=>20,
			'unsure'=>30,
			'not sure'=>30,
			'unknown'=>30
			
	);
	
	
	if(array_key_exists($timeToInstruct, $leadRank))
	{
		$rank = $leadRank[$timeToInstruct];
	}	
	
	return $rank;
	
}

/**
 * get real IP address of user, if user use proxy
 * @return string
 */
function getUserIPAddress(){
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		//ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		//ip pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

/**
 * checks if a domain name is valid
 * @param  string $domain_name
 * @return bool
 */
function isValidDomainName($domain_name)
{
    //FILTER_VALIDATE_URL checks length but..why not? so we dont move forward with more expensive operations
    $domain_len = strlen($domain_name);
    if ($domain_len < 3 OR $domain_len > 253)
        return FALSE;
    //check HTTP/S just in case was passed.
    if(stripos($domain_name, 'http://') === 0)
        return FALSE;
    elseif(stripos($domain_name, 'https://') === 0)
        return FALSE;

    //www is also not allowed
    if(stripos($domain_name, 'www.') === 0)
        return FALSE;

    if(stripos($domain_name, '@') !== false)
        return false;

    //Checking for a '.' at least, not in the beginning nor end, since http://.abcd. is reported valid
    if(strpos($domain_name, '.') === FALSE OR $domain_name[strlen($domain_name)-1]=='.' OR $domain_name[0]=='.')
        return FALSE;

    //now we use the FILTER_VALIDATE_URL, concatenating http so we can use it, and return BOOL
    return (filter_var ('http://' . $domain_name, FILTER_VALIDATE_URL)===FALSE)? FALSE:TRUE;
}

/**
 * check if email address is valid
 * @param string $email
 * @return boolean
 */
function isValidEmail($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Does string contain special characters?
 * @param string $string
 * @return number
 */
function hasSpecialCharacters( $string ) {
    return preg_match('/[^a-zA-Z\d]/', $string);
}

