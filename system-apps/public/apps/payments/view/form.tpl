<fb:editor action="index.php" labelwidth="150">
    <fb:editor-custom>
        <input type="hidden" name="action" value="invite" />
        <input type="hidden" name="aid" value="<?php echo $aid; ?>" />
        <input type="hidden" name="originalLocation" value="<?php echo $originalLocation; ?>" />
    </fb:editor-custom>
    <fb:editor-text label="*First Name" name="firstname" value="<?php echo $firstName; ?>"/>
    <fb:editor-text label="*Last Name" name="lastname" value="<?php echo $lastName; ?>"/>
    <fb:editor-custom label="*Credit Card Type">
        <table border="0" cellspacing="1" cellpadding="0" id="pricing-plans">
            <tr>
                <td><input type="radio" name="cctype" value="100" checked="true" /></td>
                <td>Visa</td>
            </tr>
            <tr>
                <td><input type="radio" name="cctype" value="101" /></td>
                <td>Mastercard</td>
            </tr>
            <tr>
                <td><input type="radio" name="cctype" value="102" /></td>
                <td>Amex</td>
            </tr>
            <tr>
                <td><input type="radio" name="cctype" value="103" /></td>
                <td>Discover</td>
            </tr>
        </table>
    </fb:editor-custom>
    <fb:editor-text label="*Credit Card Number" name="ccn" />
    <fb:editor-custom label="*Expiration Date">
        <select name="expmonth">
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select> 
        <select name="expyear">
            <option>2008</option>
            <option>2009</option>
            <option>2010</option>
            <option>2011</option>
            <option>2012</option>
            <option>2013</option>
            <option>2014</option>
            <option>2015</option>
        </select> 
    </fb:editor-custom>
    <fb:editor-text label="E-Mail" name="email" />
    <fb:editor-text label="Phone Number" name="phone" />
    <rs:payment-plans aid="<?php echo $aid; ?>" />
    <fb:editor-buttonset>
        <fb:editor-button value="Submit" class="btn-submit" />
    </fb:editor-buttonset>
</fb:editor>
