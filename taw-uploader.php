<?php

/**
 * @package TheAir.Works Uploader
 * @version 1.0
 */
/*
Plugin Name: TAW Uploader
Plugin URI:
Description: File uploader for TheAir.Works
Author: TheAir.Works
Version: 1.0
Author URI: https://theair.works
*/
add_action('admin_menu', 'taw_uploader_setup_menu');

function taw_uploader_setup_menu(){
    add_menu_page( 'TAW Uploader', 'TAW Uploader', 'manage_options', 'taw-uploader', 'taw_uploader_init' );
}

function listFiles($dir2){
  $fulldir = "/wp-content/uploads/uploader/";
  echo "<table><tr><th>File name</th><th>Image</th></tr>";

  $files = glob($dir2 . "*.*");
  for ($i = 0; $i < count($files); $i++) {
    $image = $files[$i];
    $supported_file = array(
        'gif',
        'jpg',
        'jpeg',
        'png',
        'pdf'
    );

    $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    if (in_array($ext, $supported_file)) {
      echo "<tr><td>".basename($image)."</td>";
      echo '<td><img src="' . $fulldir . '/' . basename($image) . '" alt="Random image" ,width=50px, height=50px /></td></tr>';
    } else {
      continue;
    }
  }
  echo "</table>";
}

function getFileList($dir2)
{
  // NO LONGER USED
  // array to hold return value
  echo "<h3>Files</h3>";

  // open pointer to directory and read list of files
  $d = dir($dir2);
  //echo "Path: " . $d->path . "<br>";
  // Setup table
  echo "<table><tr><th>File name</th></tr>";
  while (($file = $d->read()) !== false){
    echo "<tr><td>" . $file . "</td></tr>";
  }
  $d->close();
  echo "</table>";
}

function taw_uploader_init(){
  $upload_dir2 = wp_upload_dir();
  $dir2 = $upload_dir2["basedir"]."/uploader/";
  taw_upload();
?>

<h1>TheAir.Works Uploader</h1>
  <h2>Upload a File</h2>
  <!-- Form to handle the upload - The enctype value here is very important -->
  <!-- Form to handle the upload - The enctype value here is very important -->
  <form  method="post" enctype="multipart/form-data">
      <input type='file' id='taw_upload_file' name='taw_upload_file'></input>
      <?php submit_button('Upload') ?>
  </form>
<?php
//echo "Displaying {$dir2}";
//getFileList($dir2);
//echo "-----";
listFiles($dir2);
}

function taw_upload(){
  $upload_dir = wp_upload_dir();
  //echo "<p>Base directory is ". $upload_dir['basedir'] . "</p>";
  //$upload_dir = "/wp-content/uploads/uploader";
  //CREATE DIRECTORY
  if(isset($_FILES['taw_upload_file'])){
      $pdf = $_FILES['taw_upload_file'];
    //print_r($pdf);
    //
    $user_dirname = $upload_dir['basedir'].'/uploader/';
            if ( ! file_exists( $user_dirname ) ) {
            wp_mkdir_p( $user_dirname );
        }
    //echo "<p>Uploading now to {$user_dirname}</p>";


    if (!is_file($pdf) && !is_dir($pdf)) {
      //mkdir($dir); //create the directory
      //wp_mkdir_p($targetfilename);
      //echo "<p>{$pdf} does not exist.</p>";
  }
  else
  {
      //echo "{$pdf} exists and is a valid dir";
  }
  $dir = $user_dirname;
  $a = scandir($dir);
  $b = scandir($dir,1);
  //print_r($a);
  //print_r($b);
  // upload
  $target_dir = $user_dirname;
  $target_file = $target_dir . basename($_FILES['taw_upload_file']["name"]);
  //echo "<div><p>Uploading {$target_file}</p></div>";
  //print_r($target_file);
  $uploadOk = 1;
  $FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  // check file structure
  if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
  }
  // check file size​
  if ($_FILES['taw_upload_file']["size"] > 5000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
  }

  // File formats allowed
  if($FileType != "jpg" && $FileType != "png" && $FileType != "jpeg"
  && $FileType != "gif" && $FileType != "doc" && $FileType != "docx"
  && $FileType != "txt" && $FileType != "xls" && $FileType != "xlsx"
  && $FileType != "pdf" && $FileType != "mp3" && $FileType != "mp4") {
    echo "Sorry, only JPG, JPEG, PNG, GIF, DOC, DOCX, TXT, XLS, XLSX, PDF, MP3 and MP4 files are allowed.";
    $uploadOk = 0;
  }

  //$uploadOk = 0;
  // Check if upload is ok
  if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    // if ok upload file
  } else {
    if (move_uploaded_file($_FILES['taw_upload_file']["tmp_name"], $target_file)) {
      echo "The file ". htmlspecialchars( basename( $_FILES['taw_upload_file']["name"])). " has been uploaded.";
    } else {
      echo "Sorry, there was an error uploading your file.";
    }
  }
}
}
?>
