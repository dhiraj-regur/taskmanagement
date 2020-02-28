<?php 
/*śćźżąęółć*/
/* nieskończona */
class Isd_Pagination_Full extends Isd_Pagination_Simple {
	public $table_name;
	public $fields;
	public $mode;
	public $question_params;
	
	function __construct ($items_per_page, $table_name, $fields, $mode = ' ', $question_params=array()) {
		parent::__contruct($items_per_page);
		$this->table_name = $table_name;
		$this->fields = $fields;
		$this->mode = $mode;
		$this->question_params = $question_params;
		$this->pages_number = $this->getPagesNumber();
	}
}
?>