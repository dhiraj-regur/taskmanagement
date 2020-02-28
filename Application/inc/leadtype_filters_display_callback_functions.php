<?php

/**
 * 
 * Central place for 'Lead Type Filters Display' callback functions
 * 
 */




function countryFilterDisplay($params) {
	
	
	$filterValue = strtolower(trim($params['filterInputValue']));
	
	$countryFilterOptions = array("worldwide" => "Worldwide",
								  "all except ireland" => "Worldwide Except Ireland",
								  "europe" => "Europe Only",
								  "europe except ireland" => "Europe Except Ireland",
								  "specific countries" => "Specific Countries");
	
	$showSpecificCountryInput = false;
	$optionHtml = "";
	
	foreach ($countryFilterOptions as $key => $option) {
		
		
		$selected = false;
		$value = $key;
		
		if($key == "worldwide") $value = "";
		
		
		if(empty($filterValue) && $key == "worldwide") {
			$selected = true;
			$value = "";
		}
		
		if(!empty($filterValue) && !array_key_exists ($filterValue, $countryFilterOptions) && $key == "specific countries") {
			$showSpecificCountryInput = true;
			$selected = true;
		}
		
		if($key == $filterValue) {
			$selected = true;
		}
		
		
		if($selected === true) {
			
			$optionHtml .='<option value="'.$value.'" selected=selected>'.$option.'</option>';
			
		} else {
			
			$optionHtml .='<option value="'.$value.'">'.$option.'</option>';
		}
			
			
	}
	
	
	$style = "display:none;";
	if($showSpecificCountryInput === true) {
		
		$style = "display:inline-block;";
		
	}
		
		
	
	$html = "";
	
	$html .= '<div class="form-group">';
		
		$html .='<div class="col-md-5">
					<label class="control-label text-left lt-filter-fl"><b>'.$params['filterData']['filterLabel'].':</b></label>		
					<label class="control-label text-left lt-filter-sd">'.$params['filterData']['shortDescription'].':</label>
				</div>';
		
		$html .='<div class="col-md-7">
				
					<div class="country-filter-cust-input-group">
						<div class="input-group">
							<select class="form-control" id="country_filter_selector">';
							
							$html .= $optionHtml;
		
					$html .='</select>
							<input style="'.$style.'" class="form-control country-filter" type="text" name="'.$params['inputName'].'" value="'.trim($params['filterInputValue']).'">
							<span class="input-group-addon">for <b>'.$params['filterData']['leadType'].'</b></span>
						</div>
					</div>

					<div class="view-post-codes-wrapper">
									
						<a class="view-country-codes" href="javascript:void(0)">View Country Codes</a>&nbsp;&nbsp;&nbsp;
													
					</div>
									
					<div class="cust-tooltip">
									
						<span data-toggle="tooltip" data-placement="right" title="'.$params['filterData']['guidanceText'].'"><i class="glyphicon glyphicon-info-sign info-sign-cust text-dark-blue"></i></span>
								
					</div>					

									
				</div>';
		
		
	$html .= '</div>';
	
	
	
	return $html;	
	
}



function LeaseHoldFreeHoldDisplay($params) {
	
	$filterValue = strtolower(trim($params['filterInputValue']));
	
	$holdStatus = array("any" => "Any", "freehold" => "FreeHold", "leasehold" => "LeaseHold");
	
	
	$optionHtml = "";
	
	foreach ($holdStatus as $key => $option) 
	{
		$selected = false;
		
		if(empty($filterValue) && $key == "any") 
		{
			$selected = true;
		} 
		else if($filterValue == $key)
		{
			$selected = true;
		}
		
		
		if($selected === true)
		{
			$optionHtml .='<option value="'.$key.'" selected=selected>'.$option.'</option>';
		}
		else
		{
			$optionHtml .='<option value="'.$key.'">'.$option.'</option>';
		}
		
			
			
	}
	
	
	$html = "";
	
	$html .= '<div class="form-group">';
		
		$html .='<div class="col-md-5">
					<label class="control-label text-left lt-filter-fl"><b>'.$params['filterData']['filterLabel'].':</b></label>		
					<label class="control-label text-left lt-filter-sd">'.$params['filterData']['shortDescription'].':</label>
				</div>';
		
		$html .='<div class="col-md-7">
				
					<div class="country-filter-cust-input-group">
						<div class="input-group">
							<select class="form-control" id="country_filter_selector" name="'.$params['inputName'].'">';
							
							$html .= $optionHtml;
		
					$html .='</select>
							<span class="input-group-addon" style="width: 340px;">for <b>'.$params['filterData']['leadType'].'</b></span>
						</div>
					</div>

				</div>';
		
		
	$html .= '</div>';
	
	
	
	return $html;	
	
}

function sourceFilterDisplay($params) {
    
    $filterValues = json_decode($params['filterInputValue'],true);
    
    $filterOptions = array("exclude" => "Don't Assign leads from the following source(s)", "include" => "Only assign leads from the following source(s)");
    
    
    $optionHtml = "";
    
    foreach ($filterOptions as $key => $option)
    {
        $selected = false;
        
        if(empty($filterValues["sourceFilterType"]) && $key == "exclude")
        {
            $selected = true;
        }
        else if($filterValues["sourceFilterType"] == $key)
        {
            $selected = true;
        }
        
        
        if($selected === true)
        {
            $optionHtml .='<option value="'.$key.'" selected=selected>'.$option.'</option>';
        }
        else
        {
            $optionHtml .='<option value="'.$key.'">'.$option.'</option>';
        }
        
        
        
    }
    
    
    $html = "";
    
    $html .= '<div class="form-group">';
    
    $html .='<label class="col-md-2 control-label text-left"><b>'.$params['filterData']['filterLabel'].':</b></label>
            <div class="col-md-3">
                <div class="source-filter-cust-option-group">
					<div class="input-group">
						<select class="form-control" id="source_filter_selector" name="'.$params['filterData']['filterName'].'_filterType">';
                        $html .= $optionHtml;
                        $html .='</select>
					</div>
				</div>
			</div>';
    
    $html .='<div class="col-md-4">
    			<div class="cust-option-group">
					<div class="input-group">
						<input class="form-control" type="text" name="'.$params['filterData']['filterName'].'_value" value="'.$filterValues["sourceFilter"].'">
						<span class="input-group-addon" style="width: 340px;">for <b>'.$params['filterData']['leadType'].'</b></span>
					</div>
				</div>
			</div>
            <div class="col-md-3">		
                <div class="cust-tooltip">
					<span data-toggle="tooltip" data-placement="right" title="'.$params['filterData']['guidanceText'].'"><i class="glyphicon glyphicon-info-sign info-sign-cust text-dark-blue"></i></span>
	 			</div>		    
			</div>';
    
    
    $html .= '</div>';
    
    
    
    return $html;
    
}

function movingDistanceDisplay($params) {
    
    $filterValues = json_decode($params['filterInputValue'],true);
    
    $filterOptions = array("greaterThan" => "Only Assign Leads with Distance Greater Than", "lessThan" => "Only Assign Leads with Distance Less Than");
    
    
    $optionHtml = "";
    
    foreach ($filterOptions as $key => $option)
    {
        $selected = false;
        
        if(empty($filterValues["movingDistanceFilterType"]) && $key == "greaterThan")
        {
            $selected = true;
        }
        else if($filterValues["movingDistanceFilterType"] == $key)
        {
            $selected = true;
        }
        
        
        if($selected === true)
        {
            $optionHtml .='<option value="'.$key.'" selected=selected>'.$option.'</option>';
        }
        else
        {
            $optionHtml .='<option value="'.$key.'">'.$option.'</option>';
        }
        
        
        
    }
    
    
    $html = "";
    
    $html .= '<div class="form-group">';
    
    $html .='<label class="col-md-2 control-label text-left"><b>'.$params['filterData']['filterLabel'].':</b></label>
            <div class="col-md-3">
                <div class="source-filter-cust-option-group">
					<div class="input-group">
						<select class="form-control" id="movingDistance_filter_selector" name="'.$params['filterData']['filterName'].'_filterType">';
    $html .= $optionHtml;
    $html .='</select>
					</div>
				</div>
			</div>';
    
    $html .='<div class="col-md-7">
    			<div class="cust-input-group">
					<div class="input-group">
						<input class="form-control" type="text" name="'.$params['filterData']['filterName'].'_value" value="'.$filterValues["movingDistanceFilter"].'">
						<span class="input-group-addon">for <b>'.$params['filterData']['leadType'].'</b></span>
					</div>
				</div>
			    <div class="cust-tooltip">
					<span data-toggle="tooltip" data-placement="right" title="'.$params['filterData']['guidanceText'].'"><i class="glyphicon glyphicon-info-sign info-sign-cust text-dark-blue"></i></span>
	 			</div>
			</div>';
    
    
    $html .= '</div>';
    
    
    
    return $html;
    
}

