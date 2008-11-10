
<h2>Get your widget code</h2>
<select id='selNetworks' onchange="generateWidgetCode()" >
  <option value="-1">Please Select a Network</option>
  <?php echo $networkOptions; ?>
</select>
<br/>
<br/>
<textarea id="taCode" style="width: 400px; height: 100px" readonly="true"> Please Select A Network </textarea>
<div style="text-align:right;width:400px" ><span style="cursor:pointer;" onclick="document.getElementById('taCode').select()">Select All</span></div>


<script type="text/javascript">

  var appId = "footprints";

  function generateWidgetCode(){
  
    var sel = document.getElementById("selNetworks");
    var val = sel.options[sel.selectedIndex].value;
  
    var scr = "";
    if(val==-1) {
      scr = " Please Select A Network ";
    } else {
      scr = "<scr" + "ipt type='text/javascript' src='<?php echo $socialWidgetUrl ?>?method=app&app=footprints&nid=" + val + "'></scr" + "ipt>";    
    }
    
    document.getElementById("taCode").value = scr;
    
  }
</script>

