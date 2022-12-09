<?php $this->startSection('body'); ?>

    <h2>This is the sub layout</h2>

    <?php echo $this->sections['content'] ?? ""; ?>
    
<?php $this->endSection(); ?><?php $this->extends('base'); ?><?php /** __template_path__:  */ ?>