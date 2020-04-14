<?php if(isset($alerts)) : ?>
    <ul class="alerts">
    <?php foreach($alerts AS $alert) : ?>
        <li class="alert"><?php echo $alert; ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>