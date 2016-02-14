<?php
namespace Klubitus\Gallery\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Klubitus\Calendar\Models\Flyer as FlyerModel;
use Lang;
use October\Rain\Support\Collection;


class Flyers extends ComponentBase {

    const BY_DATE    = 'by_date';
    const NEW_FLYERS = 'new_flyers';

    /**
     * @var  string
     */
    public $flyerPage;

    /**
     * @var  Collection  FlyerModels
     */
    public $flyers = null;

    /**
     * @var  string
     */
    public $listType;


    public function componentDetails() {
        return [
            'name'        => 'Flyers',
            'description' => 'Lists of flyers.'
        ];
    }


    public function defineProperties() {
        return [
            'flyerPage' => [
                'title'       => 'Flyer Page',
                'description' => 'Page name for a single flyer.',
                'type'        => 'dropdown',
            ],
            'listType' => [
                'title'       => 'Flyer List Type',
                'description' => 'Type of flyers to list.',
                'type'        => 'dropdown',
                'default'     => self::NEW_FLYERS,
                'options'     => [
                    self::NEW_FLYERS => 'New',
                    self::BY_DATE    => 'By date',
                ],
            ],
            'month' => [
                'title'             => 'Month',
                'placeholder'       => 'Optional',
                'default'           => '{{ :month }}',
                'type'              => 'string',
                'validationPattern' => '^[0-1]?[0-9]$',
            ],
            'year' => [
                'title'             => 'Year',
                'placeholder'       => 'Optional',
                'default'           => '{{ :year }}',
                'type'              => 'string',
                'validationPattern' => '^[0-3][0-9]{3}$',
            ],
        ];
    }


    public function getFlyerPageOptions() {
        return ['' => '- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }


    public function listFlyers() {
        if (!is_null($this->flyers)) {
            return $this->flyers;
        }

        /** @var  Collection  $flyers */
        switch ($this->listType) {
            case self::NEW_FLYERS:
                $flyers = FlyerModel::with('image')
                    ->recentFlyers()
                    ->limit(16)
                    ->get();
                break;

            case self::BY_DATE:
            default:
                return [];
        }

        $flyers->each(function(FlyerModel $flyer) {
            $flyer->setUrl($this->flyerPage, $this->controller);
        });

        return $flyers;
    }


    public function onRun() {
        $this->prepareVars();

        $this->page['flyers'] = $this->listFlyers();
    }


    protected function prepareVars() {
        $this->listType  = $this->property('listType');
        $this->flyerPage = $this->property('flyerPage');
    }

}
