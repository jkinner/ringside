<?php
include_once('header.inc');
$apiKey = Config::$api_key;
$qstring = "?apikey=$apiKey";
?>

<br />
<rs:if-has-paid plan="subscriber">

		<p>NASHVILLE, Tenn. -- The Tennessee Titans will be holding a minicamp without defensive tackle Albert Haynesworth.</p>
		
		<p>The Titans are due to hit the field Thursday and Friday as part of their on-field training, but they will not have 
		arguably their best defensive player with them.</p>
		
		<p>"Albert is continuing to work out on his own," agent Chad Speck said in an e-mail.</p>
		
		<p>The Titans tagged Haynesworth as their franchise player in February to keep him from leaving as an unrestricted free
		agent. Tennessee must reach a long-term contract deal with Haynesworth by July 15. If that doesn't happen, the team can
		only sign him to a one-year deal worth $7.8 million.</p>
		
		<p>Haynesworth said last month negotiations had been going very slow. But that didn't stop him from making a couple 
		stops in the team's annual fan caravan around the state.</p>
		
		<p>Copyright 2008 by The Associated Press</p>
	
	<fb:else>
	    <a href="../payments/<?php echo $qstring; ?>">Sign up</a> for full access to all Sports News Stories!
	</fb:else>
	
</rs:if-has-paid>
