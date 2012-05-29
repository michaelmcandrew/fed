<?php

function joomla_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
	if($objectName!='Membership' && $objectName!='Organization') {
		return;
	}
	
	if($objectName=='Organization') {
		update_current_member_data_field($objectId);
		return;
	}
	

	$query="SELECT contact_id FROM `civicrm_membership` WHERE id ={$objectId}";
	$result = CRM_Core_DAO::executeQuery( $query );
	$result->fetch();
	if($result->N){
		update_current_member_data_field($result->contact_id);
	}
}


function update_current_member_data_field($contactId) {
 	require_once('api/v2/MembershipContact.php');
	$params = array(
		'contact_id' => $contactId
	);

	$membership = civicrm_membership_contact_get($params);
	$membership=current($membership[$contactId]);
	$msi=$membership['status_id'];
	$mti=$membership['membership_type_id'];
	if (
		$msi==1 OR 
		$msi==2 OR
		$msi==3
	) {
		$current = 1;
	} else {
		$current = 0;
	}
	if ($mti==6){
		$current = 0;
	} 
	$mti=$membership['membership_type_id'];

	$query="REPLACE INTO civicrm_value_current_member_10 SET `entity_id`={$contactId}, current_member_87={$current}";
	$updateResult = CRM_Core_DAO::executeQuery( $query, $params );		
}



function batch_update_current_member_data_field(){
	$query="SELECT contact_id FROM `civicrm_membership`";
  $result = CRM_Core_DAO::executeQuery( $query );
	while($result->fetch()){
		update_current_member_data_field($result->contact_id);
	}
	
}

// Uncomment line below to batch update all contacts on next Joomla access
$number=rand(1,300);
if($number==7){
  batch_update_current_member_data_field();
}
?>
