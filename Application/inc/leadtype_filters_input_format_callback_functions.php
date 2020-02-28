<?php

/**
 *
 * Central place for 'Lead Type Filters Input Format' callback functions
 *
 */

function formatSourceFilter($post,$filterName){
    
    $filterValues = array();
    $formatedFilters = array();
    $filters = '';
    $useExistingFilterValues = true;
    
    if(isset($post[$filterName.'_value'])){
        
        if(!empty($post[$filterName.'_value'])){
            if(isset($post[$filterName.'_filterType'])){
                $filterValues["sourceFilterType"] = $post[$filterName.'_filterType'];
            }
            if(isset($post[$filterName.'_value'])){
                $filterValues["sourceFilter"] = $post[$filterName.'_value'];
            }
            
            $filters =  json_encode($filterValues);
            $useExistingFilterValues = false;
        }
        else{
            $useExistingFilterValues = false;
        }
    }
    
    return $formatedFilters = array("filters" => $filters,"useExistingFilterValues" => $useExistingFilterValues);
}

function formatMovingDistanceFilter($post,$filterName){
    
    $filterValues = array();
    $formatedFilters = array();
    $filters = '';
    $useExistingFilterValues = true;
    
    if(isset($post[$filterName.'_value'])){
        
        if(!empty($post[$filterName.'_value'])){
            if(isset($post[$filterName.'_filterType'])){
                $filterValues["movingDistanceFilterType"] = $post[$filterName.'_filterType'];
            }
            if(isset($post[$filterName.'_value'])){
                $filterValues["movingDistanceFilter"] = $post[$filterName.'_value'];
            }
            
            $filters =  json_encode($filterValues);
            $useExistingFilterValues = false;
        }
        else{
            $useExistingFilterValues = false;
        }
    }
    
    return $formatedFilters = array("filters" => $filters,"useExistingFilterValues" => $useExistingFilterValues);
}