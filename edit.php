<?php
$no_data = false;

require './config/config.php';

use function Database\connect;

$conn = connect();
$table = "tasks";
$data;
$result;

// Check if there's parameterss edit-id
if (!isset($_POST["edit-id"])) {
  $no_data = true;
} else {
  $id = $_POST["edit-id"];

  $data = mysqli_query($conn, "SELECT * FROM $table WHERE `id`=$id");
  $result = mysqli_fetch_assoc($data);
}

mysqli_close($conn);

?>

<?php include("./components/header.php") ?>
<div class="bg-primary d-flex justify-content-center align-items-center" style="min-height: 92vh;">
  <div class="container">
    <div class="row">
      <?php if ($no_data) : ?>
        <h1 style="text-align: center;">NO DATA<h1>
          <?php else : ?>
            <form class="col-12 d-flex flex-column" action="index.php" method="post">
              <div class="form-group mt-3 mb-3 d-flex align-items-center flex-column w-100">
                <label for="title-edit">
                  <h4>Title</h4>
                </label>
                <input name="title" id="title-edit" style="width: 75%; padding: 3px; border-radius: 10px;" type="text" placeholder="Eg. Mencari petunjuk blue" <?= "value=" . "'{$result["title"]}'"; ?> />
              </div>
              <div class="form-group mt-3 mb-3 d-flex align-items-center flex-column w-100">
                <label for="description-edit">
                  <h4>Description</h4>
                </label>
                <textarea id="description-edit" name="description" style="width: 75%; padding: 3px; border-radius: 10px; height: 100px;" placeholder="Eg. Blue adalah seorang anjing biru yang suka berkelana"><?= "{$result["description"]}"; ?></textarea>
              </div>
              <input type="text" name="edit-id" value="<?= $_POST["edit-id"]; ?>" style="display: none;">
              <div class="mt-3 mb-3 d-flex align-items-center flex-column w-100">
                <button name="edit-form" type="submit" class="btn btn-info shadow w-75">Edit Data</button>
              </div>
            </form>
          <?php endif; ?>
    </div>
  </div>
</div>
<?php include("./components/footer.php") ?>