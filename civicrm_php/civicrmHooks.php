<?php

define('CURRENT_MEMBER_DATA_TABLE', 'civicrm_value_current_member_9');
define('CURRENT_MEMBER_DATA_FIELD', 'current_member_81');

function joomla_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
	if($op!='create' && $op!='edit'){
		return;
	}
	if($objectName=='Membership'){
		$query="SELECT contact_id FROM `civicrm_membership` WHERE id ={$objectId}";
		$result = CRM_Core_DAO::executeQuery( $query );
		$result->fetch();
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
	if (
		$msi==1 OR 
		$msi==2 OR
		$msi==3
	) {
		$current = 1;
	} else {
		$current = 0;
	}
	$query="REPLACE INTO {CURRENT_MEMBER_DATA_TABLE} SET `entity_id`={$contactId}, `{CURRENT_MEMBER_DATA_FIELD}`={$current}";
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
//batch_update_current_member_data();

?>