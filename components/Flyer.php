<?php namespace Klubitus\Gallery\Components;

use Cms\Classes\ComponentBase;
use Klubitus\Calendar\Models\Flyer as FlyerModel;

class Flyer extends ComponentBase {

    /**
     * @var  FlyerModel
     */
    public $flyer;


    public function componentDetails() {
        return [
            'name'        => 'Flyer Component',
            'description' => 'Single flyer.'
        ];
    }


    public function defineProperties() {
        return [
            'id' => [
                'title'   => 'Flyer Id',
                'default' => '{{ :flyer_id }}',
                'type'    => 'string',
            ],
        ];
    }


    public function onRun() {
        $this->page['flyer']
            = $this->flyer
            = FlyerModel::with('image', 'event')->findOrFail((int)$this->property('id'));
    }

}
