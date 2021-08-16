<?php
//Template name: Liên hệ
/**
 * App Layout: layouts/app.php
 *
 * This is the template that is used for displaying 404 errors.
 *
 * @package WPEmergeTheme
 */
?>
<section class="contact">
    <div class="container">
        <div class="__section_title _show">
            <div class="__inner" data-aos="fade-up" data-aos-duration="1500">
                <span class="rb_vuong" data-aos="zoom-in" data-aos-duration="1000" data-aos-offset="500" ></span>
                <?php theTitle(); ?>
            </div>
        </div>

        <?php
        $contacts = getOption('address__complex');
        foreach ( $contacts as $contact){
        ?>
        <div class="__contact" data-aos="fade-up" data-aos-duration="1500">
            <div class="row">
                <div class="col-12 col-lg-5 _infor">
                    <div class="--city"><i class="fas fa-map-marker-alt"></i><?php echo $contact['__branch_city'] ?>   </div>
                    <div class="--hotline">HOTLINE: <?php echo $contact['__hotline_branch'] ?></div>
                    <div class="--branch"> <?php echo apply_filters('the_content',$contact['__address_branch'])  ?> </div>
                </div>
                <div class="col-12 col-lg-7 _map">
                    <?php echo $contact['__map_branch'] ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</section>
<?php
get_template_part('views/7__register');


