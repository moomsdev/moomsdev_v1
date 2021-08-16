<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make(__('Demo Block', 'mooms'))
     ->add_fields([
         Field::make('text', 'heading', __('Block Heading')),
         Field::make('image', 'image', __('Block Image')),
         Field::make('rich_text', 'content', __('Block Content')),
     ])
     ->set_render_callback(static function ($fields, $attributes, $inner_blocks) {
         ?>

         <?php
     });
