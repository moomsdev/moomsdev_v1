<?php

/**
 * Thay đổi default order post của WP_Query
 */
add_action('pre_get_posts', static function ($query) {
    /**
     * @var \WP_Query $query
     */
    if ($query->is_main_query()) {
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
    }
});

/**
 * Bo sung nut "Tao du lieu mau" ben canh nut "Them moi"
 */
add_action('admin_head-edit.php', static function () {
    global $current_screen;
    $user = wp_get_current_user();
    if (in_array($user->user_login, SUPER_USER, true)) {
        ?>
		<script type="text/javascript">
            jQuery(document).ready(function ($) {
                jQuery(jQuery(".wrap .page-title-action")[0]).after('<a href="#TB_inline?&width=600&height=550&inlineId=modalCreateSampleData" class="page-title-action thickbox">Tạo dữ liệu mẫu</a>');
                jQuery('#formCreateSampleData').repeater({
                    initEmpty             : false,
                    defaultValues         : {
                        'text-input': 'foo'
                    },
                    show                  : function () {
                        jQuery(this).slideDown();
                    },
                    hide                  : function (deleteElement) {
                        if (confirm('Are you sure you want to delete this element?')) {
                            jQuery(this).slideUp(deleteElement);
                        }
                    },
                    ready                 : function (setIndexes) {
                        // $dragAndDrop.on('drop', setIndexes);
                    },
                    isFirstItemUndeletable: true
                });
            });
		</script>

		<div id="modalCreateSampleData" style="display:none;">
			<form action="" method="get" id="formCreateSampleData">
				<table cellspacing="0" cellpadding="5" style="width:100%">
					<tr>
						<td style="width:25%"><?php echo __('Số lượng', 'mooms') ?></td>
						<td style="width:75%"><input type="number" class="regular-text" name="gm_post_count" value="10"></td>
					</tr>
					<tr>
						<td><?php echo __('Meta value', 'mooms') ?></td>
						<td>
							<table cellpadding="5" cellspacing="0" data-repeater-list="group-a">
								<tr data-repeater-item>
									<td><input type="text" class="regular-text" name="gm_metakey" placeholder="Meta key" value=""></td>
									<td><input type="text" class="regular-text" name="gm_metavalue" placeholder="Meta value" value=""></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
                            <?php wp_nonce_field('tao_du_lieu_mau', '_token') ?>
							<input type="hidden" class="regular-text" id="gm_post_type" name="gm_post_type" value="<?php echo $current_screen->post_type ?>">
							<input type="button" id="submit_create_sample_data" class="button button-primary" value="<?php echo __('Tạo dữ liệu mẫu', 'mooms') ?>">
							<input data-repeater-create type="button" value="Add"/>
						</td>
					</tr>
				</table>
			</form>
		</div>
        <?php
    }
});
