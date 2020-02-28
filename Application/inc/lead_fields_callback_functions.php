<?php
/**
 * 
 * Central place for 'Lead Form Fields' callback functions.
 * 
 */

function on_form_html_lender_display( $params ) 
{
	$html = '';
	$field = $params['field'];
	$mode = $params['mode'];
	
	$requiredAsterisk = '';
	if($field->required) { $requiredAsterisk = "*"; }
	
	$lender = new Models_Lenders();
	$options = $lender->getLenderOptions();
	
	if($mode == "internal_lead_edit")
	{
	    $html ='<div class="form-group input_container input_'. $field->name .'" role="dropdownfield">
	        
			<label name="label" class="control-label col-md-3">'. $field->label . '&nbsp;<span class="req_field">'. $requiredAsterisk .'</span></label>
			<div class="col-md-4"><select class="form-control" id="'. $field->id .'" name="'. $field->name .'">';
	}
	else
	{
	    $html ='<div class="form-group input_container input_'. $field->name .'" role="dropdownfield">
	        
			<label name="label" class="control-label col-md-5">'. $field->label . '&nbsp;<span class="req_field">'. $requiredAsterisk .'</span></label>
			<div class="col-md-7"><select class="form-control" id="'. $field->id .'" name="'. $field->name .'">';
	}
									
	$selectedChoice = $params['value'];
	
	
	foreach($options as $key => $val)
	{
		$optGroupLabel = $key;
		if($key == 'no_lender') $optGroupLabel = 'No Lender';
		else if($key == 'lenders') $optGroupLabel = 'Lenders';
		
		$html .= "<optgroup label='{$optGroupLabel}'></optgroup>";
		foreach($val as $k => $lenderData) {
			$selected = "";
			if($lenderData['id'] == $selectedChoice || ($selectedChoice == '' && $lenderData['id'] == '0')) { $selected = "selected = 'selected'"; }
			$html .= "<option value='{$lenderData['id']}' $selected>{$lenderData['value']}</option>";
		}
		
	}
	
	$html .='</select>
		
			</div>
	  </div>';
	
	$html .='<hr/>';
	
	return $html;
}  


function on_form_data_lender_display( $params ) {
	
	$value = $params['value'];
	if($value > 0) 
	{
		$lender = new Models_Lenders($value);
		if(!$lender->isEmpty) {
			$value = $lender->name;
		}
	}
	elseif($value == 0) 
	{
		$value = 'Not Sure Yet';	
	}
	elseif($value == -1) 
	{
		$value = 'Cash Purchase - No Lender';
	}
	elseif($value == -2)
	{
		$value = 'Other - Not On List';
	}
	
	return $value;
	
}

function on_form_html_instruct_time_display( $params ) {
    
    $html = '';
    $field = $params['field'];
    $mode = $params['mode'];
    
    if($mode != "internal_lead_edit"){
        $html = $params['html'];
    }
    
    if($mode == "internal_lead_edit"){
        
        $options = array("Within 1 week", "Within 1 month", "Within 3 months", "Not sure");
    
        $requiredAsterisk = '';
        if($field->required) { $requiredAsterisk = "*"; }
    
        $html ='<div class="form-group input_container input_'. $field->name .'" role="dropdownfield">
            
			<label name="label" class="control-label col-md-3">'. $field->label . '&nbsp;<span class="req_field">'. $requiredAsterisk .'</span></label>
			<div class="col-md-4"><select class="form-control" id="'. $field->id .'" name="'. $field->name .'">';
        
        $selectedChoice = $params['value'];
        $html .= "<option value=''>Please Select</option>";
        foreach($options as $val)
        {
            $selected = "";
            if($val == $selectedChoice) { $selected = "selected = 'selected'"; }
            $html .= "<option value='{$val}' $selected>{$val}</option>";
        }
        
        $html .='</select></div></div>';
        $html .='<hr/>';
    }
    
    return $html;
}


