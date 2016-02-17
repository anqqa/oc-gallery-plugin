<?php
namespace Klubitus\Gallery\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Klubitus\Calendar\Models\Flyer as FlyerModel;
use Lang;
use October\Rain\Support\Collection;
use Redirect;
use Request;


class Flyers extends ComponentBase {

    const BY_DATE    = 'by_date';
    const NEW_FLYERS = 'new_flyers';
    const SEARCH     = 'search';

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

    /**
     * @var  int
     */
    public $month;
    public $year;

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

        $currentPage = input('page');
        $search      = trim(input('search'));

        /** @var  Collection  $flyers */
        switch ($this->listType) {
            case self::BY_DATE:
                $flyers = FlyerModel::with('image')
                    ->date($this->year, $this->month)
                    ->paginate(16, $currentPage);
                break;

            case self::NEW_FLYERS:
                $flyers = FlyerModel::with('image')
                    ->recentFlyers()
                    ->paginate(16, $currentPage);
                break;

            case self::SEARCH:
                $query = FlyerModel::with('image')
                    ->search($search);

                if ($this->year) {
                    $query->date($this->year, $this->month);
                }
                else {
                    $query->recentFlyers();
                }

                $flyers = $query->paginate(16, $currentPage);
                break;

            default:
                return [];
        }

        $flyers->each(function(FlyerModel $flyer) {
            $flyer->setUrl($this->flyerPage, $this->controller);
        });

        // Pagination
        $query = [];
        $search and $query['search'] = $search;
        $query['page'] = '';
        $paginationUrl = Request::url() . '?' . http_build_query($query);

        $lastPage = $flyers->lastPage();
        if ($currentPage > $lastPage && $currentPage > 1) {
            return Redirect::to($paginationUrl . $lastPage);
        }

        $this->page['paginationUrl'] = $paginationUrl;

        return $flyers;
    }


    public function onRun() {
        $this->prepareVars();

        $this->page['flyers'] = $this->listFlyers();
    }


    protected function prepareVars() {
        $this->month     = $this->page['month'] = $this->property('month') !== false ? (int)$this->property('month') : null;
        $this->year      = $this->page['year']  = $this->property('year') !== false ? (int)$this->property('year') : null;
        $this->flyerPage = $this->property('flyerPage');

        if (trim(input('search'))) {
            $this->listType = self::SEARCH;
        }
        else if (!is_null($this->year)) {
            $this->listType = self::BY_DATE;
        }
        else {
            $this->listType = $this->property('listType');
        }
    }

}
