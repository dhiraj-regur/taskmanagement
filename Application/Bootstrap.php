<?php

require('functions.php');

# set time zone to UK
date_default_timezone_set('Europe/London');

# init front controller
$front = LMVC_Front::getInstance();

#start session
LMVC_Session::init();


$dbLogger = new LMVC_Logger();
$dbLogger->setLogFilePath(APPLICATION_PATH .'/extras/logs/dblog.txt',true);

$leadAPILogger = new LMVC_Logger();

$postcodeBreakdownLogger = new LMVC_Logger();
$postcodeBreakdownLogger->setLogFilePath(APPLICATION_PATH .'/extras/logs/postcode-breakdown-log.txt',true);

#connect db

$db = LMVC_DB::getInstance()->connect(DB_HOST, DB_UNAME, DB_PWD, DB_NAME);

#configs
$siteConfig = new Models_SiteConfig();

#global sql data cache
$dataCache = Models_SQLCache::getInstance();

# set msyql time zone to UK (to sync with php)
//$db->query('SET @@session.time_zone ="+00:00";');

//debug db+php time sync.
//echo "DB: ".  $db->getOne("SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()))") ."<br/>";
//echo "PHP: ". date('Y-m-d H:i:s');
#set layouts if any
$front->setLayout('company', '/company/layouts/');

#set directories
$front->setApplicationDirectory(APPLICATION_PATH);
$front->setControllerDirectory(array('admin' => '/admin/controllers/',
    'company' => '/company/controllers/',
    'taskmanagement' => 'taskmanagement/controllers',
    'scripts' => '/scripts/controllers/',
	'crm' => '/crm/controllers/',
	'api' => '/api/controllers/',
	'apiv1' => '/api/controllers/v1/',
	'partnerv1' => '/api/controllers/partner/v1/'));


#regiser plugins
/*$companySessionValidator = new Plugins_CompanySessionValidator();
$front->registerPlugin($companySessionValidator);

$adminSessionValidator = new Plugins_AdminSessionValidator();
$front->registerPlugin($adminSessionValidator);

$CrmSessionValidator = new Plugins_CrmSessionValidator();
$front->registerPlugin($CrmSessionValidator);


$taskmanagementSessionValidator = new Plugins_TaskmanagementSessionValidator();
$front->registerPlugin($taskmanagementSessionValidator);
*/
$DefaultSessionValidator = new Plugins_DefaultSessionValidator();
$front->registerPlugin($DefaultSessionValidator);

$router = LMVC_Router::getInstance();

//quotes listing page(thanks page)
$router->addRoute('view_quotes', new LMVC_Route('/viewquotes/:hash/',
		array('module' => 'Default',
				'controller' => 'quotes',
				'action' => 'viewquotes')));


$router->addRoute('view_quotes_new', new LMVC_Route('/viewquotes/:hash/:viewparam/',
		array('module' => 'Default',
				'controller' => 'quotes',
				'action' => 'viewquotes')));

//quote detail page
$router->addRoute('view_detail_quote', new LMVC_Route('/viewdetailquote/:params/',
		array('module' => 'Default',
				'controller' => 'quotes',
				'action' => 'viewdetailquote')));

$router->addRoute('view_detail_quote_new', new LMVC_Route('/viewdetailquote/:params/:viewparam/',
		array('module' => 'Default',
				'controller' => 'quotes',
				'action' => 'viewdetailquote')));

// Partner Portal PDF Export
$router->addRoute('pp_export_pdf', new LMVC_Route('/ppexportpdf/:quotehash/',
		array('module' => 'Default',
			'controller' => 'quotes',
			'action' => 'ppexportpdf')));
		
// Partner Portal Bill PDF Export
$router->addRoute('pp_export_bill_pdf', new LMVC_Route('/ppexportbillpdf/:billhash/',
        array('module' => 'Default',
            'controller' => 'quotes',
            'action' => 'ppexportbillpdf')));
        
// Partner Portal Instruction PDF Export
$router->addRoute('pp_export_instruction_pdf', new LMVC_Route('/ppexportinstructionpdf/:instructionhash/',
        array('module' => 'Default',
            'controller' => 'quotes',
            'action' => 'ppexportinstructionpdf')));


//company register page
$router->addRoute('company_signup_categories', new LMVC_Route('/register/type/:leadcategory/',
			array('module' => 'Default',
				'controller' => 'register',
				'action' => 'index')));

$router->addRoute('company_leadtype_categories', new LMVC_Route('/company/leadtypes/type/:leadcategory/',
		array('module' => 'company',
				'controller' => 'leadtypes',
				'action' => 'index')));

//company register success page
$router->addRoute('company_signup_success', new LMVC_Route('/register/success/:key/',
		array('module' => 'Default',
				'controller' => 'register',
				'action' => 'success')));



//for data submission through url
$router->addRoute('api_submitdata', new LMVC_Route('/api/submitdata/:slug',
                array('module' => 'api',
                    'controller' => 'submitdata',
                    'action' => 'index')));

//api to create a lead
$router->addRoute('api_lead_create', new LMVC_Route('/api/lead/create/:id/:key',
                array('module' => 'api',
                    'controller' => 'lead',
                    'action' => 'index')));

//api to get lead by lead id

$router->addRoute('api_get_leadbyid', new LMVC_Route('/api/lead/:id/:key',
                array('module' => 'api',
                    'controller' => 'lead',
                    'action' => 'index')));

//api to get lead by lead hash (or by new method in future)
$router->addRoute('api_get_leadbyproperty', new LMVC_Route('/api/lead/:property/:value/:key',
                array('module' => 'api',
                    'controller' => 'sites',
                    'action' => 'index')));

//api to assign a lead
$router->addRoute('api_assign_lead', new LMVC_Route('/api/leadassignment/:hash/:key',
		array('module' => 'api',
				'controller' => 'leadassignment',
				'action' => 'index')));


//api to get all lenders
$router->addRoute('api_get_lenders', new LMVC_Route('/api/lender/:key',
		array('module' => 'api',
				'controller' => 'lender',
				'action' => 'index')));

//api to get lender by id
$router->addRoute('api_get_lenderbyid', new LMVC_Route('/api/lender/:id/:key',
		array('module' => 'api',
				'controller' => 'lender',
				'action' => 'index')));

//api to get postcode by search keyword
$router->addRoute('api_get_postcode', new LMVC_Route('/api/postcodelookup/:key',
		array('module' => 'api',
			  'controller' => 'postcodelookup',
			  'action' => 'index')));



// Being Partner Portal APIs		
		

// Partner Login API
$router->addRoute('api_partner_login', new LMVC_Route('/api/partner/v1/login/:key',
		array('module' => 'partnerv1',
				'controller' => 'login',
				'action' => 'index')));


// Change Password API
$router->addRoute('api_partner_change_password', new LMVC_Route('/api/partner/v1/changepassword/:key',
		array('module' => 'partnerv1',
				'controller' => 'changepassword',
				'action' => 'index')));


// Calculate Conveyancing Quote
$router->addRoute('api_calculate_conveyancing_quote', new LMVC_Route('/api/partner/v1/calculatequote/:key',
		array('module' => 'partnerv1',
				'controller' => 'Conveyancingquotes',
				'action' => 'index')));


// Lead Create, Lead Assignment and Quote Create
$router->addRoute('api_create_conveyancing_lead_and_quote', new LMVC_Route('/api/partner/v1/conveyancinglead/:key',
		array('module' => 'partnerv1',
				'controller' => 'Conveyancingleads',
				'action' => 'index')));


// Find Partner User API
$router->addRoute('api_find_partner_user', new LMVC_Route('/api/partner/v1/finduser/:key',
		array('module' => 'partnerv1',
				'controller' => 'finduser',
				'action' => 'index')));


// Get Conveyancing Instructions
$router->addRoute('api_get_conveyancing_instructions', new LMVC_Route('/api/partner/v1/getinstructions/:partnerUserId/:hash/:key',
		array('module' => 'partnerv1',
				'controller' => 'Conveyancinginstructions',
				'action' => 'index')));


// Get Partner Quotes
$router->addRoute('api_get_partner_quotes', new LMVC_Route('/api/partner/v1/getpartnerquotes/:partnerUserId/:hash/:key',
		array('module' => 'partnerv1',
				'controller' => 'Conveyancingquotes',
				'action' => 'index')));


// Get Standard Users Dashboard data
$router->addRoute('api_get_standard_users_dashboard', new LMVC_Route('/api/partner/v1/dashboarddata/standarduser/:partnerUserId/:hash/:key',
		array('module' => 'partnerv1',
				'controller' => 'Standardusersdashboard',
				'action' => 'index')));

// Get Admin Users Dashboard data
$router->addRoute('api_get_admin_users_dashboard', new LMVC_Route('/api/partner/v1/dashboarddata/adminduser/:partnerUserId/:hash/:key',
		array('module' => 'partnerv1',
				'controller' => 'Adminusersdashboard',
				'action' => 'index')));

		
// to save reset password token API
$router->addRoute('api_partner_save_reset_password_token', new LMVC_Route('/api/partner/v1/saveresetpasswordtoken/:key',
        array('module' => 'partnerv1',
            'controller' => 'Saveresetpasswordtoken',
            'action' => 'index')));
		    
// reset password  API
$router->addRoute('api_partner_reset_password', new LMVC_Route('/api/partner/v1/resetpassword/:key',
        array('module' => 'partnerv1',
            'controller' => 'Resetpassword',
            'action' => 'index')));

// Get Partner branches
$router->addRoute('api_get_partner_branches', new LMVC_Route('/api/partner/v1/getpartnerbranches/:partnerUserId/:hash/:key',
        array('module' => 'partnerv1',
        'controller' => 'Partnerbranches',
        'action' => 'index')));
        

// Get Referral Fees
$router->addRoute('api_get_referral_fees', new LMVC_Route('/api/partner/v1/getreferralfees/:partnerUserId/:hash/:key',
        array('module' => 'partnerv1',
        'controller' => 'Referralfees',
        'action' => 'index')));


// Get Branch Users
$router->addRoute('api_get_branch_users', new LMVC_Route('/api/partner/v1/getbranchusers/:branchId/:partnerUserId/:hash/:key',
        array('module' => 'partnerv1',
        'controller' => 'Branchusers',
        'action' => 'index')));

        
// Get Bill
$router->addRoute('api_get_introducer_bills', new LMVC_Route('/api/partner/v1/getintroducerbills/:partnerUserId/:hash/:key',
    array('module' => 'partnerv1',
        'controller' => 'Conveyancingintroducerbills',
        'action' => 'index')));

// Get Bill Details
$router->addRoute('api_get_introducer_bill', new LMVC_Route('/api/partner/v1/getintroducerbill/:billId/:partnerUserId/:hash/:key',
        array('module' => 'partnerv1',
            'controller' => 'Conveyancingintroducerbill',
            'action' => 'index')));
        
// Get Bill Details
$router->addRoute('api_send_introducer_bill_email', new LMVC_Route('/api/partner/v1/sendintroducerbillemail/:billId/:partnerUserId/:hash/:key',
        array('module' => 'partnerv1',
            'controller' => 'Conveyancingintroducerbillemail',
            'action' => 'index')));
    
    
// End Partner Portal APIs
		

//Begin Pinlocal REST API

/**

The Plugins_APIValidator cancels any direct access to API controller's add,update,delete action.

Ex. http://www.pinlocal.com/apiv1/companycontact/update/id/200/key/xxxxx

API endpoints should be accessible only through the configured routes below..

Remember to add the module name to API Validator if new api version is created for example apiv2 !!

 */

$apiValidator = new Plugins_APIValidator();
$apiValidator->addAPIModule('apiv1');
$front->registerPlugin($apiValidator);

$smartyFunctions = new Plugins_SmartyFunctions();
$front->registerPlugin($smartyFunctions);

//API version 1

//Find company
$router->addRoute('api_find_companies', new LMVC_Route('/api/v1/companies/find/:key',
		array('module' => 'apiv1',
				'controller' => 'findcompany',
				'action' => 'index')));

//company create
$router->addRoute('api_companies', new LMVC_Route('/api/v1/companies/:key',
		array('module' => 'apiv1',
				'controller' => 'company',
				'action' => 'index')));

//company get, edit & delete
$router->addRoute('api_companies_get_edit_delete', new LMVC_Route('/api/v1/companies/:id/:key',
		array('module' => 'apiv1',
				'controller' => 'company',
				'action' => 'index')));

//contact create
$router->addRoute('api_contacts', new LMVC_Route('/api/v1/contacts/:key',
		array('module' => 'apiv1',
				'controller' => 'companycontact',
				'action' => 'index')));

//contact get, edit & delete
$router->addRoute('api_contacts_get_edit_delete', new LMVC_Route('/api/v1/contacts/:id/:key',
		array('module' => 'apiv1',
				'controller' => 'companycontact',
				'action' => 'index')));

//get lead status
$router->addRoute('api_get_lead_status', new LMVC_Route('/api/v1/leadstatus/:property/:value/:key',
        array('module' => 'apiv1',
                'controller' => 'leadstatus',
                'action' => 'index')));



            
/*
//api to make the telesign verify
$router->addRoute('api_lead_telesign', new LMVC_Route('/api/lead/telesign/:formid/:hash/:key',
                array('module' => 'api',
                    'controller' => 'lead',
                    'action' => 'telesign')));
                    
//api to make the telesign verify
$router->addRoute('api_lead_telesign_verifycode', new LMVC_Route('/api/lead/telesignverification/:formid/:hash/:codeEntered/:key',
                array('module' => 'api',
                    'controller' => 'lead',
                    'action' => 'telesignverification')));
*/

function minify_callback($buffer)
{


    $search = array(
        '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
        '/[^\S ]+\</s',  // strip whitespaces before tags, except space
        '/(\s)+/s'       // shorten multiple whitespace sequences
    );

    $replace = array(
        '>',
        '<',
        '\\1'
    );

    $buffer = preg_replace($search, $replace, $buffer);

    return $buffer;

}

//ob_start('minify_callback');
#dispatch now!
$front->dispatch();

#destroy
unset($front);
?>