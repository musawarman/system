<?php foreach ($vendor_notes as $vendor_note) : ?>
    <div class="panel panel-default small">
        <div class="panel-body">
            <?php echo nl2br(htmlsc($vendor_note->vendor_note)); ?>
        </div>
        <div class="panel-footer text-muted">
            <?php echo date_from_mysql($vendor_note->vendor_note_date, true); ?>
        </div>
    </div>
<?php endforeach; ?>
