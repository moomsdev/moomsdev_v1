<?php

if (!defined('ABSPATH')) {
    exit;
}

class STC_Comment_Walker extends Walker_Comment {
    protected function html5_comment($comment, $depth, $args) {
        $tag = ('div' === $args['style']) ? 'div' : 'li'; ?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent media' : 'media'); ?>>

        <?php if(0 != $args['avatar_size']): ?>
            <div class="media-left">
                <a href="<?php echo get_comment_author_url(); ?>" class="media-object">
                    <?php echo get_avatar($comment, $args['avatar_size']); ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="media-body" id="div-comment-<?php comment_ID(); ?>">
            <?php printf('<h5 class="comment-author">%s</h5>', get_comment_author_link()); ?>
            <div class="comment comment-content mt-3 mb-3"><?php comment_text(); ?></div><!-- .comment-content -->
            <a href="javascript:;" class="comment-date"><?php printf(_x('%1$s at %2$s', '1: date, 2: time'), get_comment_date(), get_comment_time()); ?></a>
        </div>
    <?php }
}
