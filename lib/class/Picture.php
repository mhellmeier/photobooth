<?php
/**
 * Class to handle all picture functions
 *
 * @package     photobooth
 * @copyright   2020
 * @license     MIT
 * @link        https://github.com/andreknieriem/photobooth/
 *
 */

class Picture {

    private $filename = '';

    /**
     * Takes a picture
     *
     * @return bool
     */
    public function takePicture() {

        global $config;

        if ($config['dev']) {
            $demoFolder = __DIR__ . '/../../resources/img/demo/';
            $devImg = array_diff(scandir($demoFolder), array('.', '..'));
            copy(
                $demoFolder . $devImg[array_rand($devImg)],
                $this->getFilename()
            );
        } elseif ($config['previewCamTakesPic']) {
            $data = $_POST['canvasimg'];
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            file_put_contents($this->getFilename(), $data);
            if ($config['previewCamFlipHorizontal']) {
                $im = imagecreatefromjpeg($this->getFilename());
                imageflip($im, IMG_FLIP_HORIZONTAL);
                imagejpeg($im, $this->getFilename());
                imagedestroy($im);
            }
        } else {
            $dir = dirname($this->getFilename());
            chdir($dir); //gphoto must be executed in a dir with write permission
            $cmd = sprintf($config['take_picture']['cmd'], $this->getFilename());
            exec($cmd, $output, $returnValue);
            if ($returnValue) {
                die(json_encode([
                    'error' => 'Gphoto returned with an error code',
                    'cmd' => $cmd,
                    'returnValue' => $returnValue,
                    'output' => $output,
                ]));
            } elseif (!file_exists($this->getFilename())) {
                die(json_encode([
                    'error' => 'File was not created',
                    'cmd' => $cmd,
                    'returnValue' => $returnValue,
                    'output' => $output,
                ]));
            }
        }

        return true;
    }


    /* Getter and Setter */

    /**
     * Get the filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the filename
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

}
