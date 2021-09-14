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

add_shortcode( 'taw_upload_shortcode', 'taw_upload_shortcode' );


function taw_uploader_init(){
  ?>
  <h1>TheAir.Works File Uploader</h1>
  <p>Enable the file uploader by using the <strong>[taw_upload_shortcode]</strong> shortcode on any page.</p>
  <?php
  $upload_dir = wp_get_upload_dir();

  $siteUploads = get_site_url() . '/wp-content/uploads/uploader/';
  echo "<p>Uploads are stored in {$siteUploads}";
  echo "<table><tr><td>Name</td><td></td></tr>";
  $files = glob($siteUploads . "*.*");
  print_r ($files);
  //echo "<p>Setting up</p>";
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
      echo '<td><a target="_blank" href="' . $siteUploads . basename($image) . '">Download</a></td></tr>';
    } else {
      continue;
    }
  }
  echo "</table>";
}

// Because we are working on the front end we use the short code
function taw_upload_shortcode(){
  // Get the slug from the url
  global $post;
  $post_slug = $post->post_name;
  $upload_dir = wp_upload_dir();

  // check if the upload directory related to this page is available
  $uploadCheck = $upload_dir['basedir'] . '/uploader/' . $post_slug.'/';
  //echo "<p>Posting to {$uploadCheck}</p>";
  if ( ! file_exists( $uploadCheck ) ) {
          wp_mkdir_p( $uploadCheck );
        }
  //$upload_dir2 = wp_upload_dir();
  //$dir2 = $upload_dir2["basedir"]."/uploader/";
  global $dir2;
  $dir2 = $uploadCheck;
  taw_upload();
?>
  <h3>Upload a File</h3>
  <?php
  listFiles($dir2);
  ?>
  <!-- Form to handle the upload - The enctype value here is very important -->
  <form  method="post" enctype="multipart/form-data">
      <input type='file' id='taw_upload_file' name='taw_upload_file'></input>
      <br />
      <!--?php submit_button('Upload') ?> -->
      <input type='submit'>
  </form>
  <br />
<?php
}

function listFiles($dir2){
  global $post;
  $post_slug = $post->post_name;
  $upload_dir = wp_get_upload_dir();


  // check if the upload directory related to this page is available
  $uploadCheck = $upload_dir['basedir'] . '/uploader/' . $post_slug.'/';
  $siteUploads = get_site_url() . '/wp-content/uploads/uploader/' . $post_slug.'/';
    //echo "URL is {$uploadCheck}";
  //echo "<p>Uploading to {$uploadCheck}</p>";

  //$fulldir = "/wp-content/uploads/uploader/";
  echo "<table><tr><th>File name</th><th>Actions</th></tr>";

  $files = glob($uploadCheck . "*.*");
  //echo "<p>Setting up</p>";
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
      echo '<td><a target="_blank" href="' . $siteUploads . basename($image) . '">Download</a></td></tr>';
    } else {
      continue;
    }
  }
  echo "</table>";
}

function taw_upload(){
  global $post;
  $post_slug = $post->post_name;
  $upload_dir = wp_upload_dir();

  // check if the upload directory related to this page is available
  $uploadCheck = $upload_dir['basedir'] . '/uploader/' . $post_slug.'/';
  $dir2 = $uploadCheck;

  //echo "<p>Base directory is ". $upload_dir['basedir'] . "</p>";
  //$upload_dir = "/wp-content/uploads/uploader";
  //CREATE DIRECTORY
  if(isset($_FILES['taw_upload_file'])){
      $pdf = $_FILES['taw_upload_file'];
    //print_r($pdf);
    //
    $user_dirname = $dir2;
    //echo "<p>The user directory is {$dir2}";

            if ( ! file_exists( $user_dirname ) ) {
            wp_mkdir_p( $user_dirname );
        }
    //echo "<p>Uploading now to {$user_dirname}</p>";

  // upload
  $target_dir = $user_dirname;
  //echo "<p>The target directory is {$target_dir}";
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
