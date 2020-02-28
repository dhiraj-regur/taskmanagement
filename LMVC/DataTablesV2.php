<?php
/*
 * @author: Dharti
 * @support: Datatable version 1.10+ 
 * This class is support the referencing fields/columns by their name instead of numerice indexes as in LMVC_DataTables 
 * and earlier versions of datatables javascript grid. Class is compatable with datatable V 1.10+ JS library.
 * 
 *
 *  
 */

class LMVC_DataTablesV2
{
	private $db;
	private $table;
	private $idColumn;
	private $columns = array();	
	private $offset;
	private $length;
	private $totalRecords;
	private $totalFilteredRecords;
	private $joins;
	private $groupBy;
	private $havingClause;
	private $defaultFilters = array();
	private $disabledSearchFields = array();
    private $lastSQL = '';    
    private $rowProcessor;
    private $request;
    
    // get data table data from view part
    
    private $dtColumns = array();
    private $dtColumnsName = array();
    private $dtColumnsCount; 
    
	
	/*
	 * For server side data (Which we are using in controllers file to set the data)
	*/
	
	public function __construct()
	{
		$this->idColumn = "id";	
		$this->request = LMVC_Request::getInstance();
		
		$this->offset = $this->getVar('start');				
		$this->length = $this->getVar('length');	
		
		
	}

	public function setDBAdapter($_adapter)
	{
		$this->db = $_adapter;
	}

	public function getDBAdapter()
	{
		return $this->db;
	}

	public function setTable($_table)
	{
		$this->table = $_table;
	}
	
	public function setIdColumn($_column)
	{
		$this->idColumn = $_column;
	}

	public function addColumns($_columns)
	{
		if(is_array($_columns))
		{		
			$this->columns = $_columns; // key value pair
			return $this;
		}
		else
		{
			trigger_error('Columns must be an associate array', E_USER_ERROR);
		}
		
	}     
     
	public function setJoins($_joinStatement)
	{
		$this->joins = $_joinStatement;
	}
	
	public function setDefaultFilters($_defaultFilters)
	{
		$this->defaultFilters = $_defaultFilters;
		
	}
	
	public function setHavingClause($_havingClause)
	{
		$this->havingClause = $_havingClause;
	}
	
	public function setGroupBy($_groupBy)
	{
		$this->groupBy = $_groupBy;
	}
	

	public function setOffset($_offset)
	{
		$this->offset = $_offset;
	}

	public function setLength($_length)
	{
		$this->length = $_length;
	}
	
	public function ignoreSearchFields($_fields)
	{
		$this->disabledSearchFields = $_fields;
	}
	
	public function getVar($_varname)
	{
		if(!$this->request->isPost())
		{
			return $this->request->getVar($_varname);
		}
		else
		{
			return $this->request->getPostVar($_varname);
		}
	}
		
	
	public function overrideOrderByField($colIndex,$field)
	{
		$this->overriddenOrderByFields[$colIndex] = $field;
	} 
	
	public function registerRowProcessor($_callable)	
	{
		if(is_callable($_callable))
		{
			$this->rowProcessor = $_callable;
		}
	}
	
	public function unregisterRowProcessor($_callable)
	{
		unset($this->rowProcessor);
	}

	// end server side data 
	
	/*
	 * For client side data (data table view part)
	 */
	private function getDtColumns()
	{
		$this->dtColumns = $this->getVar('columns');
		
		return $this->dtColumns;
	}
	
	private function getDtColumnsName()
	{	
		
		foreach($this->getDtColumns() as $key => $value)
		{			
			 if(empty($value['name']) && $value['searchable'] == "true")
			{							
				trigger_error('Please set \'name\' property of the column ('. $key .') in your javascript column definition of this column. Or set \'searchable\' false for this column', E_USER_ERROR);
			} 
			
			{
				array_push($this->dtColumnsName, $value['name']);
			}	
			
		}
		
		return $this->dtColumnsName;
	}
	
	private function getDtColumnsCount()
	{
		$this->dtColumnsCounts = count($this->getDtColumns());

		return $this->dtColumnsCounts;
	}
	
    // End client side data
	
	public function getData()
	{		
		
		$aColumns = $this->columns; // server side cols
		$dataTableKeys = $this->getDtColumnsName();		
		$dtColumns = $this->getDtColumns() ; // client side's col
		
		$sTable	= $this->table;
	
	
		/** Paging */
	
		$sLimit = "";
		if ( isset( $this->offset ) && $this->length != -1 )
		{
			$sLimit = "LIMIT ". $this->offset .", ". $this->length;
		}
	
		//ordering
		$sOrder = "";
	
		$order = $this->getVar('order');
		if(!empty($order))
		{
			$sOrder = "ORDER BY  ";
			$iSortingCols = $this->getVar('order');
				
			$iSortingCols = (empty($iSortingCols))?0:$iSortingCols;
			
								
			foreach ($iSortingCols as $key => $value)
			{				
				if ($dtColumns[$value['column']]['orderable'] == "true" )
				{									
				    $orderField = $aColumns[$dataTableKeys[$value['column']]];
						
					/* if(array_key_exists($this->getVar('iSortCol_'.$i),$this->overriddenOrderByFields))
					{
						$orderField = $this->overriddenOrderByFields[$dataTableKeys[$i]];
					} */
	
					$sOrder .= $orderField." ". $value['dir'] .", ";
				}
			}
	
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}		
		
	 
		//filtering
		$sWhere = "";
		$dtSearchVal = $this->getVar('search');
		if ($dtSearchVal['value'] != '')
		{
			$sWhere = "WHERE (";			
			
			foreach($dataTableKeys as $colKey)
			{
				if(!empty($colKey))
				{
					$filterField ='';			
						
					if(!in_array($colKey, $this->disabledSearchFields))
					{
						$filterField = $aColumns[$colKey];
				
						$sWhere .= $filterField ." LIKE '%". $dtSearchVal['value'] ."%' OR ";
					}			
								
				}	
			}		

			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
	
		/* Individual column filtering */
	
		foreach ($dtColumns as $key => $dtColData)
		{			
			
			if($dtColData['searchable'] == "true" && $dtColData['search']['value'] != '')
			{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				$filterField = $aColumns[$dtColData['name']];
				
				$sWhere .= $filterField." LIKE '%". $dtColData['search']['value']."%' ";
			}
		}
	
		//default filters
	
		$sDefaultFilter = "";
		foreach( $this->defaultFilters as $key=>$val )
		{
				
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
	
			if($key == "combination_filter")
			{
				$sWhere .=  $val ;
			}
			elseif(is_array($val))
			{
				$sWhere .= $key  ." ". $val['condition'] ." ";
			}
			else
			{
				$sWhere .= $key ."=". $val ." ";
			}
	
	
			//default filter
			if ( $sDefaultFilter == "" )
			{
				$sDefaultFilter = "WHERE ";
			}
			else
			{
				$sDefaultFilter .= " AND ";
			}
	
	
			if($key == "combination_filter")
			{
				$sDefaultFilter .=  $val ;
			}
			elseif(is_array($val))
			{
				$sDefaultFilter .= $key ." ". $val['condition'] ." ";
			}
			else
			{
				$sDefaultFilter .= $key ."=". $val ." ";
			}
	
		}
	
	
	
		$sJoin = "";
		if($this->joins)
		{
			$sJoin = $this->joins;
		}
	
	
		$sGroupBy = "";
	
		if($this->groupBy)
		{
			$sGroupBy = $this->groupBy;
		}
	
		
		$sHavingClause = "";
		
		if($this->groupBy && $this->havingClause)
		{
			$sHavingClause = $this->havingClause;
		}
		
		
	
		/** SQL queries. Get data to display */
	
		$db =  $this->getDBAdapter();
	
		$fields = array();
		foreach($aColumns as $key=>$colName)
		{
					
			$col = $colName. ' AS ' .$key;	
			
			array_push($fields, $col);
			 
		}
	
		$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $fields)) ." FROM $sTable $sJoin $sWhere $sGroupBy $sHavingClause $sOrder $sLimit";
	
		//echo $sQuery;
		$this->lastSQL = $sQuery;
	
		$result = $db->getAll($sQuery,DB_FETCHMODE_ASSOC);	
		
		$sQuery = "SELECT FOUND_ROWS()";
		$this->totalFilteredRecords = $db->getOne($sQuery);
	
		/* Total data set length */
	
		if($sWhere != "")
		{
			if($sGroupBy!="")
			{
				$sQuery = "SELECT COUNT(".$this->idColumn.")	FROM   $sTable $sJoin $sDefaultFilter $sGroupBy $sHavingClause";
				$this->lastSQL .= ";\n". $sQuery;
				$result2 = $db->getCol($sQuery);
	
				if(is_array($result2))
					$this->totalRecords = count($result2);
				else
					$this->totalRecords = 0;
	
			}
			else
			{
				$sQuery = "SELECT COUNT(".$this->idColumn.")	FROM   $sTable $sJoin $sDefaultFilter";
				$this->lastSQL .= ";\n". $sQuery;
				$this->totalRecords = $db->getOne($sQuery);
			}
		}
		else
		{
			$this->totalRecords = $this->totalFilteredRecords;
		}
	
	
		header('Content-type:application/json');
		echo $this->encodeJSON($result,$this->totalRecords,$this->totalFilteredRecords);
	
	}	
	
	
	/*
	 * This function accept two parameter $query and $gridcolumns
	 * $query is mysql select query without order by clause and with placeholder "{DG_FILTER}" after where condition
	 * eg. $query = "SELECT a.id,name,age FROM abc a JOIN xyz b ON a.id = b.id WHERE age>20 {DG_FILTER}"
	 * $gridColumns is array of columns name with alias which is passed in query
	 * eg. $gridColumns = array('a.id','name','age');
	 */	

	public function getDataByQuery($sQuery,$gridColumns)
	{
		$this->columns = $gridColumns; // Server side's col
		$aColumns = $gridColumns;		
		$dataTableKeys = $this->getDtColumnsName();
		$dtColumns = $this->getDtColumns() ; // client side's col
				
		/** Paging */
		
		$sLimit = "";
		if ( isset( $this->offset ) && $this->length != -1 )
		{
			$sLimit = "LIMIT ". $this->offset .", ". $this->length;
		}
	
		
		//ordering
		$sOrder = "";
		
		$order = $this->getVar('order');
		
		if(!empty($order))
		{
			$sOrder = "ORDER BY  ";
			$iSortingCols = $this->getVar('order');
		
			$iSortingCols = (empty($iSortingCols))?0:$iSortingCols;
				
		
			foreach ($iSortingCols as $key => $value)
			{
				if ($dtColumns[$value['column']]['orderable'] == "true" )
				{

					$orderField = $aColumns[$dataTableKeys[$value['column']]];				
		
					$sOrder .= $orderField." ". $value['dir'] .", ";
				}
			}
		
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		
		
		//filtering
		$sWhere = "";
		$dtSearchVal = $this->getVar('search');
		if ($dtSearchVal['value'] != '')
		{
			$pos = strripos($sQuery,'where');
			if($pos===FALSE)
			{
				$sWhere = "WHERE (";
			}
			else 
			{
				$sWhere .= " AND (";
			}
				
			foreach($dataTableKeys as $colKey)
			{
				if(!empty($colKey))
				{
					$filterField ='';
		
					if(!in_array($colKey, $this->disabledSearchFields))
					{
						$filterField = $aColumns[$colKey];
		
						$sWhere .= $filterField ." LIKE '%". $dtSearchVal['value'] ."%' OR ";
					}
		
				}
			}			
			
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		
		foreach ($dtColumns as $key => $dtColData)
		{
			if($dtColData['searchable'] == true && $dtColData['search']['value'] != '')
			{
				$pos = strripos($sQuery,'where');
				if($pos===FALSE &&  $sWhere == "")
				{
					$sWhere = "WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				$filterField = $aColumns[$dtColData['name']];
				
				$sWhere .= $filterField." LIKE '%". $dtColData['search']['value']."%' ";
			}
		}
		
		
		
		$sQuery = str_replace('{DG_FILTER}',$sWhere,$sQuery); //replace placeholder with where condition
		$sQuery = "$sQuery $sOrder $sLimit";
		

		
		$this->lastSQL = $sQuery;
		
		$db =  $this->getDBAdapter();
		$result = $db->getAll($sQuery,DB_FETCHMODE_ASSOC);
		
		
		$sQuery = "SELECT FOUND_ROWS()";
		$this->totalFilteredRecords = $db->getOne($sQuery);
		
		$this->totalRecords = $this->totalFilteredRecords;

		header('Content-type:application/json');		
		echo $this->encodeJSON($result,$this->totalRecords,$this->totalFilteredRecords);
	}
	


	public function encodeJSON($data,$totalRecords, $totalFilteredRecords)
	{	
		
		
		$aColumns = $this->columns;	
		$output = array(
			"draw" => $this->getVar('draw'),
			"recordsTotal" => $totalRecords,
			"recordsFiltered" => $totalFilteredRecords,
			"data" => array()
		);		
		
	    foreach($data as $aRow)		
		{
			
			$row = array();								
			$row = $aRow;	 				
			
			if(isset($this->rowProcessor))
			{
				$row = call_user_func($this->rowProcessor, $row);
			}
						
			$output['data'][] = $row;			
		
		}
		return json_encode( $output );
	}

    public function getLastQuery()
    {
        return $this->lastSQL;
    }
}
?>