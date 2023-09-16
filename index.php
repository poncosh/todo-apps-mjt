<?php

require './config/config.php';

use function Database\connect;

$table = "tasks";

$conn = connect();

$empty_data = false;

// Submit New Task
if (isset($_POST["submit-activity"])) {
  $sql = "INSERT INTO $table (`title`, `description`, `status`) VALUES (?, ?, ?);";
  $title = $_POST["title"];
  $description = $_POST["description"];
  $status = "Pending";

  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "sss", $title, $description, $status);
  mysqli_stmt_execute($stmt);
}

// Edit Status
if (isset($_POST["status"])) {
  $sql = "UPDATE $table SET $table.`status` = ? WHERE $table.`id` = ?;";
  $status_post = $_POST["status"];
  $id = $_POST["id"];

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $status_post, $id);
  $stmt->execute();
}

// Edit Task
if (isset($_POST["edit-form"])) {
  $sql = "UPDATE $table SET $table.`title` = ?, $table.`description` = ? WHERE $table.`id` = ?;";
  $title = $_POST["title"];
  $description = $_POST["description"];
  $id = $_POST["edit-id"];

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $title, $description, $id);
  $stmt->execute();
}

// Delete Task
if (isset($_POST["delete-id"])) {
  $sql = "DELETE FROM $table WHERE $table.`id` = ?;";
  $id = $_POST["delete-id"];

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $id);
  $stmt->execute();
}

// Pagination
$activities_per_page = 7;
$check_data = count(query("SELECT * FROM $table"));
$total_pages = ceil($check_data / $activities_per_page);
$active_page = (isset($_GET["page"])) ? $_GET["page"] : 1;
$active_data = ($activities_per_page * $active_page) - $activities_per_page;


$activities = query("SELECT * FROM $table LIMIT $active_data, $activities_per_page");

// Search
if (isset($_GET["search"])) {
  $keyword = $_GET["search"];
  $check_data = count(query("SELECT * FROM $table WHERE $table.`title` LIKE '%$keyword%' OR $table.`description` LIKE '%$keyword%' OR $table.`status` LIKE '%$keyword%'"));
  $total_pages = ceil($check_data / $activities_per_page);
  $active_data = ($activities_per_page * $active_page) - $activities_per_page;

  $activities = query("SELECT * FROM $table WHERE $table.`title` LIKE '%$keyword%' OR $table.`description` LIKE '%$keyword%' OR $table.`status` LIKE '%$keyword%' LIMIT $active_data, $activities_per_page");
}

if ($check_data == 0) {
  $empty_data = true;
}

$tHeads = [
  "#",
  "Title",
  "Description",
  "Status",
  "Action"
];

$statuses = [
  "Pending",
  "In Progress",
  "Completed"
];

mysqli_close($conn);

?>

<?php include("./components/header.php") ?>
<div class="bg-primary d-flex flex-column justify-content-center align-items-center" style="min-height: 92vh;">
  <div class="container">
    <div class="row">
      <div class="col-6 p-3">
        <h2>To Do List</h2>
      </div>
      <div class="col-6 p-3">
        <button class="btn btn-info float-end" data-bs-toggle="modal" data-bs-target="#modal-add">
          + Add Activity
        </button>
      </div>
      <div class="col-12">
        <?php if ($empty_data) : ?>
          <h3 style="text-align: center;">No data, let's add some</h3>
        <?php else : ?>
          <form method="get" class="w-100 d-flex flex-column align-items-center">
            <div class="form-group w-100 d-flex flex-column align-items-center mb-2">
              <input type="text" name="search" id="search" placeholder="Search..." style="width: 50%; border-radius: 10px;">
            </div>
            <button type="submit" class="w-50 btn btn-info">Search</button>
          </form>
          <div class="w-100 d-flex flex-row justify-content-center mt-3">
            <?php if ($active_page > 1 && !isset($_GET["search"])) : ?>
              <a class="nav-link p-3 bg-dark text-white ms-2 me-2 rounded-3 shadow" href="?page=<?= $active_page - 1; ?>">&lt;</a>
            <?php elseif ($active_page > 1 && isset($_GET["search"])) : ?>
              <a class="nav-link p-3 bg-dark text-white ms-2 me-2 rounded-3 shadow" href="?page=<?= $active_page - 1; ?>&search=<?= $_GET["search"]; ?>">&lt;</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
              <?php if ($i == $active_page && !isset($_GET["search"])) : ?>
                <a class="nav-link p-3 bg-dark text-white ms-2 me-2 fw-bold rounded-3 shadow" href="?page=<?= $i; ?>"><?= $i; ?></a>
              <?php elseif ($i == $active_page && isset($_GET["search"])) : ?>
                <a class="nav-link p-3 bg-dark text-white ms-2 me-2 fw-bold rounded-3 shadow" href="?page=<?= $i; ?>&search=<?= $_GET["search"]; ?>"><?= $i; ?></a>
              <?php elseif ($i != $active_page && !isset($_GET["search"])) : ?>
                <a class="nav-link p-3 bg-dark text-white ms-2 me-2 rounded-3 shadow" href="?page=<?= $i; ?>"><?= $i; ?></a>
              <?php else : ?>
                <a class="nav-link p-3 bg-dark text-white ms-2 me-2 rounded-3 shadow" href="?page=<?= $i; ?>&search=<?= $_GET["search"]; ?>"><?= $i; ?></a>
              <?php endif; ?>
            <?php endfor; ?>
            <?php if ($active_page < $total_pages && !isset($_GET["search"])) : ?>
              <a class="nav-link p-3 bg-dark text-white ms-2 me-2 rounded-3 shadow" href="?page=<?= $active_page + 1; ?>">&gt;</a>
            <?php elseif ($active_page < $total_pages && isset($_GET["search"])) : ?>
              <a class="nav-link p-3 bg-dark text-white ms-2 me-2 rounded-3 shadow" href="?page=<?= $active_page + 1; ?>&search=<?= $_GET["search"]; ?>">&gt;</a>
            <?php endif; ?>
          <?php endif; ?>
          </div>
      </div>
      <div class=" col-12 table-responsive mt-3">
        <table class="shadow table table-hover align-middle">
          <thead>
            <tr>
              <?php foreach ($tHeads as $tHead) : ?>
                <th style="text-align: center;" scope="col"><?= $tHead; ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php $index = 0; ?>
            <?php foreach ($activities as $row) : ?>
              <tr>
                <td scope="row"><?= $index + 1; ?></td>
                <td style="min-height: 79px; overflow: hidden; text-overflow: ellipsis;-webkit-line-clamp: 3; -webkit-box-orient: vertical;"><?= $row["title"]; ?></td>
                <td style="min-height: 79px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;"><?= $row["description"]; ?></td>
                <td class="text-center">
                  <form method="post">
                    <select name="status" onchange='this.form.submit()'>
                      <?php foreach ($statuses as $status) : ?>
                        <option <?= "value=" . "'$status'"; ?> <?php if ($row["status"] == $status) echo "selected" ?>><?= $status; ?></option>
                      <?php endforeach; ?>
                    </select>
                    <input type="text" name="id" <?= "value=" . "'{$row["id"]}'"; ?> style="display: none;" />
                    <noscript><input type="submit" value="Submit"></noscript>
                  </form>
                </td>
                <td>
                  <div class="d-flex flex-row gap-3 justify-content-center">
                    <form id="delete-act-<?= $row["id"]; ?>" method="post">
                      <input type="text" name="delete-id" <?= "value=" . "'{$row["id"]}'"; ?> style="display: none;" />
                      <noscript><input type="submit" value="Submit delete"></noscript>
                    </form>
                    <button onclick="deleteAction(<?= $row["id"]; ?>)" class="btn btn-danger">Delete</button>
                    <form action="edit.php" method="post">
                      <input type="text" name="edit-id" <?= "value=" . "'{$row["id"]}'"; ?> style="display: none;" />
                      <button type="submit" class="btn btn-secondary">Edit</button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php $index++; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal-add" tabIndex="-1" role="dialog" aria-labelledby="modalTambahKegiatan" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">
          Add Activity
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post">
          <div class="form-group mt-3 mb-3">
            <label for="activity-title">
              <h4>Title</h4>
            </label>
            <input type="text" name="title" id="activity-title" placeholder="Insert Activity Title..." style="width: 100%; border-radius: 10px;">
          </div>
          <div class="form-group mt-3 mb-3">
            <label for="activity-description">
              <h4>Description</h4>
            </label>
            <textarea name="description" id="activity-description" placeholder="Insert Activity Description..." style="width: 100%; border-radius: 10px; height: 100px;"></textarea>
          </div>
          <button type="submit" name="submit-activity" class="btn btn-success">Add activity</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function deleteAction(id) {
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire(
          'Deleted!',
          'Your file has been deleted.',
          'success'
        )
        setTimeout(() => {
          document.getElementById(`delete-act-${id}`).submit();
        }, 1500);
      }
    })
  }
</script>
<?php include("./components/footer.php") ?>

<?php
function query($data)
{
  global $conn;
  $result = mysqli_query($conn, $data);
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }
  return $rows;
}
?>