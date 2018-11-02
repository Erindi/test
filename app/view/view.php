<?php if (!empty($emails) && isset($model)): ?>
    <?php echo $model->makeEmailsList($emails); ?>
<?php else: ?>
    <?php echo '<b>Sorry, but no emails was found.</b>'; ?>
<?php endif; ?>