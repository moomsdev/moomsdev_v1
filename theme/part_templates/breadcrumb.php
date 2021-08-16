<?php

if (function_exists('rank_math_the_breadcrumbs') && get_option('_use_rank_math_breadcrumb') === 'yes') {
    rank_math_the_breadcrumbs();
} else {
	$title = getPageTitle(); ?>
	<div class="page-breadcrumb">
		<nav aria-label="breadcrumb">
			<h1><?php echo $title ?></h1>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?php bloginfo('url') ?>"><?php echo __('Trang chủ', 'mooms') ?></a></li>
				<li class="breadcrumb-item active" aria-current="page"><?php echo $title ?></li>
			</ol>
		</nav>
	</div>
<?php }
