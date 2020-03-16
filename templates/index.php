<?php
script('elb_cal_types', 'vue');
style('elb_cal_types', 'style');
?>

<div id="app-ectypes">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('navigation/index')); ?>
        <?php print_unescaped($this->inc('settings/index')); ?>
    </div>

    <div id="app-content">
        <div id="app-content-wrapper">
            <?php print_unescaped($this->inc('content/index')); ?>
        </div>
    </div>
</div>

