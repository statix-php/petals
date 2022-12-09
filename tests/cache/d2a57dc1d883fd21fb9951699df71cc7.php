<?php $this->startSection('content'); ?>

    <p>This is the actual final page</p>

    <?php if ($name === 'John' && str_contains($name, 'ohn')) { ?>
        <p>Hello {{ $name }}</p>
    <?php } ?>

    <?php if ($name === 'John') { ?>
        <p>Hello {{ $name }}</p>    
    <?php } elseif ($name === 'Doe') { ?>
        <p>Hola {{ $nickname }}</p>
    <?php } else { ?>
        <p>Hello Guest</p>
    <?php } ?>

    <?php echo $this->render('partial', array_merge($this->data, [
        'value' => '123',
    ])); ?>

    <?php foreach (range(1, 5) as $item) { ?>
        <p>{{ $item }}</p>
    <?php } ?>

    <?php foreach ([1, 2] as $number) { ?>
        <p>{{ $number }}</p>
    <?php } ?>

    <?php for ($i = 0; $i < 10; $i++) { ?>
        <p>{{ $i }}</p>
    <?php } ?>

    

    {{ $email }}

    {{ '<p class="bg-red-100">this is escaped</p>' }}

    <?php echo '<p class="bg-red-100">this is unescaped</p>'; ?>

    {{ -- this is a comment -- }}

    <div class="container">
        Hello, {{ name }}.
    </div>    

    
        <div class="container">
            Hello, {{ name }}.
        </div>

        <div class="container">
            Hello, {{ jim }}.
        </div>
    

<?php $this->endSection(); ?><?php $this->extends('layout'); ?><?php /** __template_path__:  */ ?>