<?php namespace Klubitus\Gallery;

use Backend;
use System\Classes\PluginBase;

/**
 * Gallery Plugin Information File
 */
class Plugin extends PluginBase {

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
            'Klubitus\Gallery\Components\Flyers' => 'galleryFlyers',
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

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'galleries' => [
                'label'       => 'Gallery',
                'url'         => Backend::url('klubitus/gallery/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['klubitus.gallery.*'],
                'order'       => 500,
            ],
        ];
    }


    public function boot() {

    }

}
