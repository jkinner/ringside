<?php

include_once('config.php');


require_once("ProfileApp.php");



$uid = $ringside->get_loggedin_user();
$readonly=false;

if(array_key_exists('id', $_REQUEST)){
	$uid =  $_REQUEST['id'];
	$ringside->user=$uid;
	$readonly=true;
}

$flavor = $ringside->getFlavor();
$app= new ProfileApp($uid,$readonly,$ringside->api_client);
$app->saveStatusChanges($_REQUEST);
?> 
<script language="JavaScript" type="text/javascript">
  function toggle(obj) {
	var el = document.getElementById(obj);
	if ( el.style.display != 'none' ) {
		el.style.display = 'none';
	}
	else {
		el.style.display = '';
	}
  }
  function clearFieldsidebar(field){
  	document.forms["statusFormsidebar"].textfieldStatus.value="";
  }
  function clearFieldwide(field){
  	document.forms["statusFormwide"].textfieldStatus.value="";
  }
  function toggleEditwide(){
		toggle("status_captionwide");
		toggle("statusFormwide");
 }

  function toggleEditsidebar(){  		
		toggle("status_captionsidebar");
		toggle("statusFormsidebar");
 }
</script>
<?php  
if($flavor=='sidebar'){
	
		print("<div class='content'>\n");
		print("<b><fb:name useyou='false' uid='$uid'  /><br></b>");
		$status_block=$app->getStatusBlock("sidebar");
		print join( $status_block );
		print("</div>");
		
		print("<div class='content'>\n" .
		"		<fb:profile-pic uid='$uid' /><br />\n");
		print("</div>");
		
?>
	
<?php } else { ?>

<div id="profilemastercolumn" class="profile-master-column">
	<div id="profilenarrowcolumn" class="profile-cols">
		<?php 
			$app->printNarrow();
		?>
	</div><!-- END profilenarrowcolumn -->
	<div id="profilewidecolumn" class="profile-cols">
		<?php 
			$app->printWide();
		?>
<fb:comments xid="<?php echo $uid; ?>" showform="true" canpost="true" candelete="true" />
	</div><!-- END profilewidecolumn -->
</div><!-- END profilemastercolumn -->
<?php } ?>