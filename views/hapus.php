<?php
require_once "../config.php";

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) exit;

/* Ambil semua prediksi terkait */
$getPred = mysqli_query($conn, "
  SELECT id FROM prediksi
  WHERE ibu_id = $id AND deleted_at IS NULL
");

/* Soft delete faktor_risiko */
while ($row = mysqli_fetch_assoc($getPred)) {
  mysqli_query(
    $conn,
    "
    UPDATE faktor_risiko
    SET deleted_at = NOW()
    WHERE prediksi_id = " . $row['id']
  );
}

/* Soft delete prediksi */
mysqli_query($conn, "
  UPDATE prediksi
  SET deleted_at = NOW()
  WHERE ibu_id = $id
");

/* Soft delete ibu_hamil */
mysqli_query($conn, "
  UPDATE ibu_hamil
  SET deleted_at = NOW()
  WHERE id = $id
");

/* Kembali + Toast */
echo "<script>
  sessionStorage.setItem('toast_success', 'Data berhasil dihapus');
  window.location.href = document.referrer;
</script>";
exit;
