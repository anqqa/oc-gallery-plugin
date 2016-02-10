<?php namespace Klubitus\Gallery\Models;

use October\Rain\Support\Facades\Http;
use System\Models\File as FileBase;
use SystemException;


/**
 * File attachment model with remote file support.
 */
class File extends FileBase {

    /**
     * @var  string  Temp file when downloading remote files.
     */
    protected $tempFile;


    /**
     * Before the model is saved
     * - check if new file data has been supplied, eg: $model->data = 'http://domain.tld/image.jpg';
     */
    public function beforeSave() {
        if (filter_var($this->data, FILTER_VALIDATE_URL) !== false) {
            $this->fromUrl($this->data);
        }

        parent::beforeSave();

        // Delete downloaded file from temp
        if ($this->tempFile) {
            @unlink(realpath($this->tempFile));
        }

    }


    /**
     * Add image size information.
     *
     * @param  string $filePath
     * @return  File
     */
    public function fromFile($filePath) {
        if (!is_null($filePath)) {
            parent::fromFile($filePath);

            if ($this->isImage() && $this->hasFile($this->disk_name)) {
                list($width, $height) = getimagesize(realpath($filePath));

                $width and $this->image_width = $width;
                $height and $this->image_height = $height;
            }
        }

        return $this;
    }


    /**
     * Add image size information.
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $uploadedFile
     * @return  File
     */
    public function fromPost($uploadedFile) {
        if (!is_null($uploadedFile)) {
            parent::fromPost($uploadedFile);

            if ($this->isImage() && $this->hasFile($this->disk_name)) {
                list($width, $height) = getimagesize($uploadedFile->getRealPath());

                $width and $this->image_width = $width;
                $height and $this->image_height = $height;
            }
        }

        return $this;
    }


    /**
     * Download remote file and save to temp.
     *
     * @param  string
     * @return  File
     * @throws  SystemException  If file could not be downloaded
     */
    public function fromUrl($url) {
        $this->source = $url;

        $fileName = basename(parse_url($url, PHP_URL_PATH));
        $localFile = temp_path('images') . '/' . $fileName;

        $response = Http::get($url, function($http) use ($localFile) {
            $http->toFile($localFile);
        });

        if ($response->code == 200) {
            $this->data = $localFile;
            $this->tempFile = $localFile;
        }

        return $this;
    }

}
