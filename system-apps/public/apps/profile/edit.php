<?php
/**
 * Profile Listing Page
 * 
 */
require_once(dirname(__FILE__)."/ProfileApp.php");
require_once( 'ringside/api/clients/RingsideApiClients.php');
$ringside = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
$ringside->setLocalClient( true );
$user = $ringside->require_login();
if ( $user == null) { 
   return;
}

// Get reference to our app, model and caclulated fields for display
$app= new ProfileApp();
$updateOccured=$app->saveEditChanges($_REQUEST);
$user=$app->getUserData();
$formDataArry=$app->getEditFormData();
//print("form data ".$user->userbasicprofile->political."zz<p>\n");
//var_dump($formDataArry);
$page=1;
?>

<h1>Edit Profile</h1>

<?php include(dirname(__FILE__)."/menu.inc"); ?>
<?php if($updateOccured){ ?>
<fb:success><fb:message><strong>Success!</strong>&nbsp;&nbsp;  Your changes have been saved. </fb:message></fb:success>
<?php } ?>

<div class="profile-editor-form">
<form id="form1" name="form1" method="post" action="">
  <table border="0" cellspacing="5" cellpadding="0">
  <tr>
    <td width="40%"><div align="right">Sex:</div></td>
    <td><select class="select" name="sex" id="sex"  >
      <?php $app->printNumericOption(0,2,$formDataArry['sex'],array('Select Sex:','Female','Male')); ?>
    </select></td>
  </tr>
  <tr>
    <td width="40%"><div align="right"><span style="line-height: 70%">Birthday:</span></div></td>
    <td><select name="birthday_month" id="birthday_month" autocomplete="off">
      <?php $app->printNumericOption(0,12,$formDataArry['birthMonth'],array('Month:','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec')); ?>
    </select>
      <select name="birthday_day" id="birthday_day"  autocomplete="off">
        <?php $app->printNumericOption(1,31,$formDataArry['birthDayOfMonth']); ?>
      </select>
      <select name="birthday_year" id="birthday_year"  autocomplete="off">
        <?php $app->printNumericOption(1909,2009,$formDataArry['birthYear'],array('Year:',
			'1910','1911','1912','1913','1914','1915','1916','1917','1918','1919',
			'1920','1921','1922','1923','1924','1925','1926','1927','1928','1929',
			'1930','1931','1932','1933','1934','1935','1936','1937','1938','1939',
			'1940','1941','1942','1943','1944','1945','1946','1947','1948','1949',
			'1950','1951','1952','1953','1954','1955','1956','1957','1958','1959',
			'1960','1961','1962','1963','1964','1965','1966','1967','1968','1969',
			'1970','1971','1972','1973','1974','1975','1976','1977','1978','1979',
			'1980','1981','1982','1983','1984','1985','1986','1987','1988','1989',
			'1990','1991','1992','1993','1994','1995','1996','1997','1998','1999',
			'2000','2001','2002','2003','2004','2005','2006','2007','2008','2009')); ?>
      </select></td>
  </tr>
  <tr>
    <td width="40%"><div align="right"><span style="">Political Views:</span></div></td>
    <td><select name="political" id="political">
      <option value="Very Liberal" <?php if($user->userbasicprofile->political=="Very Liberal"){ print("selected=\"true\"");} ?> >Very Liberal</option>
      <option value="Liberal" <?php if($user->userbasicprofile->political=="Liberal"){ print("selected=\"true\"");} ?>>Liberal</option>
      <option value="Moderate" <?php if($user->userbasicprofile->political=="Moderate"){ print("selected=\"true\"");} ?>>Moderate</option>
      <option value="Conservative" <?php if($user->userbasicprofile->political=="Conservative"){ print("selected=\"true\"");} ?> >Conservative</option>
      <option value="Very Conservative" <?php if($user->userbasicprofile->political=="Very Conservative"){ print("selected=\"true\"");} ?>>Very Conservative</option>
      <option value="Other" <?php if($user->userbasicprofile->political=="Other"){ print("selected=\"true\"");} ?>>Other</option>
    </select></td>
  </tr>
  <tr>
    <td width="40%"><div align="right"></div></td>
    <td><select name="dob_privilage" id="dob_privilage">
      <?php $app->printNumericOption(1,3,$user->userbasicprofile->dob_privilage,array('Show my full birthday in my profile.','Show only month & day in my profile.','Don\'t show my birthday in my profile.')); ?>
    </select>
    <input name="hometown" type="hidden" id="hometown" size="40" value="<?php print( $user->userbasicprofile->hometown) ?>"/></td>
  </tr>
  <tr>
    <td width="40%"><div align="right"><span style="">Religious Views: </span></div></td>
    <td><input name="religion" type="text" id="religious_views" size="40" value="<?php print( $user->userbasicprofile->religion) ?>"/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
    	<br />
    	<input name="action" type="submit" id="action" value="Save Changes" class="btn-input" />&nbsp;
    	<input name="action" type="submit" id="action" value="Cancel" class="btn-input" />
    </td>
  </tr>
</table>
</form>
</div>


