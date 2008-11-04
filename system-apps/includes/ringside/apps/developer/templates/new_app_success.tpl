<h1>Developer</h1>
<h2>Application  <?php echo $appName; ?> Successfully Created</h2>
<br />

<div class="rs-content-block">

	<br />
   <div style="font-style: italic; align: center;">Your application has been created with the following properties:</div>
   <br />
	<table border="0">
	<tr>
		<td><b>API Key</b></td>
		<td><?php echo $apiKey; ?>
	</tr>
	<tr>
		<td><b>Secret</b></td>
		<td><?php echo $secret; ?>
	</tr>
	</table>
	<br />
	<br />
	
	<center>
		<a href="edit_app.php?api_key=<?php echo $apiKey; ?>&form_action=edit" class="btn-nav">Edit Application Properties</a>
	</center>
    <br />
    <br />

</div>
