<!DOCTYPE html>
<!--
Filename   : index.php
Created On : 29.05.2019, 10:54:38
Author     : Jonas Wiesli <jonas.wiesli at stud.kftg.ch>
Hey, there's no rule against reusing my own work, is there?
-->
<html lang="de">
<head>
  <!-- Meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Bootstrap V4.1.1 Template für M101">
  <meta name="author" content="Jonas Wiesli">

  <!-- Title -->
  <title>Memegalerie</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="css/jcss.css">

  <!-- Favicons created with realfavicongenerator.net -->
  <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
  <link rel="manifest" href="favicons/site.webmanifest">
  <link rel="mask-icon" href="favicons/safari-pinned-tab.svg" color="#5bbad5">
  <link rel="shortcut icon" href="favicons/favicon.ico">
  <meta name="msapplication-TileColor" content="#2d89ef">
  <meta name="msapplication-config" content="favicons/browserconfig.xml">
  <meta name="theme-color" content="#ffffff">
</head>
<body>
  <div class="main-container">
    <div class="fixer-container">
      <header>
        <h1>Bildergalerie</h1>
      </header>
      <main>
        <?php
        error_reporting(0);

        //Quelle: https://pqina.nl/blog/creating-thumbnails-with-php/
        // Link image type to correct image loader and saver
        // - makes it easier to add additional types later on
        // - makes the function easier to read
        const IMAGE_HANDLERS = [
          IMAGETYPE_JPEG => [
            'load' => 'imagecreatefromjpeg',
            'save' => 'imagejpeg',
            'quality' => 100
          ],
          IMAGETYPE_PNG => [
            'load' => 'imagecreatefrompng',
            'save' => 'imagepng',
            'quality' => 0
          ],
          IMAGETYPE_GIF => [
            'load' => 'imagecreatefromgif',
            'save' => 'imagegif'
          ]
        ];
        if (password_verify($_POST['password'], "$2y$10\$Yy/C/SM6CFtfIDBIjSdYhOAsmlPo3v1F7iC2CpfZRKuk/ZosnGWlG") == true) {

          /**
          * @param $src - a valid file location
          * @param $dest - a valid file target
          * @param $targetWidth - desired output width
          * @param $targetHeight - desired output height or null
          */
          function createThumbnail($src, $dest, $targetWidth, $targetHeight = null) {

            // 1. Load the image from the given $src
            // - see if the file actually exists
            // - check if it's of a valid image type
            // - load the image resource
            // get the type of the image
            // we need the type to determine the correct loader
            $type = exif_imagetype($src);

            // if no valid type or no handler found -> exit
            if (!$type || !IMAGE_HANDLERS[$type]) {
              return null;
            }

            // load the image with the correct loader
            $image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);

            // no image found at supplied location -> exit
            if (!$image) {
              return null;
            }
            
            // 2. Create a thumbnail and resize the loaded $image
            // - get the image dimensions
            // - define the output size appropriately
            // - create a thumbnail based on that size
            // - set alpha transparency for GIFs and PNGs
            // - draw the final thumbnail
            // get original image width and height
            $width = imagesx($image);
            $height = imagesy($image);

            // maintain aspect ratio when no height set
            if ($targetHeight == null) {

              // get width to height ratio
              $ratio = $width / $height;

              // if is portrait
              // use ratio to scale height to fit in square
              if ($width > $height) {
                $targetHeight = floor($targetWidth / $ratio);
              }
              // if is landscape
              // use ratio to scale width to fit in square
              else {
                $targetHeight = $targetWidth;
                $targetWidth = floor($targetWidth * $ratio);
              }
            }

            // create duplicate image based on calculated target size
            $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

            // set transparency options for GIFs and PNGs
            if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {

              // make image transparent
              imagecolortransparent(
                $thumbnail, imagecolorallocate($thumbnail, 0, 0, 0)
              );

              // additional settings for PNGs
              if ($type == IMAGETYPE_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
              }
            }

            // copy entire source image to duplicate image and resize
            imagecopyresampled(
              $thumbnail, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height
            );

            // 3. Save the $thumbnail to disk
            // - call the correct save method
            // - set the correct quality level
            // save the duplicate version of the image to disk
            return call_user_func(
              IMAGE_HANDLERS[$type]['save'], $thumbnail, $dest, IMAGE_HANDLERS[$type]['quality']
            );
          }

          // Quelle: https://www.php.net/manual/de/function.filesize.php Kommentar von rommel
          function human_filesize($bytes, $decimals = 2) {
            $sz = 'BKMGTP';
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
          }

          $verzeichnis = 'bilder/';
          $thumbverzeichnis = 'thumb/';
          foreach (array_slice(scanDir($verzeichnis), 2) as $datei) {
            if (in_array(substr($datei, -3, 3), array('png'))) {
              $image = imagecreatefrompng($verzeichnis . $datei);
              $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
              imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
              imagealphablending($bg, TRUE);
              imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
              imagedestroy($image);
              $quality = 100; // 0 = worst / smaller file, 100 = better / bigger file
              imagejpeg($bg, $verzeichnis . $datei . ".jpg", $quality);
              imagedestroy($bg);
            }
            if (in_array(substr($datei, -3, 3), array('gif'))) {
              $image = imagecreatefromgif($verzeichnis . $datei);
              $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
              imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
              imagealphablending($bg, TRUE);
              imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
              imagedestroy($image);
              $quality = 100; // 0 = worst / smaller file, 100 = better / bigger file
              imagejpeg($bg, $verzeichnis . $datei . ".jpg", $quality);
              imagedestroy($bg);
            }
            if (in_array(substr($datei, -3, 3), array('jpg'))) {
              echo '<div style="float: left; height: 200px; width: 150px; font-size: 20px; word-wrap: break-word; margin: 10px;" onclick="showImage(\'' . $verzeichnis . $datei . '\')">';
              if (file_exists($thumbverzeichnis . 'thumb_' . $datei)) {

              } else {
                createThumbnail($verzeichnis . $datei, $thumbverzeichnis . 'thumb_' . $datei, 150);
              }
              echo '<img src="' . $thumbverzeichnis . 'thumb_' . $datei . ' "><br>';
              $exif = exif_read_data($verzeichnis . $datei, 'FileName');
              if (isset($exif['Title'])) {
                echo $exif['Title'];
              } else {
                echo $exif['FileName'];
              }
              echo '<br>' . human_filesize(filesize($verzeichnis . $datei));
              echo '</div>' . "\r\n";
            }
          }
        } else {
          if ($_POST['password'] != null) {
            print('Das eingegebene Passwort ist falsch! ');
          }
          print('<form action = "index.php" method = "POST">');
          print('<input type = "password" class="form-control" placeholder="Passwort" name = "password" value = "" />');
          print('<input type="submit" class="btn btn-dark" value="Bestätigen" />');
          print('</form>');
        }
        ?>
        <img id="image-holder" alt="">

        <script>
        function showImage(path) {
          var img = document.getElementById('image-holder');
          img.src = path;
          img.style.display = 'block';
        }
        </script>

        <!-- Optional JavaScript -->
        <!-- Custom File first, then jQuery, then Popper.js, then Bootstrap JS -->
        <!-- <script src="js/myscripts.js"></script> -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
      </main>
    </div>
  </div>
</body>
</html>
