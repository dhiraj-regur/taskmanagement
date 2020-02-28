<?php
//config constants.
set_include_path( MVC_PATH."/LMVC/Libs" . PATH_SEPARATOR . get_include_path());
define('SITE_ROOT', MVC_PATH ."/public");
define('FORM_DIR','/images/forms/');
define('JS_DIR','/js/');

define('ENV', getenv('ENV'));
define('SITE_URL', getenv('SITE_URL'));
define('SITE_URL_SSL', getenv('SITE_URL_SSL'));	//secured https url

define('DEFAULT_MINIMUM_BALANCE',15);
define('LEADTYPE_DEFAULT_CAP_TYPE','weekly');

//ADMIN ALERT EMAILS

define('ADMIN_EXTRA_EMAIL','liam@pinlocal.com');
define('ADMIN_EXTRA_EMAIL2','jenny@pinlocal.com');


define('CONSUMER_CRM_LEAD_DISPLAY_CATIDS',serialize(array(4,5,9,10,11)));

define('LEADTYPE_CAP_MONTHLY',serialize(array('monthly' => 1000, 'weekly' => 0, 'daily' => 0, 'addWeeklyCap' => 'n', 'addDailyCap' => 'n')));
define('LEADTYPE_CAP_WEEKLY',serialize(array('monthly' => 0, 'weekly' => 1000, 'daily' => 0, 'addWeeklyCap' => 'n', 'addDailyCap' => 'n')));
define('LEADTYPE_CAP_DAILY',serialize(array('monthly' => 0, 'weekly' => 0, 'daily' => 1000, 'addWeeklyCap' => 'n', 'addDailyCap' => 'n')));

define('LEADTYPECAT_CAP_MONTHLY',serialize(array('monthly' => 5000, 'weekly' => 0, 'daily' => 0, 'addWeeklyCap' => 'n', 'addDailyCap' => 'n')));
define('LEADTYPECAT_CAP_WEEKLY',serialize(array('monthly' => 0, 'weekly' => 5000, 'daily' => 0, 'addWeeklyCap' => 'n', 'addDailyCap' => 'n')));
define('LEADTYPECAT_CAP_DAILY',serialize(array('monthly' => 0, 'weekly' => 0, 'daily' => 5000, 'addWeeklyCap' => 'n', 'addDailyCap' => 'n')));


define('BILLING_METHOD_MANUAL','manual');
define('BILLING_METHOD_RECURLY','recurly');
define('BILLING_METHOD_STRIPE','stripe');
define('BILLING_METHOD_DIRECTDEBIT','directdebit');
define('BILLING_METHOD_PREPAY_RECURLY', 'prepayrecurly');
define('BILLING_METHOD_PREPAY_STRIPE', 'prepaystripe');
define('BILLING_METHOD_CHEQUE','cheque');
define('BILLING_METHOD_OTHER', 'other');

//Storing the file path(physical) in the constant 
define('DB_BACKUP_FILE_PATH','/var/sqlbackup/pinlocal_live.sql.gz'); //please adjust if needed

/**
 *  key and value (value = class name) class name must be capitalize
 * @var array $registeredPaymentMethods
 */
$registeredPaymentMethods = array(BILLING_METHOD_MANUAL =>'Manual',BILLING_METHOD_DIRECTDEBIT =>'Directdebit',BILLING_METHOD_STRIPE =>'Stripe',BILLING_METHOD_CHEQUE=>'Cheque',BILLING_METHOD_OTHER=>'Other',BILLING_METHOD_RECURLY=>'Recurly'); 

$donotReassignReasons = array('new-company'=>'New Company','gone-over-caps'=>'Gone Over Caps','out-of-area-reassigns'=>'Out of Area Re-assigns','bedroom-sizes'=>'Bedroom Sizes','never-reassign'=>'NEVER RE-ASSIGN');
define('DONOT_REASSIGN_REASONS',serialize($donotReassignReasons));

$accountTypes = array();

$accountTypes = array(
		'postpaid'=>'Postpaid',
		'prepaid'=>'Prepay'
		) ;  

$leadCreditReasonsArray = array ('duplicate' =>'Duplicate',
		'no_ring'=>'Dead Line',
		'invalid_phone_email'=>'Invalid Details',
		'no_response'=>'No Response',		
		'not_the_customer'=>'Not The Customer',		
		'short_notice_period'=>'Short Notice (<48 hours)',
		'out_of_area'=>'Out of Area',
		'other'=>'Other');

define('LEAD_CREDIT_REASONS',serialize($leadCreditReasonsArray));

define('COMPANY_LOGO_PATH', SITE_ROOT . '/company_resources/logo/');
define('COMPANY_LOGO_URL', SITE_URL . '/company_resources/logo/');

define('CONSUMER_CRM_AUTO_ARCHIVE_LEAD_STATUS_SLUG',serialize(array('spam'))); 

//email type array
$emailTypes = array('confirmation_lead_success' => array('label' => 'Lead Confirmation Email'),
		'site_review_prompt' => array('label'=>'Site Review Prompt'),
		'broadband_prompt' => array('label'=> 'Broadband Prompt'),
		'company_review_prompt' => array('label'=> 'Company Review Prompt'),
		'conveyancing_quotes' => array('label' => 'Conveyancing Quotes Email'),
		'follow_up_email' => array('label' => 'Follow Up Email'),
		'consumer_callback_request' => array('label' => 'Consumer Callback Request Success'),
		'consumer_instruct_request' => array('label' => 'Consumer Instruct Request Success'),
		'client_instruction_confirmation_email' => array('label' => 'Client Instruction Confirmation Email'),
		'company_instruction_confirmation_email' => array('label' => 'Company Instruction Confirmation Email'));

define('EMAIL_TEMPLATE_TYPES', serialize($emailTypes));

$companyCalculatedStatus = array("notactive"=>"Not Active ","confirmed"=>"Confirmed","intrial"=>"In Trial","paying"=>"Paying","paused"=>"Paused");
$companyDeactivatedStatus = array("deactivated"=>"De-activated","junk"=>"Junk","debtor"=>"Debtor","stopped_trading"=>"Stopped Trading");		


//SMS type array
$smsTypes = array('confirmation_lead_success' => array('label' => 'Lead Confirmation SMS'),
				  'conveyancing_quote_sms'=> array('label'=>'Conveyancing Quote SMS'));

define('SMS_TEMPLATE_TYPES', serialize($smsTypes));

/* @@ Ticket 91 (Lead tagging mechanism)
 *
*/
$leadTagsKey = array('sourcepage' => 'Source Page','rid' => 'RM Region ID', 'reg' => 'RM Region', 'loc' => 'AW Location', 'dst' => 'AW Destination','typ' => 'Company Type','matchtype' => 'Match Type','network' => 'Network','device' => 'Device',
					 'campaignid' => 'Campaign ID','adgroupid' => 'Ad Group ID','feeditemid' => 'Feed Item ID','targetid' => 'Target ID','loc_physical_ms' => 'Location (Actual)','loc_interest_ms' => 'Location (Of Interest)',
					 'devicemodel' => 'Device Model','creative' => 'Creative ID','keyword'=>'Keyword','placement'=>'Placement','target'=>'Target','aceid'=>'AdWords Campaign Experiments ID','adposition'=>'Ad Position');
define('LEAD_TAGS', serialize($leadTagsKey));


//Ticket #92(Reverse run)

$reverseRunLeadTypes = array('1');
define('REVERSE_RUN_LEADTYPES', serialize($reverseRunLeadTypes));


//Ticket #120 (Assignment algorithms)
define('ALGO_ROUND_ROBIN','round_robin');
define('ALGO_PROFIT_MAXIMIZER','profit_maximizer');
$assignmentAlgorithms = array(
		ALGO_ROUND_ROBIN => 'Round Robin',
		ALGO_PROFIT_MAXIMIZER => 'Profit Maximizer'
);

define('ASSIGNMENT_ALGORITHMS', serialize($assignmentAlgorithms));


define('EVALUATION_LEADS_THRESHOLD',50); //default evaluation leads count set for a company.




/*
 * Map 'To Post Code' field based on lead type of form.
* Main index is of leadType and in value set the field form wise(key is of form id and value is a 'To Post Code' field)
*
*/

$toPostCodeMapping = array( '1' => array('1' => 'to_postcode', '35' => 'to_postcode', '48' => 'to_postcode'), '5' => array('3' => 'to_postcode', '36' => 'to_postcode')); 
define('TO_POST_CODE_MAPPING', serialize($toPostCodeMapping));

//Assignment Mode Label
$assignmentModeLabel = array('cron' => 'Cron', 'triggered' => 'Triggered', 'reassign' => 'Re-Assigned', 'reverse_run' => 'Reverse Run', 'na' => 'N.A');
define('ASSIGNMENT_MODE_LABEL', serialize($assignmentModeLabel));


//end ticket #92(Reverse run)

#Ticket239 Distance Calculation of postcode code for DM and CM leads
$removalsLeadTypePrefix = array('DM','CM');
define('REMOVALS_LEAD_TYPE_PREFIX', serialize($removalsLeadTypePrefix));

define('REMOVALS_LEAD_TYPE_CATEGORY_ID', 1);


#Ticket #107(Rename 'Removals form fields rename')
$formFieldsRenameMapping = array("3" => array('f4' => 'to_address_line1','f19' => 'to_postcode', 'f18' => 'moving_date', 'f14' => 'storage_service', 
											  'f15' => 'packing_service','f16' => 'assembly_service','f17' => 'special_instructions'),
								 "36" => array('f4' => 'to_address_line1','address_line_2' => 'to_address_line2','f19' => 'to_postcode', 'floor_to' => 'to_floor',
											   'lift_available_to' => 'to_lift_available', 'parking_available_to' => 'to_parking_available', 'parking_issues_to' => 'to_parking_issues',
											   'f14' => 'storage_service','f15' => 'packing_service','f16' => 'assembly_service','f18' => 'moving_date','f17' => 'special_instructions'),
								 "1" => array('f18' => 'to_postcode','f19' => 'to_city','f16' => 'no_of_bedrooms', 'f23' => 'storage_service','f24' => 'assembly_service',
								  			   'f25' => 'packing_service','f28' => 'moving_date','f27' => 'special_instructions'),
								 "35" => array('f16'=>'no_of_bedrooms','f19'=>'to_city','f18'=>'to_postcode','f25'=>'packing_service','f24'=>'assembly_service',
								 			   'f23'=>'storage_service','f28'=>'moving_date','f27'=>'special_instructions'),
								 "48" => array('f16'=>'no_of_bedrooms','f19'=>'to_city','f18'=>'to_postcode','f25'=>'packing_service','f24'=>'assembly_service',
								 			   		'f23'=>'storage_service','f28'=>'moving_date','f27'=>'special_instructions'), //Movinga exclusive form
								 "4" => array('f9'=>'to_city','f10'=>'to_postcode','f11'=>'to_country','f13'=>'moving_date','f14'=>'storage_service',
								 			  'f15'=>'vehicle_transport','f16'=>'employer_paying_cost','f17'=>'special_instructions'),
								 "37" => array('f9'=>'to_city', 'f10'=>'to_postcode', 'f11'=>'to_country', 'f15'=>'packing_service', 'f16'=>'assembly_service',
								 			   'f14'=>'storage_service', 'f13'=>'moving_date', 'f17'=>'special_instructions')			  													 
								);

define('REMOVALS_FORM_FIELDS_RENAME_MAPPING', serialize($formFieldsRenameMapping));

define('TOTAL_LEAD_ASSIGNED_COMPANY_COLUMN', 6); //#iss149(lead data export)

#Partner Referral Fees Constant
define('PARTNER_REFERRAL_FEE','Partner Referral Fee');
define('MI_REFERRAL_FEE','Master Introducer Fee');
define('PL_PREMIUM_FEE','PinLocal Premium Fee');
define('HEAD_OFFICE_COMMISSION','Head Office Commission');

// Conveyancing Quote Static Fees
$cnvQuoteStaticFees = array(
    array('feeName' => 'Legal Fees', 'feeClassName' => 'LegalFee'),
    array('feeName' => 'Leasehold Fee', 'feeClassName' => 'Leasehold'),
    array('feeName' => 'Mortgage Admin Fee', 'feeClassName' => 'MortgageAdmin'),
    array('feeName' => 'Telegraphic Transfer (TT) Fee', 'feeClassName' => 'TelegraphicTransfer'),
    array('feeName' => 'Help to Buy Equity Loan', 'feeClassName' => 'HelpToBuy'),
    array('feeName' => 'Right To Buy Fee', 'feeClassName' => 'RightToBuy'),
    array('feeName' => 'Shared Ownership Fee', 'feeClassName' => 'SharedOwnership'),
    array('feeName' => 'Newbuild Fee', 'feeClassName' => 'Newbuild'),
    array('feeName' => 'Land Registry Search', 'feeClassName' => 'LandRegistrySearch'),
    array('feeName' => 'Land Charges Search', 'feeClassName' => 'LandChargesSearch'),
    array('feeName' => 'Identity Check', 'feeClassName' => 'IdentityCheck'),
    array('feeName' => 'Anti Money Laundering Search', 'feeClassName' => 'AntiMoneyLaunderingSearch'),
    array('feeName' => 'Official Copies', 'feeClassName' => 'OfficialCopies'),
    array('feeName' => 'Local Search Insurance', 'feeClassName' => 'LocalSearchInsurance'),
    array('feeName' => 'Search Pack (Local Authority, Water & Drainage, Environmental Searches)', 'feeClassName' => 'SearchPack'),
    array('feeName' => 'Help To Buy ISA Fee', 'feeClassName' => 'HelpToBuyISA'),
    array('feeName' => 'Buy To Let Fee', 'feeClassName' => 'BuyToLet'),
    array('feeName' => 'Auction Fee', 'feeClassName' => 'Auction'),
    array('feeName' => 'Auction Legal Pack', 'feeClassName' => 'SaleAuction'),
    array('feeName' => 'Islamic Mortgage Fee', 'feeClassName' => 'IslamicMortgage'),
    array('feeName' => 'Referral Fee', 'feeClassName' => 'Referral'),
    array('feeName' => 'Multi-Search', 'feeClassName' => 'MultiSearch'),
    array('feeName' => 'Land Register Discharge Dues', 'feeClassName' => 'LandRegisterDischargeDues'),
    array('feeName' => 'Land Register Advance Notice', 'feeClassName' => 'LandRegisterAdvanceNotice'),
    array('feeName' => 'Search and Land and Building Transaction Return', 'feeClassName' => 'SearchAndLandAndBuildingTransactionReturn'),
    array('feeName' => 'Advance Notice of Standard Security', 'feeClassName' => 'AdvanceNoticeofStandardSecurity'),
    array('feeName' => 'Land Register Standard Security Dues', 'feeClassName' => 'LandRegisterStandardSecurityDues'),
    array('feeName' => 'Standard Security Registration Dues', 'feeClassName' => 'StandardSecurityRegistrationDues'),
    array('feeName' => 'Registry Fees', 'feeClassName' => 'Registry'),
    array('feeName' => 'Land Registration Dues', 'feeClassName' => 'LandRegistrationDues'),
    array('feeName' => 'Auction Search Pack', 'feeClassName' => 'AuctionSearchPack'),
    array('feeName' => 'Gifted Deposit Fee', 'feeClassName' => 'GiftedDeposit'),
    array('feeName' => 'Transfer of Equity Fee', 'feeClassName' => 'TransferOfEquity'),
	array('feeName' => 'Company Search', 'feeClassName' => 'CompanySearch')
);
define('CNV_QUOTE_STATIC_FEES', serialize($cnvQuoteStaticFees));


#128 (SalesForce)
define('SF_SANDBOX_AUTH_URL','https://test.salesforce.com/services/oauth2/authorize');
define('SF_LIVE_AUTH_URL','https://login.salesforce.com/services/oauth2/authorize');

define('SF_SANDBOX_TOKEN_URL', 'https://test.salesforce.com/services/oauth2/token');
define('SF_LIVE_TOKEN_URL', 'https://login.salesforce.com/services/oauth2/token');


#Exclusive lead + Altium
define('ALTIUM_LEAD_TYPE_CATEGORY_ID', '10');
define('CONVEYANCING_EXCLUSIVE_LEAD_TYPE_CATEGORY_ID', '9');

$exclusiveLeadTypeIds = array(42,43,44,45,52,53,54,55);
define('EXCLUSIVE_LEAD_TYPE_IDS', serialize($exclusiveLeadTypeIds));

$conveyancingLeadTypeCategorySlugs = array('conveyancing','conveyancing-exclusive','altium-conveyancing');
define('CONVEYANCING_LEAD_TYPE_CATEGORY_SLUGS', serialize($conveyancingLeadTypeCategorySlugs));

#269 Email Templates
$emailContentBlocksSlugs = array('exclusive_quotes_email_intro'=>'Exclusive Quotes Email Intro');
define('EMAIL_CONTENT_BLOCKS', serialize($emailContentBlocksSlugs));

#264 - Provider Singup
define('BILLING_URL_SALT', 'paPx0kJImNpL');


#269-sms SMS Templates
$smsContentBlocksSlugs = array('exclusive_quotes_sms_intro'=>'Exclusive Quotes SMS Intro');
define('SMS_CONTENT_BLOCKS', serialize($smsContentBlocksSlugs));


#267 SMS to only mobile numbers
$validMobileInitials = array('07', '+07', '447', '+447', '0447', '00447');
define('VALID_MOBILE_INITIALS', serialize($validMobileInitials));


// form ids and leadtypeCatIds
$conveyancingLeadTypeCategoryIds = array(4,9,10);
define('CONVEYANCING_LEAD_TYPE_CATEGORY_IDS', serialize($conveyancingLeadTypeCategoryIds));

// partner quote fees transaction type
$quoteTransactionTypes = array('conveyancing' => array('sale', 'purchase', 'remortgage'));
define('QUOTE_TRANSACTION_TYPES', serialize($quoteTransactionTypes));


$CNSPLeadFormIds = array(5,52,56);
define('CNSP_LEAD_FORM_IDS', serialize($CNSPLeadFormIds));
	
$CNSLeadFormIds = array(7,53,57);
define('CNS_LEAD_FORM_IDS', serialize($CNSLeadFormIds));
	
$CNPLeadFormIds = array(31,51,55);
define('CNP_LEAD_FORM_IDS', serialize($CNPLeadFormIds));
	
$CNRLeadFormIds = array(32,54,58);
define('CNR_LEAD_FORM_IDS', serialize($CNRLeadFormIds));


// #371 - Ignore form questions
$ignoreLeadFormQuestions = array('financial_mortgage_opt_in','financial_remortgage_opt_in');
define('IGNORE_LEAD_FORM_QUESTIONS', serialize($ignoreLeadFormQuestions));

// #487 - Lead XML feed tweak
$ignoreEmptyNodeFromXmlAndCsv = array('purchase_property_purchase_schemes');
define('IGNORE_EMPTY_NODE_FROM_XML_AND_CSV', serialize($ignoreEmptyNodeFromXmlAndCsv));

// System Objects Webhook Target Systems

// Target name and Object name Must Match with the DB(`system_objects_webhook_queue`)
// Ex: Target: Salesforce
// Ex: Object: Invoice

$targets = array('Salesforce' => array('Invoice','NLC'));
define('SYSTEM_OBJECTS_WEBHOOK_TARGETS', serialize($targets));

#330 - additional quote edit authorization rights
define("CAN_AUTHORISE_QUOTE_EDIT",serialize(array("rvs","lharrison")));

#330 - invoice type
define("INVOICE_TYPE_LEAD", "1");
define("INVOICE_TYPE_INSTRUCTION", "2");


#Rotating Premium Company
define("ENABLE_ROTATING_PREMIUM", TRUE);

# HERE-API
define('HERE_API_APP_ID', getenv('HERE_API_APP_ID'));  
define('HERE_API_APP_CODE', getenv('HERE_API_APP_CODE'));

# Redis
define('USE_REDIS',true);
define("REDIS_LEADS_KEY", "pinLocalLeads");
define("REDIS_LENDERS_KEY", "pinLocalLenders");
define("REDIS_INPROCESS_INVOICES_KEY", "bulkPaymentStripeInvoices");
define("REDIS_INPROCESS_INVOICES_COUNTER_KEY", "bulkPaymentStripeInvoicesCounter");
define("REDIS_INVOICE_STRIPE_PAYMENT_WORKER_KEY", "rmqWorkerInvoicePaymentCollectorStatus");
define("REDIS_INVOICE_STRIPE_PAYMENT_LOG_KEY", "bulkPaymentStripeInvoicesLogs");
define("REDIS_ROTATING_PREMIUM_COMPANY_IDS_KEY", 'rotatingPremiumCompanyIds');
define("REDIS_RMQ_CONFIGURATION_KEY", "pinLocalRMQConfiguration");
define("REDIS_RMQ_WORKER_STATUS_KEY", "pinLocalRMQWorkerStatus");
define("REDIS_SYSTEM_NOTIFICATIONS_KEY","systemNotifications");
define("REDIS_MULTI_SELECT_UI_COMPONENT_KEY","pinLocalMultiSelectUIComponentData");

define("REDIS_NEW_LEADS_FOR_AGENT_KEY","newLeadsForAgent"); // old key need to remove later on
define("REDIS_AGENT_LEAD_ASSIGNMENT_DATA","agentLeadAssignmentData"); // old key need to remove later on

define("REDIS_LEAD_QUEUE_FOR_AGENT_KEY","leadQueueForAgent"); // new key - iss415
define("REDIS_AGENT_LEAD_ASSIGNMENT_INFO_KEY","agentLeadAssignmentInfo"); // new key - iss415


define("REDIS_LOCKED_INVOICES_KEY", "pinLocalLockedInvoices");


#RabbitMQ
define("RABBITMQ_LEAD_DISTANCE_CALCULATOR_QUEUE", "lead_distance_queue");
define("RABBITMQ_QUOTE_PDF_GENERATOR_QUEUE", "quote_pdf_generation_queue");
define("RABBITMQ_CONSUMER_NOTIFICATION_QUEUE","consumer_notification_queue");
define("RABBITMQ_LEAD_ASSIGNMENT_QUEUE","lead_assignment_queue");
define("RABBITMQ_CRM_LEAD_CREATE_QUEUE","crm_lead_create_queue");
define("RABBITMQ_CONVEYANCING_QUOTE_EMAILS_QUEUE","conveyancing_quote_emails_queue");
define("RABBITMQ_INVOICE_STRIPE_PAYMENT_QUEUE", "invoice_stripe_payment_queue");
define("RABBITMQ_COMPANY_LEAD_EMAIL_QUEUE", "company_lead_email_queue");
define("RABBITMQ_LEAD_NEARBY_COMPANY_QUEUE", "lead_nearby_company_queue");
define("RABBITMQ_NEW_LEADS_QUEUE","new_leads_queue");
define("RABBITMQ_REVERSE_RUN_LEAD_ASSIGNMENT_QUEUE","reverse_run_lead_assignment_queue");


#349 - Partner Portal
define('PARTNER_PAYMENTMODEL_PPL', 1);
define('PARTNER_PAYMENTMODEL_PPI', 2);
define('PP_NON_SCOTTISH_QUOTE_COMPANY_ID', 3321);
define('PP_SCOTTISH_QUOTE_COMPANY_ID', 3322);

#399 - RabbitMQ monitoring system
define('SYSTEM_NOTIFICATION_EMAIL', getenv('SYSTEM_NOTIFICATION_EMAIL'));

#txtLocal API
define('TXT_LOCAL_API_KEY', 'dzSPWuNdFWQ-WyTToq1ZtnaVwotNO7fpRJAjj6PA0h');

#387
define("DEFAULT_AL_INSTRUCTION_DEPOSIT_AMOUNT", 50);

#443
define("MOVING_DISTANCE_FILTER_ID", 40);

# .env specific config variables defined here..

define('DB_HOST',getenv('DB_HOST'));
define('DB_UNAME',getenv('DB_UNAME'));
define('DB_PWD',getenv('DB_PWD'));
define('DB_NAME',getenv('DB_NAME'));

define('ADMIN_ALERT_EMAIL',getenv('ADMIN_ALERT_EMAIL'));
define('DEBUG_EMAIL',getenv('DEBUG_EMAIL'));

define('LEAD_ALERT_FROMEMAIL',getenv('LEAD_ALERT_FROMEMAIL'));
define('LEAD_ALERT_ADMINEMAIL',getenv('LEAD_ALERT_ADMINEMAIL'));

define('LEAD_CONVERT_PARTNER_API',getenv('LEAD_CONVERT_PARTNER_API'));

define('STRIPE_SECRET_KEY',getenv('STRIPE_SECRET_KEY')); // stripe account pinlocal
define('STRIPE_PUBLIC_KEY',getenv('STRIPE_PUBLIC_KEY'));

define('CONVEYANCING_CONSUMER_DEPOSIT_STRIPE_SECRET_KEY',getenv('CONVEYANCING_CONSUMER_DEPOSIT_STRIPE_SECRET_KEY')); // stripe account conveyancing instruction
define('CONVEYANCING_CONSUMER_DEPOSIT_STRIPE_PUBLIC_KEY',getenv('CONVEYANCING_CONSUMER_DEPOSIT_STRIPE_PUBLIC_KEY'));

define('SF_PRODUCTION_MODE', (getenv('SF_PRODUCTION_MODE') === 'TRUE') ? TRUE : FALSE);
define('SF_CLIENT_ID', getenv('SF_CLIENT_ID')); 
define('SF_CLIENT_SECRET', getenv('SF_CLIENT_SECRET')); 
define('SF_REDIRECT_URI',getenv('SF_REDIRECT_URI'));

// Altium Legal Site
define('ALTIUM_LEGAL_DEFAULT_CALLBACK_URL', getenv('ALTIUM_LEGAL_DEFAULT_CALLBACK_URL'));
define('ALTIUM_LEGAL_DEFAULT_INSTRUCT_URL', getenv('ALTIUM_LEGAL_DEFAULT_INSTRUCT_URL'));

define('ALTIUM_PARTNER_PORTAL_URL',getenv('ALTIUM_PARTNER_PORTAL_URL'));

// iss415
define('NODE_JS_SERVER_IP',getenv('NODE_JS_SERVER_IP'));
define('TAKE_A_LEAD_PANEL_ENABLE_TIME',getenv('TAKE_A_LEAD_PANEL_ENABLE_TIME'));
define('TAKE_A_LEAD_PANEL_DISABLE_TIME',getenv('TAKE_A_LEAD_PANEL_DISABLE_TIME'));
define('TAKE_A_LEAD_PANEL_ENABLE_TIME_IN_WEEKEND',getenv('TAKE_A_LEAD_PANEL_ENABLE_TIME_IN_WEEKEND'));
define('TAKE_A_LEAD_PANEL_DISABLE_TIME_IN_WEEKEND',getenv('TAKE_A_LEAD_PANEL_DISABLE_TIME_IN_WEEKEND'));
define('TAKE_A_LEAD_FEATURE_DEPLOYMENT_DATE','2019-05-31 09:00:00'); 


#AWS Backup
define('AWS_KEY', getenv('AWS_KEY')); // AWS ACCOUNT KEY
define('AWS_SECRET_KEY', getenv('AWS_SECRET_KEY')); // AWS ACCOUNT SECRET KEY
define('AWS_REGION', getenv('AWS_REGION'));
define('AWS_BUCKET_NAME', getenv('AWS_BUCKET_NAME')); // AWS bucket to use
define('DB_BACKUP_FILE_DIRECTORY', getenv('DB_BACKUP_FILE_DIRECTORY'));


# Redis
define("REDIS_HOST", getenv('REDIS_HOST'));
define("REDIS_PORT", getenv('REDIS_PORT'));

# RabbitMQ
define("RABBITMQ_HOST", getenv('RABBITMQ_HOST'));
define("RABBITMQ_PORT", getenv('RABBITMQ_PORT'));
define("RABBITMQ_USERNAME", getenv('RABBITMQ_USERNAME'));
define("RABBITMQ_PASSWORD", getenv('RABBITMQ_PASSWORD'));

define("BULK_PAYMENT_STRIPE_INVOICES_RMQ_WORKER_PATH", getenv('BULK_PAYMENT_STRIPE_INVOICES_RMQ_WORKER_PATH'));
define("RABBITMQ_QUEUE_API_URL", getenv('RABBITMQ_QUEUE_API_URL'));


$agentAssignableLeadStatuses = array('unassigned'=>"Unassigned",'invalid'=>"Invalid",'hold'=>"Hold",'spam'=>"Spam");
define('AGENT_ASSIGNABLE_LEAD_STATUSES',serialize($agentAssignableLeadStatuses));

# Lead Assignment log notification email address
define("LEAD_ASSIGNMENT_LOG_EMAIL", getenv('LEAD_ASSIGNMENT_LOG_EMAIL'));



// #415 user tabs 
$userRulesLtcIds = array(9,10,11);
define('USER_RULES_LTC_IDS', serialize($userRulesLtcIds));

define('SYSTEM_LOGS_PATH', APPLICATION_PATH . '/extras/logs/');

# Google reCAPTCHA
define('GOOGLE_CAPTCHA_SITE_KEY', getenv('GOOGLE_CAPTCHA_SITE_KEY'));
define('GOOGLE_CAPTCHA_SECRET_KEY', getenv('GOOGLE_CAPTCHA_SECRET_KEY'));

//#482 list of IP to allow assess of TestController.
define('TESTCONTROLLER_WHITELIST_IP', getenv('TESTCONTROLLER_WHITELIST_IP'));

//#495 - Tax code options for company setting if no UK vat charge
$taxCodes = array('T20'=>'EU - Reverse Charge (T20)', 'T0'=>'Outside of EU (T0)');
define('TAX_CODES',serialize($taxCodes));

// pens down, stop editing from this line onwards!!

define('LOG_QUERIES', (getenv('LOG_QUERIES') === 'TRUE') ? TRUE : FALSE);

if(ENV === 'production')
{
    error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT);
    
    ini_set('display_errors', 'Off');
    ini_set("log_errors", 1);
    $errorLogFileName = 'pinlocal-system-'. date('Y-m-d') .".log";
    ini_set("error_log", MVC_PATH ."/Application/extras/logs/web/". $errorLogFileName);
   	
}
elseif(ENV === 'staging')
{
    error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT);
    
    ini_set('display_errors', 'Off');
    ini_set("log_errors", 1);
    $errorLogFileName = 'pinlocal-system-'. date('Y-m-d') .".log";
    ini_set("error_log", MVC_PATH ."/Application/extras/logs/web/". $errorLogFileName);
    
    
}
else
{
	error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT);
    ini_set('display_errors', 'On');

}

//social post variables
define('TWITTER_CONSUMER_KEY', getenv('TWITTER_CONSUMER_KEY'));
define('TWITTER_CONSUMER_SECRET', getenv('TWITTER_CONSUMER_SECRET'));
define('TWITTER_ACCESS_TOKEN', getenv('TWITTER_ACCESS_TOKEN'));
define('TWITTER_ACCESS_TOKEN_SECRET', getenv('TWITTER_ACCESS_TOKEN_SECRET'));
define('SOCIALPOST_DIR','/images/socialpost/');
define('FACEBOOK_APP_SECRET', getenv('FACEBOOK_APP_SECRET'));
define('FACEBOOK_CLIENT_TOKEN', getenv('FACEBOOK_CLIENT_TOKEN'));
define('FACEBOOK_PAGE_ACCESS_TOKEN', getenv('FACEBOOK_PAGE_ACCESS_TOKEN'));
define('FACEBOOK_APP_ID', getenv('FACEBOOK_APP_ID'));


?>