<?php namespace Klubitus\Gallery;

use Backend;
use System\Classes\PluginBase;

/**
 * Gallery Plugin Information File
 */
class Plugin extends PluginBase {

    public $require = [
        'Klubitus.Comment',
        'RainLab.User',
    ];


    /**
     * Returns information about this plugin.
     *
     * @return  array
     */
    public function pluginDetails() {
        return [
            'name'        => 'Klubitus Gallery',
            'description' => 'Image galleries for Klubitus.',
            'author'      => 'Antti Qvickström',
            'icon'        => 'icon-camera-retro',
            'homepage'    => 'https://github.com/anqqa/oc-gallery-plugin',
        ];
    }


    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return  array
     */
    public function registerComponents() {
        return [
            'Klubitus\Gallery\Components\Flyer'       => 'galleryFlyer',
            'Klubitus\Gallery\Components\Flyers'      => 'galleryFlyers',
            'Klubitus\Gallery\Components\FlyerMonths' => 'galleryFlyerPerMonth',
        ];
    }


    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return  array
     */
    public function registerNavigation() {
        return [
            'calendar' => [
                'label'       => 'Gallery',
                'url'         => Backend::url('klubitus/gallery/migrate'),
                'icon'        => 'icon-camera-retro',
                'permissions' => ['klubitus.gallery.*'],
                'order'       => 110,
            ],
        ];
    }


    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'klubitus.gallery.some_permission' => [
                'tab' => 'Gallery',
                'label' => 'Some permission'
            ],
        ];
    }


    public function boot() {

    }

}
