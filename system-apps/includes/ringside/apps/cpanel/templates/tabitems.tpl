<fb:tabs>
    <fb:tab-item href='index.php?show=ringsideinfo'
                 title='Home'
                 selected='<?php echo empty($GLOBALS['homeSelected']) ? "false" : "true"; ?>' />
    <fb:tab-item href='inventory.php'
                 title='Inventory'
                 selected='<?php echo empty($GLOBALS['inventorySelected']) ? "false" : "true"; ?>' />
    <fb:tab-item href='config.php'
                 title='Configuration'
                 selected='<?php echo empty($GLOBALS['configSelected']) ? "false" : "true"; ?>' />
    <fb:tab-item href='metrics.php'
                 title='Metrics'
                 selected='<?php echo empty($GLOBALS['metricsSelected']) ? "false" : "true"; ?>' />
</fb:tabs>
