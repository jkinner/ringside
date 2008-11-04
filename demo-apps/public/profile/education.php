<?php
/**
 * Profile Listing Page
 * 
 */
require_once(dirname(__FILE__)."/ProfileApp.php");

// Get reference to our app, model and caclulated fields for display
$app= new ProfileApp();
$updateOccured=$app->saveEducationChanges($_REQUEST);
$user=$app->getUserData();
//$formDataArry=$app->getEducationFormData();
$page=5;
?>


<h1>Edit Profile</h1>

<?php include(dirname(__FILE__)."/menu.inc"); ?>
<?php if($updateOccured){ ?>
<fb:success><fb:message><strong>Success!</strong>&nbsp;&nbsp;  Your changes have been saved. </fb:message></fb:success>
<?php } ?>

<div class="profile-editor-form">
  <form id="form1" name="form1" method="post" action="">
    <div id="education_college_1" class="school">

      <table class="editor education" border="0" cellspacing="0">

<?php for($index=1;$index<4;$index++){
	
		$schoolProfile=$app->extractObject($user->userprofileschool,$index-1);
		if(!is_null($schoolProfile)){
			$school=new School();
			if(!is_null($schoolProfile->school_id)){
				$school=$school->find_first("id=".$schoolProfile->school_id);
			}
			$schoolType=$school->school_type;
		} else {
			$schoolType="College";
		}
 ?> 
			  <tr>
                <td><div align="left">School:</div></td>
			    <td><div style="float: left; width: 215px">
			      <input name="school_name<?php print($index);?>" id="school_name<?php print($index);?>" value="<?php print($app->extractValue($user->userprofileschool,$index-1,'school_name','')); ?>" size="25" maxlength="100" autocomplete="off" />
                  </div>
			        <select name="grad_year<?php print($index);?>" id="grad_year<?php print($index);?>">
			        <?php $app->printNumericOption(1909,2019,$app->extractValue($user->userprofileschool,$index-1,'grad_year','1909'),array(
			'Class Year:',
			'1910','1911','1912','1913','1914','1915','1916','1917','1918','1919',
			'1920','1921','1922','1923','1924','1925','1926','1927','1928','1929',
			'1930','1931','1932','1933','1934','1935','1936','1937','1938','1939',
			'1940','1941','1942','1943','1944','1945','1946','1947','1948','1949',
			'1950','1951','1952','1953','1954','1955','1956','1957','1958','1959',
			'1960','1961','1962','1963','1964','1965','1966','1967','1968','1969',
			'1970','1971','1972','1973','1974','1975','1976','1977','1978','1979',
			'1980','1981','1982','1983','1984','1985','1986','1987','1988','1989',
			'1990','1991','1992','1993','1994','1995','1996','1997','1998','1999',
			'2000','2001','2002','2003','2004','2005','2006','2007','2008','2009',
			'2010','2011','2012','2013','2014','2015','2016','2017','2018','2019')); ?>
                  </select>                
                  </td>
		      </tr>
			  <tr>
                <td><div align="left"></div></td>
			    <td><select name="attended_for<?php print($index);?>" id="attended_for<?php print($index);?>" >
                    <option <?php if($schoolType=='College'){ ?>selected="selected"<?php } ?>>College</option>
                    <option <?php if($schoolType=='Grade School'){ ?>selected="selected"<?php } ?>>Grade School</option>
                    <option <?php if($schoolType=='High School'){ ?>selected="selected"<?php } ?>>High School</option>
                    <option <?php if($schoolType=='Technical School'){ ?>selected="selected"<?php } ?>>Technical School</option>
                </select></td>
		      </tr>
			  <tr>
				  <td valign="top" align="left">Concentration:</td>
				  <td><input type="text" class="inputtext" size="25" autocomplete="off" maxlength="100" id="concentrations<?php print($index);?>" name="concentrations<?php print($index);?>" value="<?php print($app->extractValue($user->userprofileschool,$index-1,'concentrations','')); ?>" onblur="" /><br><br><br></td>
			  </tr>
			  <?php } ?>
			
			<tr>
    <td>&nbsp;</td>
    <td>
    	<input name="action" type="submit" id="action" value="Save Changes" class="btn-input" />
    	<input name="action" type="submit" id="action" value="Cancel" class="btn-input" />
    </td>
  </tr>
		</table>
  </form>
</div>
