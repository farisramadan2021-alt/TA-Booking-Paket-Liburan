<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Booking Paket Wisata</title>
</head>
<body>
    <h2>Form Booking Paket Wisata</h2>
    <form>
        <label for="nama">Nama Lengkap</label>
           <input type="text" id="nama" name="nama" required>
            <br>
        <label for="email">Email</label>
           <input type="email" id="email" name="email" required>
            <br>
        <label for="tlp">No. Telephone</label>
            <input type="tel" id="tlp" name="tlp" required>
            <br>
           <label for="paket">Pilihan Paket Wisata</label>
            <select name="paket" id="paket" required>
              <option value="">----- Pilih Paket Wisata -----</option>
              <option value="bali">Paket Wisata Bali</option>
              <option value="Bromo">Paket Wisata Bromo</option>
              <option value="jogja">Paket Wisata jogja</option>
              </select>
            <br>
            <label for="tanggal">Tanggal Keberangkatan</label>
            <input type="date" id="tanggal" name="tanggal" required>
            <br>
             <label for="jumlah_peserta">Jumlah Peserta</label>
                <input type="number" id="jumlah_peserta" name="jumlah_peserta" min="1" required>
             <br>
        <button type="submit">Booking Sekarang</button>
    </form>
</body>
</html>