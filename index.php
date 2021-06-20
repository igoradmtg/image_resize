<?php
function resize_image($file, $w, $h, $crop = false) {
    $src = @imagecreatefromjpeg($file);
    if(!$src) $src = @imagecreatefromgif ($file);   //try gif
    if(!$src) $src = @imagecreatefrompng ($file);   //try png
    if(!$src) {echo "Error read image $file <br>\r\n";return false;}
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        $width_orig = $width;
        $height_orig = $height;
        if ($width > $height) {
            $width = $height;
        } else {
            $height = $width;
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = intval($h*$r);
            $newheight = $h;
        } else {
            $newheight = intval($w/$r);
            $newwidth = $w;
        }
    }
    $dst = imagecreatetruecolor($newwidth, $newheight);
    if ($crop) {
      //echo "$width_orig $height_orig $width $height \r\n";
      $src_x = intval($width_orig / 2 - $width / 2);
      $src_y = intval($height_orig / 2 - $height / 2);
      //echo "$src_x $src_y \r\n";
      imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, $newwidth, $newheight, $width, $height);
    } else {
      imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    }
    return $dst;
}

$str_error = '';
if (isset($_FILES['document']['name'])) {
  $is_file = true;
  $fname = basename($_FILES['document']['name']);
  $uploaddir = __DIR__ . '/';
  $uploadfile = $uploaddir . $fname;
  if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadfile)) {
    //add_log('Upload file '.$uploadfile);
    $w = 200; // Ширина
    $h = 200; // Высока
    $quality = 50; // Необязательный параметр, и может принимать значения в диапазоне от 0 (низкое качество, маленький размер файла) до 100 (высокое качество, большой размер файла). По умолчанию (-1) используется качество IJG (около 75).
    $img = resize_image($uploadfile, $w, $h);
    unlink($uploadfile);
    if ($img == false) {
        $str_error = 'Error resize file '.$fname;
    } else {
        header("Content-Type: image/jpeg");
        imagejpeg($img,null,$quality);
        exit;
    }
  } else {
      $str_error = 'Error move file';
    //add_log('Error move file');
  }
}
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.83.1">
    <title>Image Resize</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    
  </head>
  <body>
<header>
  <div class="collapse bg-primary" id="navbarHeader">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor01">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link active" href="#">Home
                <span class="visually-hidden">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Features</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Pricing</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">About</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Dropdown</a>
              <div class="dropdown-menu">
              {% for item in menu() %}
                <a class="dropdown-item" href="{{ item.url }}">{{ item.title }}</a>
              {% endfor %}  
                <a class="dropdown-item" href="#">Another action</a>
                <a class="dropdown-item" href="#">Something else here</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Separated link</a>
              </div>
            </li>
          </ul>
          <form class="d-flex">
            <input class="form-control me-sm-2" type="text" placeholder="Search">
            <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
          </form>
        </div>
      </div>
  </nav>
    
  </div>
  <div class="navbar navbar-dark bg-primary shadow-sm">
    <div class="container">
      <a href="#" class="navbar-brand d-flex align-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" aria-hidden="true" class="me-2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        <strong>Album</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>

</header>
  
  <?php
  if (!empty($str_error))
      echo '<p>'.$str_error.'</p>';
  ?>
<!-- Тип кодирования данных, enctype, ДОЛЖЕН БЫТЬ указан ИМЕННО так -->
<form enctype="multipart/form-data" action="resize_image.php" method="POST">
    <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
    <!-- Название элемента input определяет имя в массиве $_FILES -->
    Отправить этот файл: <input name="document" type="file" /><br>
    <input type="submit" value="Отправить файл" />
</form>
<footer class="text-muted py-5">
  <div class="container">
    <p class="float-end mb-1">
      <a href="#">Back to top</a>
    </p>
    <p class="mb-1">Album example is &copy; Bootstrap, but please download and customize it for yourself!</p>
    <p class="mb-0">New to Bootstrap? <a href="/">Visit the homepage</a> or read our <a href="../getting-started/introduction/">getting started guide</a>.</p>
  </div>
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>