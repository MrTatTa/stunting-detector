<?php
require_once "../config.php";

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) exit;

$getPred = mysqli_query($conn, "SELECT id FROM prediksi WHERE ibu_id = $id");

while ($row = mysqli_fetch_assoc($getPred)) {
  mysqli_query($conn, "DELETE FROM faktor_risiko WHERE prediksi_id = " . $row['id']);
}

mysqli_query($conn, "DELETE FROM prediksi WHERE ibu_id = $id");
mysqli_query($conn, "DELETE FROM ibu_hamil WHERE id = $id");

/* balik ke halaman sebelumnya */
echo "<script>
  sessionStorage.setItem('toast_success', 'Data berhasil dihapus');
  window.history.back();
</script>";
exit;
