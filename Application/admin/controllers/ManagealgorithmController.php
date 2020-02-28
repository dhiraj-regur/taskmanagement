<?php
class Admin_ManagealgorithmController extends LMVC_Controller{
	
	public function init(){
		
	}
	
	public function indexAction(){
						
		//Getting all leadType Categories which are active
		$leadCategory = new Models_LeadTypeCategory();
		$leadTypeCategory = $leadCategory->getAll(array('id,name'),"active = 'y'");
				
		$leadTypesCategories = array();
		foreach($leadTypeCategory as $result){
			$leadTypesCategories[$result->id] = $result->name;
		}

		$this->setViewVar('leadTypesCategories', $leadTypesCategories);
				
		//Getting All Zip Code Zones in drop down list
		$zipCodeZones = new Models_ZipCodeZones();
		$allZones = $zipCodeZones->getAll(array('id,zone'));
		
		$zipCodeZones = array();
		foreach($allZones as $data){
			$zipCodeZones[$data->id] = $data->zone;
		}
		
		$this->setViewVar('zipCodeZones', $zipCodeZones);	
		
		
	}
	
	public function getleadtypesAction(){
	
		$this->setNoRenderer(true);
	
		global $db;
	
		$leadTypeCatId = $this->getRequest()->getPostVar('leadtypecatid');
	
		$leadTypes = new Models_LeadTypes();
		$allLeadstypes = $leadTypes->getAllLeadTypes("leadTypeCatId = '$leadTypeCatId'");
		
		//returning lead types drop down from selected leadtype category
		echo '<option value="">Please Select </option>';
		
		foreach ($allLeadstypes as $result){
			echo '<option value="'.$result->id.'">'.$result->typeName.'</option>';
		}
	}
	
	public function getzoneprefixalgorithmsAction(){
	
		$this->setNoRenderer(true);
	
		global $db;
	
		$zoneId = $this->getRequest()->getPostVar('zoneid');
		$leadTypeId = $this->getRequest()->getPostVar('leadtypeid');
	
		//Getting all Zipcodezone Prefixes
		$zipCodeZonePrefix = new Models_ZipCodePrefixes();

		//Below Function contains two optional parameters first is getting zipcodezone prefix for only active zone(pass: true/false) and 
		//second is for particular zone for zipcodezone prefix
		$zonePrefix = array();
		
		//to get all zip code zone prefixes set zoneId to null
		if($zoneId == "all"){
			$zoneId = "";
		}
		
		$zonePrefix= $zipCodeZonePrefix->getAllGroupByZone(false,$zoneId);
		
		
				
		//getting values of enum types
		$leadTypeAlgorithm = new Models_LeadTypeAlgorithm();
		$algorithms = Models_LeadTypeAlgorithm::getAssignmentAlgorithms();
		
		//Getting all the values from lead_type_algorithm table

		$leadTypeAlgorithmDetails = $leadTypeAlgorithm->getAll(array('leadTypeId,zipCodePrefixId,assignmentAlgorithm'),"leadTypeId = '$leadTypeId'");
		$table = '';
		
 		//creating master switch div
		$table .='<div id="masterSwitch"><table style="padding-left: 155px;margin-bottom: 15px;"><tr><td class="col-md-3"><label class="control-label">Bulk Action : </label>';
        $table .='</td><td class="col-md-9"><select id="algorithms" name="assignmentAlgorithms" class="form-control">';
        
				foreach ($algorithms as $key=>$value){
					$table .='<option value='.$key.'>'.$value.'</option>';
				}
							
      	$table .='</select></td><td><input id="applyButton" class="btn btn-success" style="margin-left: 10px;" type="button" value="Apply" />';
        $table .='</td></tr></table></div><br />';
        //master switch div over
        
        
        //creating save and reset button on top
        $table .='<div class="col-md-6"></div>';
        $table .='<div class="col-md-6">';
        $table .='<input class="saveButton btn btn-success" type="button" value="Save Algorithm" /><input class="resetButton btn btn-default" style="margin-left: 10px;" type="button" value="Reset Settings" />';		
        $table .='</div><br />';
        
        //creating table of zone prefix and assignment algorithms
		$table .='<table id="algorithmTable" cellspacing="5" style="margin-bottom: 10px;"><thead><tr>';
	    $table .='<th height="40" style="padding-left: 5px;vertical-align: middle;">Prefix </th><th height="40" style="vertical-align: middle;">Name </th><th height="40" style="vertical-align: middle;">Algorithm</th>';
		$table .= '</tr></thead><tbody>';
		$table .= '<br />';
		
		foreach($zonePrefix as $data){
				foreach($data as $zipCodeZone){
					
					$table .= '<tr>';
					$table .= '<td height="50" style="padding-left: 12px;vertical-align: middle;">'.$zipCodeZone["zipCode"] .'</td>';
					$table .= '<td height="50" style="vertical-align: middle;">'.$zipCodeZone["name"] .'</td>';
					$table .= '<td height="50" style="vertical-align: middle;"><select style="width:80%;" class="assignment_algorithm form-control" name="algorithm[]['.$zipCodeZone["prefixId"].']">';
					
					
					foreach ($algorithms as $key=>$value){
						
						$table .='<option value='.$key." ";
						
								foreach ($leadTypeAlgorithmDetails as $leadTypeAlgo){
									
									//checking the algorithm from database if algorithm found then display it as selected
									if($leadTypeAlgo->zipCodePrefixId == $zipCodeZone["prefixId"]){
										if($leadTypeAlgo->assignmentAlgorithm == $key){
											$table .= 'selected';
										}else{	
											$table .= '';
										}	
										$table .= '>'.$value.'</option>';
									}
								}
								$table .= '>'.$value.'</option>';
					}
					
					$table .= '</select></td>';
					$table .='</tr>';
				}
		}
		$table .= '</tbody></table>';
		
		//creating save and reset button at bottom
		$table .='<br /><div class="col-md-6"></div>';
        $table .='<div class="col-md-6">';
		$table .='<input class="saveButton btn btn-success" type="button" value="Save Algorithm" /><input class="resetButton btn btn-default" style="margin-left: 10px;" type="button" value="Reset Settings" />';
		$table .='</div>';

		$response = array("algorithmTable" => $table);
		echo json_encode($response);
	
	}
	
	public function savealgorithmAction(){
		
		$this->setNoRenderer(true);
		
		global $db;
		
		//getting the leadTypeId
		$leadTypeId = $this->getRequest()->getPostVar('leadTypes');
		
		//getting zonePrefixId and its selected/default assignment algorithm 
		$formData = $this->getRequest()->getPostVar('algorithm');

		$saveAlgorithm = false;
		
		foreach ($formData as $results){
			
			//initializing leadTypeAlgorithm object
			$leadTypeAlgorithm = new Models_LeadTypeAlgorithm();
			
			foreach ($results as $zipCodePrefixId => $assignmentAlgorithm){
				
				//fetching the data from table through leadTypeId and zoneprefixId
				$leadTypeAlgorithm->fetchByProperty(array('leadTypeId','zipCodePrefixId'),array($leadTypeId,$zipCodePrefixId));
				
					//if data is found just update the assignment algorithm
					if(!$leadTypeAlgorithm->isEmpty){	
								
								$leadTypeAlgorithm->assignmentAlgorithm = $assignmentAlgorithm;
								$result = $leadTypeAlgorithm->update(array('assignmentAlgorithm'));
								
								if($result>0){
									$saveAlgorithm = true;
								}
						
					}else{
						
						//if data is not found from database then insert it
						$leadTypeAlgorithm->leadTypeId = $leadTypeId;
						$leadTypeAlgorithm->zipCodePrefixId = $zipCodePrefixId;
						$leadTypeAlgorithm->assignmentAlgorithm = $assignmentAlgorithm;
						
						$id = $leadTypeAlgorithm->create();
						
						if($id>0){
							$saveAlgorithm = true;
						}
					}
				
			}//inner foreach ended
		}//main foreach ended
		
		$response = array('saveAlgorithm' => $saveAlgorithm);
		echo json_encode($response);
	}
}

?>