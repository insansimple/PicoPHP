<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            color: #333;
            text-align: center;
            padding: 50px;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            font-size: 80px;
            color: #e74c3c;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .buttons a {
            display: inline-block;
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin: 0 10px;
            transition: background-color 0.3s ease;
        }

        .buttons a:hover {
            background-color: #2980b9;
        }

        .buttons a.back-button {
            background-color: #6c757d;
            /* Abu-abu untuk tombol kembali */
        }

        .buttons a.back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>404</h1>
        <h2>Halaman Tidak Ditemukan</h2>
        <p>Maaf, halaman yang Anda cari tidak ada. Mungkin URL salah ketik, atau halaman telah dihapus.</p>
        <div class="buttons">
            <a href="#" onclick="history.back(); return false;" class="back-button">Kembali ke Halaman Sebelumnya</a>

            <a href="<?php echo base_url(); ?>">Kembali ke Beranda</a>
        </div>
    </div>
</body>

</html>