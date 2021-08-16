<?php
namespace App\PostTypes;
use Carbon_Fields\Container\Container;
use Carbon_Fields\Field;
use mooms\Abstracts\AbstractPostType;

class Service extends \App\Abstracts\AbstractPostType {

    public function __construct()
    {
        $this->showThumbnailOnList = true;
        $this->supports            = [
            'title',
            'editor',
            'thumbnail',
        ];
        $this->menuIcon            = 'dashicons-megaphone'; //change icon in website: https://developer.wordpress.org/resource/dashicons/
        $this->post_type           = 'service'; //Name Posttype
        $this->singularName        = $this->pluralName = __('Services', 'mooms'); // show name in admin: Bài viết, sản phẩm, ...
        $this->titlePlaceHolder    = __('Post', 'mooms');
        $this->slug                = 'services'; //slug posttype: bai-viet, san-pham, ...
        parent::__construct();
    }

    /**
     * Document: https://docs.carbonfields.net/#/containers/post-meta
     */
    public function metaFields()
    {

    }
}
