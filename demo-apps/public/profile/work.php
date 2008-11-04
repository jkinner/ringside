<?php
/**
 * Profile Listing Page
 * 
 */
require_once(dirname(__FILE__)."/ProfileApp.php");

// Get reference to our app, model and caclulated fields for display
$app= new ProfileApp();
$updateOccured=$app->saveWorkChanges($_REQUEST);
$user=$app->getUserData();
//$formDataArry=$app->getWFormData();
$page=6;
?>


<h1>Edit Profile</h1>

<?php include(dirname(__FILE__)."/menu.inc"); ?>
<?php if($updateOccured){ ?>
<fb:success><fb:message><strong>Success!</strong>&nbsp;&nbsp;  Your changes have been saved. </fb:message></fb:success>
<?php } ?>

<div class="profile-editor-form">
  <form id="form1" name="form1" method="post" action="">

		<div id="work_history_1" class="work_position">
		
			<table class="editor" border="0" cellspacing="0">


<?php for($index=1;$index<3;$index++){
		$work=$schoolProfile=$app->extractObject($user->userprofilework,$index-1);	
		
		$startDateParts=date_parse($work->start_date );
		$startDayOfMonth=0;
		$startMonth=0;
		$startYear=1949;
		if(array_key_exists('day',$startDateParts))  {$startDayOfMonth=$startDateParts['day'];}
		if(array_key_exists('month',$startDateParts))  {$startMonth=$startDateParts['month'];}
		if(array_key_exists('year',$startDateParts))  {$startYear=$startDateParts['year'];}
		

		$endDateParts=date_parse($work->end_date );
		$endDayOfMonth=0;
		$endMonth=0;
		$endYear=1949;
		if(array_key_exists('day',$endDateParts))  {$endDayOfMonth=$endDateParts['day'];}
		if(array_key_exists('month',$endDateParts))  {$endMonth=$endDateParts['month'];}
		if(array_key_exists('year',$endDateParts))  {$endYear=$endDateParts['year'];}

 ?> 
			<tr class="company_name work_history_1">
				<td class="label">Employer:</td>
	
				<td>
					<div >
					  <input name="employer<?php print($index); ?>" class="inputtext" id="employer<?php print($index); ?>" value="<?php print($work->employer); ?>" >
				  </div>
				</td>
			</tr>
	
			<tr class="text work_history_1_position">
				<td class="label">Position:</td>
	
				<td><input type="text" id="position<?php print($index); ?>" name="position<?php print($index); ?>" class="inputtext" value="<?php print($work->position); ?>"></td>
			</tr>
	
			<tr class="textarea work_history_1_description tallrow">
				<td class="label">Description:</td>
	
				<td>
				<textarea cols="30" rows="2" id="description<?php print($index); ?>" name="description<?php print($index); ?>">
				<?php print($work->description); ?>
				</textarea></td>
			</tr>
	
			<tr class="text city_selector tallrow">
				<td class="label">City/Town:</td>
	
				<td><input id="city<?php print($index); ?>" name="city<?php print($index); ?>" value="<?php print($work->city); ?>" class="inputtext" maxlength="100" size="25" autocomplete="off"></td>
			</tr>
	
			<tr class="checkbox work_history_1_workspan_current tallrow">
				<td class="label">Time Period:</td>
	
				<td>
					<table class="option_field" border="0" cellspacing="0">
						<tr>
							<td><label for="work_history_1_workspan_current">I currently work here.</label>&nbsp;
							<input name="current<?php print($index); ?>" type="checkbox" id="current<?php print($index); ?>" value="true" <?php if($work->current=='true') { print("checked='true'");} ?> ></td>
							<td></td>
						</tr>
					</table>
				</td>
			</tr>
	
			<tr class="workspan work_history_1_workspan">
				<td class="label">From:</td>
	
				<td>
				  <div id="start_month<?php print($index); ?>">
						<select name="start_month<?php print($index); ?>" id="start_month<?php print($index); ?>" autocomplete="off">
	          				<?php $app->printNumericOption(0,12,$startMonth,array('Month:','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec')); ?>
					</select> 
						<select name="start_year<?php print($index); ?>" id="start_year<?php print($index); ?>" autocomplete="off">
	         <?php $app->printNumericOption(1949,2009,$startYear,array('Year:',
		'1950','1951','1952','1953','1954','1955','1956','1957','1958','1959',
		'1960','1961','1962','1963','1964','1965','1966','1967','1968','1969',
		'1970','1971','1972','1973','1974','1975','1976','1977','1978','1979',
		'1980','1981','1982','1983','1984','1985','1986','1987','1988','1989',
		'1990','1991','1992','1993','1994','1995','1996','1997','1998','1999',
		'2000','2001','2002','2003','2004','2005','2006','2007','2008','2009')); ?>
					</select>
					
					<span id="work_history_1_workspan_endspan" >
				<span class="inline_text">to</span><select name="end_month<?php print($index); ?>" id="work_history_1_workspan_end_month" autocomplete="off">
	      				<?php $app->printNumericOption(0,12,$endMonth,array('Month:','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec')); ?>
							</select>
					<select name="end_year<?php print($index); ?>" id="end_year<?php print($index); ?>" autocomplete="off">
	         <?php $app->printNumericOption(1949,2009,$endYear,array('Year:',
		'1950','1951','1952','1953','1954','1955','1956','1957','1958','1959',
		'1960','1961','1962','1963','1964','1965','1966','1967','1968','1969',
		'1970','1971','1972','1973','1974','1975','1976','1977','1978','1979',
		'1980','1981','1982','1983','1984','1985','1986','1987','1988','1989',
		'1990','1991','1992','1993','1994','1995','1996','1997','1998','1999',
		'2000','2001','2002','2003','2004','2005','2006','2007','2008','2009')); ?>
					</select></span></div>
				</td>
			</tr>
	<?php } ?>
		<tr>
    <td>&nbsp;</td>
    <td>
    	<br />
    	<input name="action" type="submit" id="action" value="Save Changes" class="btn-input" />
    	<input name="action" type="submit" id="action" value="Cancel" class="btn-input" />
    </td>
  </tr>
		</table>
	</form>
</div>
