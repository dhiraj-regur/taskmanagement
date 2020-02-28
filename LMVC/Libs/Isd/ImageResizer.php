<?php
include_once 'Abs.php';  

class Isd_ImageResizer extends Isd_Abs {
 	var $strOriginalImagePath;
 	var $strResizedImagePath;
 	var $arrOriginalDetails;
	var $arrResizedDetails;
    var $resOriginalImage;
    var $resResizedImage;
	var $resResizedImage_2;
    var $boolProtect = true;
	public static $pic_q=84;
/* 
* 
*   @Method:      __constructor 
*   @Parameters:   5
*   @Param-1:      ścieżka do obrazka źródłowego
*   @Param-2:      strSavePath - String - The path to save the new image to
*   @Param-3:      strType - String - The type of resize you want to perform
*   @Param-4:      value - Number/Array - The resize dimensions 
*   @Param-5:      boolProect - Boolen - Protects the image so that it doesnt resize an image if its already smaller 
* 
*/
  		function __constructor($strPath, $strSavePath, $strType = 'W', $value = '150', $boolProtect = true){
			$this->ISD_ImageResizer($strPath, $strSavePath, $strType, $value);
		}
/* 
* 
*   @Method:      ISD_ImageResizer 
*   @Parameters:   5
*   @Param-1:      strPath - String - The path to the image
*   @Param-2:      strSavePath - String - The path to save the new image to
*   @Param-3:      strType - String - The type of resize you want to perform
*   @Param-4:      value - Number/Array - The resize dimensions 
    @Param-5:      boolProect - Boolen - Protects the image so that it doesnt resize an image if its already smaller 
* 
*/
       
    function ISD_ImageResizer($strPath, $strSavePath, $strType = 'W', $value = '150', $boolProtect = true){
       //save the image/path details
       	$this->strOriginalImagePath = $strPath;
       	$this->strResizedImagePath = $strSavePath;
     	$this->boolProtect = $boolProtect; 
     //get the image dimensions
       	$this->arrOriginalDetails = getimagesize($this->strOriginalImagePath);
		$this->arrResizedDetails = $this->arrOriginalDetails;
      //create an image resouce to work with
       $this->resOriginalImage = $this->createImage($this->strOriginalImagePath);    
      //select the image resize type
       switch(strtoupper($strType)){
		   	case 'P':
				 $this->resizeToPercent($value);
				 break;
			case 'H':
				 $this->resizeToHeight($value);
				break;
         	case 'C':
			   $this->resizeToCustom($value);
				break;
			# tm - modyfikacja - obcina maksymalnie do podanych wymiarow - zawartośc obrazka jest nietknieta	
			case 'B':
			   $this->resizeToBorders($value);
				break;
			
			# tm - modyfikacja - obcina dokaldnie do podanych wymiarow - czesc obrazka jest obcięta
			case 'F':
			   $this->resizeFacebookStyle($value);
				break;
			# tm - modyfikacja - obcina dokaldnie do podanych wymiarow - czesc obrazka jest obcięta
			case 'R':
			   $this->resizeRudakoffStyle($value);
				break;	
			
        	default:
				 $this->resizeToWidth($value);
				break;
       } 
   }
/* 
* 
*   @Method:      setQ 
*   @Parameters:   1 
*   @Param-1:      num - jakośc zdjęcia
*   @Description:   Returns an array of details about the resource identifier that you pass it 
* 
*/ 
   
   static function setQ($num) {
		self::$pic_q = $num;   
	}
   
/* 
* 
*   @Method:      findResourceDetails 
*   @Parameters:   1 
*   @Param-1:      resImage - Resource - The image resource you want details on 
*   @Description:   Returns an array of details about the resource identifier that you pass it 
* 
*/      
    function findResourceDetails($resImage){
      //check to see what image is being requested
       if($resImage==$this->resResizedImage){                              
         //return new image details
          return $this->arrResizedDetails;
       } else {
         //return original image details
          return $this->arrOriginalDetails;
      }
   }
/* 
* 
*   @Method:      updateNewDetails    
*   @Parameters:   0 
*   @Description:   Updates the width and height values of the resized details array 
* 
*/
    
    function updateNewDetails(){  
     	$this->arrResizedDetails[0] = imagesx($this->resResizedImage);
		$this->arrResizedDetails[1] = imagesy($this->resResizedImage);
   }
/* 
* 
*   @Method:      createImage 
*   @Parameters:   1 
*   @Param-1:      strImagePath - String - The path to the image 
*   @Description:   Created an image resource of the image path passed to it 
* 
*/
       
    function createImage($strImagePath){
       //get the image details
       $arrDetails = $this->findResourceDetails($strImagePath); 
      //choose the correct function for the image type 
       switch($arrDetails['mime']){
         case 'image/jpeg':
		 		#zwraca id obrazka
             	return  imagecreatefromjpeg($strImagePath);
            break;
         case 'image/png':
            return imagecreatefrompng($strImagePath);
             break;
         case 'image/gif':
             return imagecreatefromgif($strImagePath);
             break;
      }
    }
/* 
* 
*   @Method:      saveImage 
*   @Parameters:   1 
*   @Param-1:      numQuality - Number - The quality to save the image at 
*   @Description:   Saves the resize image 
* 
*/
  
    function saveImage(){
		$numQuality = self::$pic_q;
		$png_q =  intval($numQuality/10)-1;
		
	  	switch($this->arrResizedDetails['mime']){
          case 'image/jpeg':
             imagejpeg($this->resResizedImage, $this->strResizedImagePath, $numQuality);
             break;
         case 'image/png':
             // imagepng = [0-9] (not [0-100])          
             imagepng($this->resResizedImage, $this->strResizedImagePath, $png_q);
             break;
          case 'image/gif':
            imagegif($this->resResizedImage, $this->strResizedImagePath, $numQuality);
             break;
       }
   }
   
   function saveCutImage(){
	   	$numQuality = self::$pic_q;
		$png_q =  intval($numQuality/10)-1;
		
	   switch($this->arrResizedDetails['mime']){
          case 'image/jpeg':
             imagejpeg($this->resResizedImage_2, $this->strResizedImagePath, $numQuality);
             break;
         case 'image/png':
             // imagepng = [0-9] (not [0-100])          
             imagepng($this->resResizedImage_2, $this->strResizedImagePath, $png_q);
             break;
          case 'image/gif':
             imagegif($this->resResizedImage_2, $this->strResizedImagePath, $numQuality);
             break;
       }
   }
   
     function saveFillImage(){
	   $numQuality = self::$pic_q;
		$png_q =  intval($numQuality/10)-1;
	   switch($this->arrResizedDetails['mime']){
          case 'image/jpeg':
             imagejpeg($this->resResizedImage_2, $this->strResizedImagePath, $numQuality);
             break;
         case 'image/png':
             // imagepng = [0-9] (not [0-100])          
             imagepng($this->resResizedImage_2, $this->strResizedImagePath, $png_q);
             break;
          case 'image/gif':
             imagegif($this->resResizedImage_2, $this->strResizedImagePath, $numQuality);
             break;
       }
   }
   
/* 
* 
*   @Method:      showImage 
*   @Parameters:   1 
*   @Param-1:      resImage - Resource - The resource of the image you want to display 
*   @Description:   Displays the image resouce on the screen 
* 
*/
   function showImage($resImage) {
      //get the image details
       $arrDetails = $this->findResourceDetails($resImage);
       //set the correct header for the image we are displaying 
       header("Content-type: ".$arrDetails['mime']);
      switch($arrDetails['mime']){
          case 'image/jpeg':
             return imagejpeg($resImage);
            break;
          case 'image/png':
            return imagepng($resImage);
            break;
        case 'image/gif':
            return imagegif($resImage);
            break;
     }
   }
/* 
* 
*   @Method:      destroyImage 
*   @Parameters:   1 
*   @Param-1:      resImage - Resource - The image resource you want to destroy 
*   @Description:   Destroys the image resource and so cleans things up 
* 
*/
  	function destroyImage($resImage){
   	 	imagedestroy($resImage);
  	}
 
/* 
* 
*   @Method:      _resize 
*   @Parameters:   2 
*   @Param-1:      numWidth - Number - The width of the image in pixels 
*   @Param-2:      numHeight - Number - The height of the image in pixes 
*   @Description:   Resizes the image by creating a new canvas and copying the image over onto it. DONT CALL THIS METHOD DIRECTLY - USE THE METHODS BELOW 
* 
*/
       
    function _resize($numWidth, $numHeight){
       //check for image protection 
       if($this->_imageProtect($numWidth, $numHeight)){    
          
		  if($this->arrOriginalDetails['mime']=='image/gif'){
           
		   //GIF image
             $this->resResizedImage = imagecreate($numWidth, $numHeight);
       
         } else if($this->arrOriginalDetails['mime']=='image/jpeg'){
       
             //JPG image
             $this->resResizedImage = imagecreatetruecolor($numWidth, $numHeight);
       
         } else if($this->arrOriginalDetails['mime']=='image/png'){ 
             //PNG image 
             $this->resResizedImage = imagecreatetruecolor($numWidth, $numHeight); 
             imagecolortransparent($this->resResizedImage, imagecolorallocate($this->resResizedImage, 0, 0, 0)); 
             imagealphablending($this->resResizedImage, false); 
             imagesavealpha($this->resResizedImage, true); 
          } 
          //update the image size details 
          $this->updateNewDetails(); 
		  
          //do the actual image resize 
         imagecopyresampled ($this->resResizedImage, $this->resOriginalImage, 0, 0, 0, 0, $numWidth, $numHeight, $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]);
         //saves the image 
        	$this->saveImage(); 
      } 
    }   
/* 
* 
*   @Method:      _resizeWithCutWidth 
*   @Parameters:   3 
*   @Param-1:      szerokośc docelowa obrazka
*   @Param-2:      wysokość docelowa obrazka
*   @Param-3:      wysokość dla pierwszego zmniejszenia
*   @Description:   Resizes the image by creatin a new canvas and copying the image over onto it. DONT CALL THIS METHOD DIRECTLY - USE THE METHODS BELOW 
*   DODANE !!!!!!!!!!
* 
*/
       
    function _resizeWithCutWidth($numWidth, $numHeight, $dest_height){
       //check for image protection 
       if($this->_imageProtect($numWidth, $numHeight)){    
          
		  if($this->arrOriginalDetails['mime']=='image/gif'){
           
		   //GIF image 
           $this->resResizedImage = imagecreate($numWidth, $numHeight);
		  
			#drugie	 - właściwe płotno	  
			$this->resResizedImage_2 = imagecreate($numWidth, $dest_height);
       
         } else if($this->arrOriginalDetails['mime']=='image/jpeg'){
       
             //JPG image - tworzy płotno o podanych wymiarach
             $this->resResizedImage = imagecreatetruecolor($numWidth, $numHeight);
			 #drugie	 - właściwe płotno	  
			 $this->resResizedImage_2 = imagecreatetruecolor($numWidth, $dest_height);
       
         } else if($this->arrOriginalDetails['mime']=='image/png'){ 
             //PNG image 
             $this->resResizedImage = imagecreatetruecolor($numWidth, $numHeight); 
             imagecolortransparent($this->resResizedImage, imagecolorallocate($this->resResizedImage, 0, 0, 0)); 
             imagealphablending($this->resResizedImage, false); 
             imagesavealpha($this->resResizedImage, true); 
			 
			  //PNG image 
             $this->resResizedImage_2 = imagecreatetruecolor($numWidth, $dest_height); 
             imagecolortransparent($this->resResizedImage_2, imagecolorallocate($this->resResizedImage_2, 0, 0, 0)); 
             imagealphablending($this->resResizedImage_2, false); 
             imagesavealpha($this->resResizedImage_2, true); 
          } 
          //update the image size details 
          $this->updateNewDetails(); 
          //do the actual image resize 
		 imagecopyresampled($this->resResizedImage, $this->resOriginalImage, 0, 0, 0, 0, $numWidth, $numHeight, $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]);
		 //imagecopy($this->resResizedImage_2, $this->resResizedImage, 0, 0, 0, 0, $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]);
		 imagecopy($this->resResizedImage_2, $this->resResizedImage, 0, 0, 0, 0, $numWidth, $numHeight);
         //saves the image 
        $this->saveCutImage(); 
      } 
    }  
/* 
* 
*   @Method:      _resizeWithCutHeight 
*   @Parameters:   3 
*   @Param-1:      szerokośc docelowa obrazka
*   @Param-2:      wysokość docelowa obrazka
*   @Param-3:      wysokość dla pierwszego zmniejszenia
*   @Description:   Resizes the image by creatin a new canvas and copying the image over onto it. DONT CALL THIS METHOD DIRECTLY - USE THE METHODS BELOW 
*   DODANE !!!!!!!!!!
* 
*/
       
    function _resizeWithCutHeight($numWidth, $numHeight, $dest_width){
       //check for image protection 
       if($this->_imageProtect($numWidth, $numHeight)){    
          
		  if($this->arrOriginalDetails['mime']=='image/gif'){
           
		   //GIF image 
           $this->resResizedImage = imagecreate($numWidth, $numHeight);
		  
			#drugie	 - właściwe płotno	  
			$this->resResizedImage_2 = imagecreate($dest_width, $numHeight);
       
         } else if($this->arrOriginalDetails['mime']=='image/jpeg'){
       
             //JPG image - tworzy płotno o podanych wymiarach
             $this->resResizedImage = imagecreatetruecolor($numWidth, $numHeight);
			 #drugie	 - właściwe płotno	  
			 $this->resResizedImage_2 = imagecreatetruecolor($dest_width, $numHeight);
       
         } else if($this->arrOriginalDetails['mime']=='image/png'){ 
             //PNG image 
             $this->resResizedImage = imagecreatetruecolor($numWidth, $numHeight); 
             imagecolortransparent($this->resResizedImage, imagecolorallocate($this->resResizedImage, 0, 0, 0)); 
             imagealphablending($this->resResizedImage, false); 
             imagesavealpha($this->resResizedImage, true); 
			 
			  //PNG image 
             $this->resResizedImage_2 = imagecreatetruecolor($dest_width, $numHeight); 
             imagecolortransparent($this->resResizedImage_2, imagecolorallocate($this->resResizedImage_2, 0, 0, 0)); 
             imagealphablending($this->resResizedImage_2, false); 
             imagesavealpha($this->resResizedImage_2, true); 
          } 
          //update the image size details 
          $this->updateNewDetails(); 
          //do the actual image resize 
		 
		 //errp()
		 imagecopyresampled($this->resResizedImage, $this->resOriginalImage, 0, 0, 0, 0, $numWidth, $numHeight, $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]);
		 //imagecopy($this->resResizedImage_2, $this->resResizedImage, 0, 0, 0, 0, $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]);
			imagecopy($this->resResizedImage_2, $this->resResizedImage, 0, 0, 0, 0, $numWidth, $numHeight);
         //saves the image 
        $this->saveCutImage(); 
      } 
    }  
	
/* 
* 
*   @Method:      _imageProtect 
*   @Parameters:   2 
*   @Param-1:      numWidth - Number - The width of the image in pixels 
*   @Param-2:      numHeight - Number - The height of the image in pixes 
*   @Description:   Checks to see if we should allow the resize to take place or not depending on the size the image will be resized to 
* 
*/    
    function _imageProtect($numWidth, $numHeight){ 
      	if($this->boolProtect AND ($numWidth > $this->arrOriginalDetails[0] OR $numHeight > $this->arrOriginalDetails[1])){ 
      		return 0; 
 		} 
      	return 1; 
 	} 
/* 
* 
*   @Method:      resizeToWidth 
*   @Parameters:   1 
*   @Param-1:      numWidth - Number - The width to resize to in pixels 
*   @Description:   Works out the height value to go with the width value passed, then calls the resize method. 
* 
*/
   function resizeToWidth($numWidth){
 		$numHeight = (int)(($numWidth*$this->arrOriginalDetails[1])/$this->arrOriginalDetails[0]);
  		$this->_resize($numWidth, $numHeight);   
 	}
	
/* 
* 
*   @Method:      resizeToWidthWithCut 
*   @Parameters:   1 
*   @Param-1:      numWidth - Number - The width to resize to in pixels 
*   @Description:   Works out the height value to go with the width value passed, then calls the resize method. 
*   @ DODANE !!!!!!!
* 
*/
   function resizeToWidthWithCut($numWidth, $dest_heigth){
	   	$numHeight = (int)(($numWidth*$this->arrOriginalDetails[1])/$this->arrOriginalDetails[0]);
  		$this->_resizeWithCutWidth($numWidth, $numHeight, $dest_heigth);      
 	}	
/* 
* 
*   @Method:      resizeToHeight 
*   @Parameters:   1 
*   @Param-1:      numHeight - Number - The height to resize to in pixels 
*   @Description:   Works out the width value to go with the height value passed, then calls the resize method. 
* 
*/
	function resizeToHeight($numHeight){
		$numWidth = (int)(($numHeight*$this->arrOriginalDetails[0])/$this->arrOriginalDetails[1]);
		
		$this->_resize($numWidth, $numHeight);   
 	}
/* 
* 
*   @Method:      resizeToHeight 
*   @Parameters:   1 
*   @Param-1:      numHeight - Number - The height to resize to in pixels 
*   @Description:   Works out the width value to go with the height value passed, then calls the resize method. 
* 
*/
	function resizeToHeightWithCut($numHeight, $dest_width){
		$numWidth = (int)(($numHeight*$this->arrOriginalDetails[0])/$this->arrOriginalDetails[1]);
		$this->_resizeWithCutHeight($numWidth, $numHeight, $dest_width);
 	}
 
 /* 
* 
*   @Method:      resizeToHeightWithFill
*   @Parameters:   1 
*   @Param-1:      numWidth - Number - The width to resize to in pixels 
*   @Description:   Works out the height value to go with the width value passed, then calls the resize method. 
*   @ DODANE !!!!!!!
* 
*/
   function resizeToHeightWithFill($numHeight, $dest_width){
		$numWidth = (int)(($numHeight*$this->arrOriginalDetails[0])/$this->arrOriginalDetails[1]);
		
		//$this->_resizeWithCutHeight($numWidth, $numHeight, $dest_width);
 	}
/* 
* 
*   @Method:      resizeToPercent 
*   @Parameters:   1 
*   @Param-1:      numPercent - Number - The percentage you want to resize to 
*   @Description:   Works out the width and height value to go with the percent value passed, then calls the resize method. 
* 
*/
	function resizeToPercent($numPercent){
		$numWidth = (int)(($this->arrOriginalDetails[0]/100)*$numPercent);
		$numHeight = (int)(($this->arrOriginalDetails[1]/100)*$numPercent);
  		$this->_resize($numWidth, $numHeight);   
 	}
/* 
* 
*   @Method:      resizeToCustom 
*   @Parameters:   1 
*   @Param-1:      size - Number/Array - Either a number of array of numbers for the width and height in pixels 
*   @Description:   Checks to see if array was passed and calls the resize method with the correct values. 
* 
*/
   function resizeToCustom($size){
		if(!is_array($size)){
		    $this->_resize((int)$size, (int)$size);
		 } else {
		    $this->_resize((int)$size[0], (int)$size[1]);
 		}
 	}
	
	
/* 
* 
*   @Method:      resizeToBorders 
*   @Parameters:   1 
*   @Param-1:      size - Array - array of numbers for the border width and height in pixels 
*   @Description:   Checks to see if array was passed and calls the resize method with the correct values. 
* 
*/
   function resizeToBorders($size){
		if(is_array($size)){
			$org_details = $this->arrOriginalDetails;
			
			$fac_org = (int)$org_details[0]/$org_details[1];
			$fac_given = (int)$size[0]/$size[1];
			
			#jesli wiekszy, wtedy trzymamy sie wysokości
			if($fac_given>$fac_org) {
				$this->resizeToHeight($size[1]);
			#jesli mniejszy, wtedy trzymamy sie szerokości	
			} elseif($fac_given<$fac_org) {
				$this->resizeToWidth($size[0]);
			} else {
				$this->resizeToCustom($size);
			}
 		} else {
			trigger_error("zły parametr");
		}
 	}	
/* 
* 
*   @Method:      resizeFacebookStyle 
*   @Parameters:   1 
*   @Param-1:      size - Array - array of numbers for the border width and height in pixels 
*   @Description:  przycina obrazek maksymalnie do podanych rozmiarów
* 
*/
	
	function resizeFacebookStyle($size) {
		if(is_array($size)){
			$org_details = $this->arrOriginalDetails;
			
			#wspolczynnik oryginalny
			$fac_org = (int)$org_details[0]/$org_details[1];
			
			#wspolczynnik podany
			$fac_given = (int)$size[0]/$size[1];
			
			#jesli wiekszy, wtedy trzymamy sie szerokości
			if($fac_given > $fac_org) {
				$this->resizeToWidthWithCut($size[0], $size[1]);
			#jesli mniejszy, wtedy trzymamy sie wysokości	
			} elseif($fac_given < $fac_org) {
				$this->resizeToHeightWithCut($size[1], $size[0]);
			} else {
				$this->resizeToCustom($size);
			}
 		} else {
			trigger_error("zły parametr");
		}	
	}
#################################################################################################################################################################################################################################
/* 
* 
*   @Method:      resizeRudakoffStyle 
*   @Parameters:   1 
*   @Param-1:      size - Array - array of numbers for the border width and height in pixels 
*   @Description:  pomniejsza obrazek dokaldnie do podanych rozmiarów zachowując propocje, reszte wypelnie białyum tłem
* 
*/
	
	function resizeRudakoffStyle($size) {
		
		if(is_array($size)){
			$org_details = $this->arrOriginalDetails;
			
			#wspolczynnik oryginalny
			$fac_org = (float)$org_details[0]/$org_details[1];
			
			#wspolczynnik podany
			$fac_given = (float)$size[0]/$size[1];
			//echo 'fac_org: '.$fac_org.' fac_given: '.$fac_given;
			#jesli wiekszy, wtedy trzymamy sie szerokości
			if($fac_given > $fac_org) {
				//echo 'jeden ';
				$new_sizes = $this->getSizesToHeightWithFill($size[0], $size[1]);
				$this->_resizeWithFillHeight($new_sizes);
			} elseif($fac_given < $fac_org) {
				//echo 'dwa ';
				$new_sizes = $this->getSizesToWidthWithFill($size[0], $size[1]);
				//ee($new_sizes); 
				$this->_resizeWithFillWidth($new_sizes);
			} else {

				$this->resizeToCustom($size);
			}
 		} else {
			trigger_error("zły parametr");
		}	
	}
	#Description: Oblicza docelowe rozmary płotna i obrazka
	private function getSizesToHeightWithFill($canvas_width, $canvas_height){
	   	$new_sizes = array();
		$actual_pic_width = ceil(($canvas_height*$this->arrOriginalDetails[0])/$this->arrOriginalDetails[1]);
		$left_margin = floor(($canvas_width - $actual_pic_width)/2);
		$new_sizes['canvas_width'] = $canvas_width;
		$new_sizes['canvas_height'] = $canvas_height;
		$new_sizes['actual_pic_width'] = $actual_pic_width;
		$new_sizes['left_margin'] = $left_margin;
		return $new_sizes;  
 	}
	
	#Description: Oblicza docelowe rozmary płotna i obrazka
	private function getSizesToWidthWithFill($canvas_width, $canvas_height){
	   	$new_sizes = array();
		$actual_pic_height = ceil(($canvas_width*$this->arrOriginalDetails[1])/$this->arrOriginalDetails[0]);
		$top_margin = floor(($canvas_height - $actual_pic_height)/2);
		$new_sizes['canvas_width'] = $canvas_width;
		$new_sizes['canvas_height'] = $canvas_height;
		$new_sizes['actual_pic_height'] = $actual_pic_height;
		$new_sizes['top_margin'] = $top_margin;
		return $new_sizes;  
 	}
	
	private function _resizeWithFillHeight($new_sizes){
       //check for image protection 
       	if(!$this->_imageProtect($new_sizes['canvas_width'], $new_sizes['canvas_height'])) return;
	   
	   	if($this->arrOriginalDetails['mime']=='image/gif'){
			 #właściwy obrazek
             $this->resResizedImage = imagecreate($new_sizes['actual_pic_width'], $new_sizes['canvas_height']);
			  #płótno
			 $this->resResizedImage_2 = imagecreate($new_sizes['canvas_width'], $new_sizes['canvas_height']);
			 $background = imagecolorallocate($this->resResizedImage, 255, 255, 255);
			 $background_2 = imagecolorallocate($this->resResizedImage_2, 255, 255, 255);
				
		} else if($this->arrOriginalDetails['mime']=='image/jpeg'){
             
			 #właściwy obrazek
			 $this->resResizedImage = imagecreatetruecolor($new_sizes['actual_pic_width'], $new_sizes['canvas_height']);
			 #płótno
			 $this->resResizedImage_2 = imagecreatetruecolor($new_sizes['canvas_width'], $new_sizes['canvas_height']);
			 $background = imagecolorallocate($this->resResizedImage, 255, 255, 255);
			 $background_2 = imagecolorallocate($this->resResizedImage_2, 255, 255, 255);
			 
        } else if($this->arrOriginalDetails['mime']=='image/png'){ 
            #właściwy obrazek
             $this->resResizedImage = imagecreatetruecolor($new_sizes['actual_pic_width'], $new_sizes['canvas_height']); 
             imagecolortransparent($this->resResizedImage, imagecolorallocate($this->resResizedImage, 0, 0, 0)); 
			  $background = imagecolorallocate($this->resResizedImage, 255, 255, 255);
             imagealphablending($this->resResizedImage, false); 
             imagesavealpha($this->resResizedImage, true); 
			 
			  #płótno
             $this->resResizedImage_2 = imagecreatetruecolor($new_sizes['canvas_width'], $new_sizes['canvas_height']);
			 $background_2 = imagecolorallocate($this->resResizedImage_2, 255, 255, 255);
             imagecolortransparent($this->resResizedImage_2, imagecolorallocate($this->resResizedImage_2, 0, 0, 0)); 
             imagealphablending($this->resResizedImage_2, false); 
             imagesavealpha($this->resResizedImage_2, true); 
			 
			
        } 
		
		imagefill($this->resResizedImage, 0, 0, $background);
		imagefill($this->resResizedImage_2, 0, 0, $background_2);
		$this->updateNewDetails(); 
		
		#przekopiowujemy dane z oryginalnego obrazka do wlasciwej miniaturki
        imagecopyresampled($this->resResizedImage, $this->resOriginalImage, 0, 0, 0, 0, $new_sizes['actual_pic_width'], $new_sizes['canvas_height'], $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]);
		imagecopy($this->resResizedImage_2, $this->resResizedImage, $new_sizes['left_margin'], 0, 0, 0, $new_sizes['actual_pic_width'], $new_sizes['canvas_height']);
		
         //saves the image 
       	$this->saveFillImage(); 
    } 
   private function _resizeWithFillWidth($new_sizes){

       //check for image protection 
       	if(!$this->_imageProtect($new_sizes['canvas_width'], $new_sizes['canvas_height'])) return;
	   
	   	if($this->arrOriginalDetails['mime']=='image/gif'){
			 #właściwy obrazek
             $this->resResizedImage = imagecreate($new_sizes['canvas_width'], $new_sizes['actual_pic_height']);
			  #płótno
			 $this->resResizedImage_2 = imagecreate($new_sizes['canvas_width'], $new_sizes['canvas_height']);
			 $background = imagecolorallocate($this->resResizedImage, 255, 255, 255);
			 $background_2 = imagecolorallocate($this->resResizedImage_2, 255, 255, 255);
				
		} else if($this->arrOriginalDetails['mime']=='image/jpeg'){
             
			 #właściwy obrazek
			 $this->resResizedImage = imagecreatetruecolor($new_sizes['canvas_width'], $new_sizes['actual_pic_height']);
			 #płótno
			 $this->resResizedImage_2 = imagecreatetruecolor($new_sizes['canvas_width'], $new_sizes['canvas_height']);
			 $background = imagecolorallocate($this->resResizedImage, 255, 255, 255);
			 $background_2 = imagecolorallocate($this->resResizedImage_2, 255, 255, 255);
			
        } else if($this->arrOriginalDetails['mime']=='image/png'){ 
            #właściwy obrazek
             $this->resResizedImage = imagecreatetruecolor($new_sizes['canvas_width'], $new_sizes['actual_pic_height']); 
             imagecolortransparent($this->resResizedImage, imagecolorallocate($this->resResizedImage, 0, 0, 0)); 
			  $background = imagecolorallocate($this->resResizedImage, 255, 255, 255);
             imagealphablending($this->resResizedImage, false); 
             imagesavealpha($this->resResizedImage, true); 
			 
			  #płótno
             $this->resResizedImage_2 = imagecreatetruecolor($new_sizes['canvas_width'], $new_sizes['canvas_height']);
			 $background_2 = imagecolorallocate($this->resResizedImage_2, 255, 255, 255);
             imagecolortransparent($this->resResizedImage_2, imagecolorallocate($this->resResizedImage_2, 0, 0, 0)); 
             imagealphablending($this->resResizedImage_2, false); 
             imagesavealpha($this->resResizedImage_2, true); 
			 
			
        } 
		
		imagefill($this->resResizedImage, 0, 0, $background);
		imagefill($this->resResizedImage_2, 0, 0, $background_2);
		$this->updateNewDetails(); 
		
		#przekopiowujemy dane z oryginalnego obrazka do wlasciwej miniaturki
        imagecopyresampled($this->resResizedImage, $this->resOriginalImage, 0, 0, 0, 0, $new_sizes['canvas_width'], $new_sizes['actual_pic_height'], $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]);
		imagecopy($this->resResizedImage_2, $this->resResizedImage, 0, $new_sizes['top_margin'], 0, 0, $new_sizes['canvas_width'], $new_sizes['actual_pic_height']);
		
         //saves the image 
       	$this->saveFillImage(); 
    } 	
}
?>