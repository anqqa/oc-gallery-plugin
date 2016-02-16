<?php namespace Klubitus\Gallery\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Klubitus\Calendar\Models\Flyer as FlyerModel;


class FlyerMonths extends ComponentBase {

    /**
     * @var  string
     */
    public $flyerPage;
    public $flyersPage;

    /**
     * @var  int
     */
    public $month;
    public $year;

    /**
     * @var  array
     */
    public $months = null;


    public function componentDetails() {
        return [
            'name'        => 'Flyers Per Month',
            'description' => 'Count of flyers per month.'
        ];
    }


    public function defineProperties() {
        return [
            'flyersPage' => [
                'title' => 'Flyers Page',
                'description' => 'Page name for a list of flyers.',
                'type' => 'dropdown',
            ],
            'flyerPage' => [
                'title' => 'Flyer Page',
                'description' => 'Page name for a single flyer.',
                'type' => 'dropdown',
            ],
            'month' => [
                'title' => 'Month',
                'placeholder' => 'Optional',
                'default' => '{{ :month }}',
                'type' => 'string',
                'validationPattern' => '^[0-1]?[0-9]$',
            ],
            'year' => [
                'title' => 'Year',
                'placeholder' => 'Optional',
                'default' => '{{ :year }}',
                'type' => 'string',
                'validationPattern' => '^[0-3][0-9]{3}$',
            ],
        ];
    }


    public function getFlyerPageOptions() {
        return ['' => '- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }


    public function getFlyersPageOptions() {
        return $this->getFlyerPageOptions();
    }


    public function listMonths() {
        if (!is_null($this->months)) {
            return $this->months;
        }

        $months = [];

        foreach (FlyerModel::countsPerMonth() as $year => $counts) {
            $months[$year] = [
                'months' => [],
                'url'    => $this->controller->pageUrl(
                    $this->flyersPage,
                    [ 'year' => $year ],
                    false
                ),
                'active' => $year === $this->year,
            ];

            foreach ($counts as $month => $count) {
                $months[$year]['months'][$month] = [
                    'count' => $count,
                    'url'   => $this->controller->pageUrl(
                        $this->flyersPage,
                        [ 'year'  => $year, 'month' => $month ],
                        false
                    ),
                    'active' => $year === $this->year && $month === $this->month,
                ];
            }
        }

        return $this->months = $months;
    }


    public function onRun() {
        $this->prepareVars();

        $this->page['flyersPerMonth'] = $this->listMonths();
    }


    protected function prepareVars() {
        $this->flyerPage  = $this->property('flyerPage');
        $this->flyersPage = $this->property('flyersPage');
        $this->month      = $this->property('month') !== false ? (int)$this->property('month') : null;
        $this->year       = $this->property('year') !== false ? (int)$this->property('year') : null;
    }

}
