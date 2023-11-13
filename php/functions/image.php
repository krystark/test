<?

    /**
     * Создает файл формата .webp, изменяет размер изображения
     * @param string $file_path
     * @param int $width
     * @param int $height
     * @param bool $crop
     * @throws ImagickException
     */
    function compress_resize_img(string $file_path, int $width = 1280, int $height = 720, bool $crop = false)
    {
        if (file_exists($file_path)) {
            $file_type = mb_strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            $img = new Imagick($file_path);
            if ($file_type == "gif") {
                $img = $img->coalesceImages();
                $i_width = $img->getImageWidth();
                $i_height = $img->getImageHeight();
                if (($i_height > $height) || ($i_width > $width)) {
                    if ($width > $height) {
                        $new_width = $width;
                        $new_height = ceil($i_height / ($i_width / $width));
                    } else {
                        $new_height = $height;
                        $new_width = ceil($i_width / ($i_height / $height));
                    }
                    $new_x = ($new_width - $width) / 2;
                    $new_y = ($new_height - $height) / 2;
                }
                foreach ($img as $frame) {
                    if (isset($new_x)) {
                        $frame->scaleImage($new_width, $new_height, $crop);
                        if ($crop) {
                            $frame->cropImage($width, $height, $new_x, $new_y);
                        }
                    }
                    $frame->setImageDepth(3 /* bits */);
                    $frame->stripImage();
                }
                $img = $img->deconstructImages();
                $img->writeImages($file_path, true);
            } else {
                if (
                    (($img->getImageColorspace() != IMagick::COLORSPACE_SRGB) || ($img->getImageDepth() > 8))
                    &&
                    $img->getImageColorspace() != IMagick::COLORSPACE_GRAY
                ) {
                    $img->transformimagecolorspace(IMagick::COLORSPACE_RGB);
                    $img->setImageDepth(8);
                }

                resize_img($width, $height, $img, $crop);
                if ($file_type == "png") {
                    $img->setImageFormat("png");
                } else {
                    $img->setImageFormat("jpg");
                    $img->setImageCompression(Imagick::COMPRESSION_JPEG);
                    $img->setImageCompressionQuality(70);
                    $img->setInterlaceScheme(Imagick::INTERLACE_PLANE);
                }
                $img->stripImage();
                $img->writeImage($file_path);
            }
            $img->clear();
            $img->destroy();
            if ($file_type != "gif") {
                $file_path_webp = pathinfo($file_path, PATHINFO_DIRNAME) . "/" . pathinfo($file_path, PATHINFO_FILENAME) . ".webp";
                $img2 = new Imagick($file_path);
                if (
                    (($img2->getImageColorspace() != IMagick::COLORSPACE_SRGB) || ($img2->getImageDepth() > 8))
                    &&
                    $img2->getImageColorspace() != IMagick::COLORSPACE_GRAY
                ) {
                    $img2->transformimagecolorspace(IMagick::COLORSPACE_RGB);
                    $img2->setImageDepth(8);
                }
                resize_img($width, $height, $img2, $crop);
                $img2->setImageFormat("webp");
                $img2->setOption("webp:method", "6");
                $img2->setImageCompressionQuality(80);
                $img2->stripImage();
                $img2->writeImage($file_path_webp);
                $img2->clear();
                $img2->destroy();
            }
        }
    }

    /**
     * @param int $width
     * @param int $height
     * @param Imagick $img
     * @param bool $crop
     * @throws ImagickException
     */
    function resize_img(int $width, int $height, Imagick $img, bool $crop = true): void
    {
        $i_width = $img->getImageWidth();
        $i_height = $img->getImageHeight();
        if (($i_height > $height) || ($i_width > $width)) {
            if ($width >= $height) {
                $new_width = $width;
                $new_height = floor($i_height / ($i_width / $width));
            } else {
                $new_height = $height;
                $new_width = floor($i_width / ($i_height / $height));
            }
            $img->scaleImage($new_width, $new_height, $crop);
            if ($crop) {
                $new_x = ($new_width - $width) / 2;
                $new_y = ($new_height - $height) / 2;
                //die("$new_width $new_height $new_x $new_y");
                $img->cropImage($width, $height, $new_x, $new_y);
            }
        }
    }
