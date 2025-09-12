<?php
function isSimutransRunning(){
	global $SIMUTRANS_BIN;
	$process_check_command='ps aux | grep "'.$SIMUTRANS_BIN.' -server" | grep -v grep';
	exec($process_check_command,$process_check_output);	
	if(count($process_check_output)==0){
		return false;
	}else{
		return true;
	}
}
?>