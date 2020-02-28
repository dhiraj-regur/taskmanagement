<?php
function order_ccf($order_set, $field) {
	if(isset($order_set['field']) && $order_set['field']==$field) echo 'class="current"';
}
?>