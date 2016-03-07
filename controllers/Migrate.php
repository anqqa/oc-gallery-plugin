<?php namespace Klubitus\Gallery\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Klubitus\Calendar\Models\Flyer as FlyerModel;
use Klubitus\Gallery\Models\LegacyImage as ImageModel;


/**
 * Migrate Back-end Controller
 */
class Migrate extends Controller {

    public function __construct() {
        parent::__construct();

        BackendMenu::setContext('Klubitus.Gallery', 'gallery', 'migrate');
    }


    public function index() {}


    public function index_onMigrateFlyers($save = true) {
        $migrated = $unmigrated = $added = $skipped = $ignored = 0;

        // Load a batch of unmigrated flyers
        $unmigrated = FlyerModel::with('legacyImage')
            ->has('image', 0)
            ->count();

        $flyers = FlyerModel::with('legacyImage')
            ->has('image', 0)
            ->limit(100)
            ->get();
        $migrated = $flyers->count();

        // Migrate
        foreach ($flyers as $flyer) {
            $legacyImage = $flyer->legacyImage;

            // Too old?
            if (!$legacyImage->file) {
                $skipped++;

                continue;
            }

            $dir = str_split(sprintf('%08x', (int)$legacyImage->id), 2);
            array_pop($dir);
            $dir = implode('/', $dir);
            $path = 'migrate/' . $dir . '/' . $legacyImage->file;

            // Original file not found?
            if (!file_exists($path)) {
                $ignored++;

                continue;
            }

            if ($save) {
                $flyer->image()->create(['data' => $path]);

                $added++;
            }
        }

        $this->vars['added']      = $added;
        $this->vars['ignored']    = $ignored;
        $this->vars['skipped']    = $skipped;
        $this->vars['migrated']   = $migrated;
        $this->vars['unmigrated'] = $unmigrated;
    }


    public function index_onMigrateFlyersTest() {
        return $this->index_onMigrateFlyers(false);
    }

}
