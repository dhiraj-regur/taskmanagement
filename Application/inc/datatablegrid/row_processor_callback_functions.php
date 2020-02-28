<?php

/**
 * Data grid row processor function/callback to modify individual column of the table    
 * @param array $row
 * @return array
 */


function dgCompaniesTable($row)
{
	if($row['status'] == "paused")
	{ 	
		if($row['unPauseDate'] != "0000-00-00" && $row['unPauseDate'] != '')
 		{
		  $pausedDateTime = Helpers_DateFormat::getTimestamp($row['unPauseDate'],$row['unPauseTime'],'Y-m-d');
		  $currentDateTime = Helpers_DateFormat::getTimestamp(date("Y-m-d"),date("H:i:s"),'Y-m-d');
			
		  if($pausedDateTime > $currentDateTime)
		  {
		  	$row['unPauseDate'] = $row['unPauseDate']." ".$row['unPauseTime'];
		  }
		  else
		  {
		  	$row['unPauseDate'] = 'none';
		  }		  
 		}
 		else
 		{
 			$row['unPauseDate'] = 'none';
 		}		
 	}
 	
 	 		
 	
 	if(!empty($row['billingMethod']))
 	{	
 		$pm = Service_PaymentMethodsFactory::getInstance($row['billingMethod']);
 		$row['billingMethod'] = $pm->getpaymentMethodLabel();
 		$row['billingMethodType'] = getAccountTypeLabel($row['billingMethodType']);
 	}
 	else
 	{
 		$row['billingMethod'] = 'None';
 		$row['billingMethodType'] = ''; 		
 	}	
 	
 	if(!empty($row['status']))
 	{
 	  $label =	getCompanyStatusLabel($row['status']);
 	  $className = getCompanyStatusLabelCssClassName($row['status']);
 	  
 	  $row['status'] = $label."|".$className;
 	}
 
 	return $row;
}

/**
 * 
 * @param array $row
 * @return array
 * Below function is used for invoice listing and company invoices(edit company screen).
 */
 
function dgInvoiceTable($row)
{
	if($row['billingMethodType'] == 'prepaid')
	{
		$row['billingPeriod'] = 'N/A';
		$row['totalLeads'] = 'N/A';
	}
	else
	{
		$row['billingPeriod'];
		$row['totalLeads'];
	}
	if(!empty($row['billingMethod']))
	{
		$pm = Service_PaymentMethodsFactory::getInstance($row['billingMethod']);
		$row['billingMethod'] = $pm->getpaymentMethodLabel();
		
		if(!empty($row['subBillingMethod']))
		{
			$row['subBillingMethod'] = $pm->getSubPaymentMethodLabel($row['subBillingMethod']);
		}
	}
	
	if($row['status'] == 'paid' || $row['status'] == 'partial')
	{
		$pm = Service_PaymentMethodsFactory::getInstance($row['paymentMethod']);
		$row['paymentMethod'] = $pm->getpaymentMethodLabel();		
		$row['lastPaymentDate'] = $row['lastPaymentDate'];
			
	}
	else
	{
		$row['paymentMethod'] = 'N/A';
		$row['lastPaymentDate'] = 'N/A';
	}
	
	if($row['paidAmount'] == '' || $row['paidAmount'] == null)
	{	
		$row['paidAmount'] = 0; // Amount paid
	}	
	
	if($row['dueAmount'] == '' || $row['dueAmount'] == null)
	{	
		$row['dueAmount'] = $row['amount'] - $row['paidAmount']; // Due Amount
	}
	
	$row['netAmount'] = dgNumberFormat($row['netAmount']);
	$row['vatAmount'] = dgNumberFormat($row['vatAmount']);
	$row['amount'] = dgNumberFormat($row['amount']);
	$row['paidAmount'] = dgNumberFormat($row['paidAmount']);
	$row['dueAmount'] = dgNumberFormat($row['dueAmount']);
	$row['billingMethodType'] = getAccountTypeLabel($row['billingMethodType']);
	
	// Get Salesforce URL
	if(array_key_exists('sfCompanyId', $row) && array_key_exists('companyId', $row)){
		
		$companyUrl = getCurrentUIContextCompanyURL($row['companyId'], $row['sfCompanyId']);
		$row['companyUrl'] = $companyUrl;
	}
	
	// create instruction invoice info
	$instructionInfo = '';
	if($row["invoiceType"] == INVOICE_TYPE_INSTRUCTION && !empty($row["instructionRef"])){
	    
	    $clientName = $row["firstName"];
	    if(!empty($row["lastName"])){
	        $clientName .= " ".$row["lastName"];
	    }
	    
	    $instructionInfo = "Instruction Ref.: <a href='/crm/instructions/view/id/{$row["instructionId"]}' target='_blank'>".$row["instructionRef"].
	                                       "</a><br/>Client Name: ".$clientName."<br/>Instruction Status: ".$row["instructionStatusLabel"];
	}
	$row["instructionInfo"] = $instructionInfo;
	
	return $row;
}

/**
 *
 * @param array $row
 * @return array
 * Below function is used for payment listing and company paymnets(edit company screen).
 */
function dgPaymentTable($row)
{
	if($row['billingMethodType'] == 'prepaid')
	{
		$row['billingPeriod'] = 'N/A';
		$row['totalLeads'] = 'N/A';
	}
	
	if(!empty($row['billingMethod']))
	{
		$pm = Service_PaymentMethodsFactory::getInstance($row['billingMethod']);
		$row['billingMethod'] = $pm->getpaymentMethodLabel();
	}
	
	if(!empty($row['paymentMethod']))
	{
		$pm = Service_PaymentMethodsFactory::getInstance($row['paymentMethod']);
		$row['paymentMethod'] = $pm->getpaymentMethodLabel();
		if(!empty($row['subPaymentMethod']))
		{
			$row['subPaymentMethod'] = $pm->getSubPaymentMethodLabel($row['subPaymentMethod']);
		}
	}
	$row['billingMethodType'] = getAccountTypeLabel($row['billingMethodType']); 
	$row['netAmount'] = dgNumberFormat($row['netAmount']);
	$row['vatAmount'] = dgNumberFormat($row['vatAmount']);
	$row['amount'] = dgNumberFormat($row['amount']);
	return $row;
}

function dgInstructionTable($row){
    
    if($row["companyInvoiceBalanceExclVat"] > 0 || $row["lastInvoiceId"] > 0){
        
        if($row["lastInvoiceId"] == 0){
            $row["invoiceStatus"] = "Uninvoiced";
        }
        else{
            $row["invoiceStatus"] = ucfirst($row["status"]);
        }
    }
    else{
        $row["invoiceStatus"] = "-";
    }
    
    $depositBalance = 0;
    if($row["isActive"] == 0){
        
        $instruction = new Models_ConveyancingInstruction($row["instructionId"]);
        $depositBalance = $instruction->getTotalRemainderDepositExclVat();
    }
    
    $row["depositBalance"] = pl_number_format($depositBalance);
    
    return $row;
}

function dgUserRoleLabel($row)
{
    $row["role"] = getUserRoleValue($row["role"]);
    return $row;
}

function dgFormatCrmCallLogValues($row){
    $row["formatedContent"] = nl2br($row["content"]);
    return $row;
}

function dgCreditnoteTable($row){
    
    $row["netAmount"] = $row["amount"] - $row["vatAmount"];
    
    $row['netAmount'] = dgNumberFormat($row['netAmount']);
    $row['vatAmount'] = dgNumberFormat($row['vatAmount']);
    $row['amount'] = dgNumberFormat($row['amount']);
    
    return $row;
}

function dgNumberFormat($amount)
{
	return number_format($amount,2);
}


function dgPartnerUserRoleLabel($row)
{
    $row["userRole"] = getPartnerUserRoleLabel($row["userRole"]);
    return $row;
}

function dgLeadtypeEmailTemplate($row)
{

  if (!empty($row["globalEmailTemplateId"]))
  {
                 if(!empty($row["globalTemplateFrom"]))
                 {
                     $row['fromEmail'] = $row["globalTemplateFrom"];
                }
                if(!empty($row["globalTemplateSubject"]))
                {
                    $row['subject'] = $row["globalTemplateSubject"];
                }
    }
    return $row;
}
function dgSmsTypeList($row)
{
    $smsTypes = unserialize(SMS_TEMPLATE_TYPES);
    
    $row["smsType"] = $smsTypes[$row["smsType"]]["label"];

    return $row;
}

function dgSysNotificationTable($row){
    
    $row["formatedType"] = getSysNotificaitonType($row["type"]);
    
    return $row;
}




