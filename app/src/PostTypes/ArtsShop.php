<?php
namespace App\PostTypes;
use Carbon_Fields\Container\Container;
use Carbon_Fields\Field;
use mooms\Abstracts\AbstractPostType;

class ArtsShop extends \App\Abstracts\AbstractPostType {

    public function __construct()
    {
        $this->showThumbnailOnList = true;
        $this->supports            = [
            'title',
        ];
        $this->menuIcon            = 'dashicons-businesswoman';
        $this->post_type           = 'artsshop';
        $this->singularName        = $this->pluralName = __('Arts Shop', 'mooms');
        $this->titlePlaceHolder    = __('Post', 'mooms');
        $this->slug                = 'arts-shop';
        parent::__construct();
    }

    /**
     * Document: https://docs.carbonfields.net/#/containers/post-meta
     */
    public function metaFields()
    {
        Container::make('post_meta', __('Thông tin học viên', 'mooms'))
                 ->set_context('carbon_fields_after_title')
                 ->set_priority('high')
                 ->where('post_type', 'IN', [$this->post_type])
                 ->add_fields([
                     Field::make('complex', 'student__complex'       . currentLanguage(), __('Nhập thông tin:', 'mooms'))
                          ->set_layout( 'tabbed-horizontal' )
                          ->add_fields([
                              Field::make('image',     '__img'    , __('Ảnh đại diện __ 215x215', 'mooms'))   ->set_width(45),
                              Field::make('text',      '__name'   , __('Tên (Bí danh)', 'mooms'))             ->set_width(45),
                              Field::make('rich_text', '__desc'   , __('Nội dung'     , 'mooms')),
                          ])->set_header_template('<% if (__name) { %><%- __name %><% } %>'),
                 ]);


    }
}
