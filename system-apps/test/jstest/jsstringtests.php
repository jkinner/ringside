
<script type="text/javascript">

	function showVar1()
	{
		alert('var1=' + var1);
	}
	
	function showVar2()
	{
		alert('var2=' + var2);
	}
	
</script>

<fb:js-string name="var1">Body Text</fb:js-string>

<fb:js-string name="var2">hello <fb:name uid="100000" />!</fb:js-string>

<input type="button" value="var1" onclick="showVar1()" />
<input type="button" value="var2" onclick="showVar2()" />

