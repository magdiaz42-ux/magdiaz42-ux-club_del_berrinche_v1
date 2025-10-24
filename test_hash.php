<?php
$hash = '$2y$10$5j5NPUe.EaPjRGpi6vZB2eeIEZIvTxRmkiUSOZDyZQRS1c3QltJie';
$password = '12345';

if (password_verify($password, $hash)) {
  echo "✅ Coincide correctamente";
} else {
  echo "❌ No coincide";
}
?>
