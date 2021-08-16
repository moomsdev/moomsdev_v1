<?php
//Template name: Giới thiệu
/**
 * App Layout: layouts/app.php
 *
 * This is the template that is used for displaying 404 errors.
 *
 * @package WPEmergeTheme
 */
?>

    <section class="about_us pt-4 pt-md-5">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-6 __content">
                    <span data-aos="fade-right" data-aos-duration="1500" data-aos-delay="200"></span>
                    <div class="__inner" data-aos="fade-up" data-aos-anchor-placement="top-bottom"  data-aos-duration="1500">
                        <div class="--title_hda"><?php theOption('about_us__title'); ?></div>
                        <div class="--desc_hda"><?php theOption('about_us__desc'); ?></div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 __media">
                    <span class="_lt" data-aos="fade-down" data-aos-duration="2000" data-aos-delay="500"></span>
                    <span class="_rb" data-aos="fade-left" data-aos-duration="2000" data-aos-delay="300"></span>

                    <div class="__inner" data-aos="fade-up" data-aos-anchor-placement="top-bottom"  data-aos-duration="3000">
                        <iframe class="_iframe" width="572px" height="322px" src="<?php echo getYoutubeEmbedUrl(getOption('about_us__link'))  ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
// get_template_part('views/1__about_us');
get_template_part('views/1__1__about_us');
get_template_part('views/1__2__teacher');
get_template_part('views/7__register');

