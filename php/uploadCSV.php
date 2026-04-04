<?php
$title = "uploadCSV";
$message = $error = null;

include_once 'config.php';

if (isset($_FILES["fileCSV"])) {
    $from_path = $_FILES["fileCSV"]["tmp_name"];
    $to_path =  BASE_DIR . '/assets/uploads/' . UPLOAD_FILE_NAME;
    if ($_FILES["fileCSV"]["error"] > 0) {
        $error = "Return Code: {$_FILES["fileCSV"]["error"]}";
    } elseif (!move_uploaded_file($from_path, $to_path)) {
        $error = "Failed moving file to uploads folder. Please try again";
    } else {
        $message = "Successfully uploaded file";
    }
}

include_once BASE_DIR . '/templates/header.php';
?>
<form class="row g-3 py-5" enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
    <div class="col-md-4">
        <label for="fileCSV" class="form-label">Upload Data File in CSV format</label>
        <input class="form-control form-control-lg" id="fileCSV" name="fileCSV" type="file" accept=".csv">
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>

<?php
include_once BASE_DIR . '/templates/footer.php';
