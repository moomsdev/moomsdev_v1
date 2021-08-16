<?php
namespace App\PostTypes;
use Carbon_Fields\Container\Container;
use Carbon_Fields\Field;
use mooms\Abstracts\AbstractPostType;

class Work extends \App\Abstracts\AbstractPostType {

    public function __construct()
    {
        $this->showThumbnailOnList = true;
        $this->supports            = [
            'title',
            'thumbnail',
            'editor',
        ];
        $this->menuIcon            = 'dashicons-wordpress-alt'; //change icon in website: https://developer.wordpress.org/resource/dashicons/
        $this->post_type           = 'work'; //Name Posttype
        $this->singularName        = $this->pluralName = __('Work', 'mooms');
        $this->titlePlaceHolder    = __('Post', 'mooms');
        $this->slug                = 'works';
        parent::__construct();
    }

    /**
     * Document: https://docs.carbonfields.net/#/containers/post-meta
     */
    public function metaFields()
    {
        Container::make('post_meta', __('Chọn hình ảnh', 'mooms'))
                 ->set_context('carbon_fields_after_title')
                 ->set_priority('high')
                 ->where('post_type', 'IN', [$this->post_type])
                 ->add_fields([
                     Field::make('media_gallery', 'image' , __('', 'mooms')),
                 ]);


    }
}
