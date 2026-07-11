<?php include 'partials/header.php'; ?>
<?php
$host = "localhost";
$user = "root";       // Username database Anda (default XAMPP biasanya root)
$pass = "";           // Password database Anda (default XAMPP biasanya kosong)
$db   = "dayasatu"; // Ubah dengan nama database yang Anda buat

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>

<?php
define('DAYASATU', true);

require_once 'config/connection.php';
require_once 'includes/product_query.php';
?>

<main class="page" id="home">
  <section class="screen hero-screen">
    <div class="container screen-inner hero-grid">
      <div class="hero-copy hidden-left">
        <div class="eyebrow">
          Pemasok bahan pengecatan • Kalimantan Barat
        </div>
        <h1>CV Daya Satu</h1>
        <p class="lead">
          Berdiri sejak 2010, CV Daya Satu menyediakan bahan pengecatan, cat
          otomotif, tiner, dan produk finishing untuk kebutuhan perlindungan
          serta hasil akhir yang rapi.
        </p>

        <div class="hero-actions">
          <a class="btn btn-primary" href="#produk">Lihat Produk</a>
          <a class="btn btn-secondary" href="#kontak">Hubungi Kami</a>
        </div>
      </div>

      <aside class="hero-card hidden-left delay2">
        <span class="card-kicker">Serve to Protect and Beauty</span>
        <h2>Fokus pada perlindungan dan keindahan hasil kerja.</h2>
        <p>
          Melayani kebutuhan proyek, retail, dan workshop di wilayah
          Kalimantan Barat.
        </p>

        <div class="hero-meta">
          <div class="meta-item">
            <strong>Berdiri</strong>
            <span>2010</span>
          </div>
          <div class="meta-item">
            <strong>Wilayah</strong>
            <span>Kalimantan Barat</span>
          </div>
          <div class="meta-item">
            <strong>Target Kerja</strong>
            <span>Proyek, retail, workshop</span>
          </div>
        </div>
      </aside>
    </div>
  </section>

  <section id="tentang" class="screen section-light">
    <div class="container screen-inner stack">
      <div class="section-head hidden-left">
        <span class="section-tag">Tentang</span>
        <h2>Profil Perusahaan</h2>
      </div>

      <div class="grid-2">
        <article class="card hidden-left">
          <p>
            CV Daya Satu berdiri pada tahun 2010 dan bergerak di bidang
            pemasokan bahan pengecatan seperti tiner, cat otomotif, serta
            berbagai produk finishing. Perusahaan berkembang ke sektor
            dekoratif dan konstruksi besi untuk menjawab kebutuhan pasar
            yang lebih luas.
          </p>

          <div class="chips">
            <span>Tiner</span>
            <span>Cat Otomotif</span>
            <span>Dekoratif</span>
            <span>Konstruksi Besi</span>
            <span>Furnitur</span>
          </div>
        </article>

        <article class="card card-black hidden-left delay2">
          <h3>Nilai Utama</h3>
          <ul class="value-list hidden delay3">
            <li>
              <strong>Melindungi</strong>
              <span>Menjaga substrat agar lebih awet, tahan penggunaan, dan
                tetap stabil.</span>
            </li>
            <li>
              <strong>Memperindah</strong>
              <span>Memberikan hasil akhir yang rapi, bersih, menarik, dan
                konsisten.</span>
            </li>
            <li>
              <strong>Pelayanan</strong>
              <span>
                Responsif, tepat, komunikatif, dan berorientasi solusi.
              </span>
            </li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <section id="visi" class="screen section-dark">
    <div class="container screen-inner stack">
      <div class="section-head section-head-dark hidden-left">
        <span class="section-tag section-tag-dark">Visi & Misi</span>
        <h2>Tujuan yang Jelas, Langkah yang Terarah</h2>
      </div>

      <div class="vision-layout">
        <article class="card card-light vision-card hidden-left">
          <div class="vision-brand">
            <img
              class="vision-logo"
              src="assets/images/banner.png"
              alt="Logo CV Daya Satu" />
          </div>

          <div class="vision-block">
            <h4>Visi</h4>
            <p>
              Menjadi supplier cat otomotif dan finishing yang terpercaya,
              unggul, dan rujukan utama di Kalimantan Barat.
            </p>
          </div>

          <div class="vision-block">
            <h4>Misi</h4>
            <ul class="vision-list">
              <li>
                Menyediakan cat otomotif dan produk finishing yang
                berkualitas.
              </li>
              <li>
                Menjaga ketersediaan produk untuk kebutuhan pelanggan.
              </li>
              <li>Memperluas distribusi di seluruh Kalimantan Barat.</li>
              <li>
                Memberikan pelayanan yang cepat, tepat, dan profesional.
              </li>
              <li>Membangun kerja sama jangka panjang dengan pelanggan.</li>
            </ul>
          </div>
        </article>

        <div class="mission-stack hidden-left">
          <article class="mini-card hidden-right delay2">
            <span>1</span>
            <h3>Produk berkualitas</h3>
            <p>
              Menyediakan material yang mendukung hasil kerja yang tahan
              lama dan presisi.
            </p>
          </article>
          <article class="mini-card hidden-right delay3">
            <span>2</span>
            <h3>Distribusi merata</h3>
            <p>
              Menjangkau area strategis agar produk mudah diakses oleh
              pelanggan.
            </p>
          </article>
          <article class="mini-card hidden-right delay4">
            <span>3</span>
            <h3>Layanan profesional</h3>
            <p>
              Memberi respon cepat, akurat, dan mendukung kebutuhan
              pelanggan secara menyeluruh.
            </p>
          </article>
        </div>
      </div>
    </div>
  </section>

  <section id="produk" class="screen section-light">
    <div class="container screen-inner stack">
      <div class="section-head">
        <span class="section-tag">Produk</span>
        <h2>Produk Unggulan</h2>
        <p>
          Kami menyediakan berbagai brand dan material untuk kebutuhan
          pengecatan dan finishing. <br><br>
        </p>

        <section class="product-filter-container">

          <button
              id="filter-toggle"
              class="filter-toggle">

              <div class="filter-toggle-left">
                  <div class="filter-header">
                      <i class="fa-solid fa-filter"></i>
                      <span id="filter-title">
                          Filter Produk
                      </span>
                  </div>
                  <div id="active-filters" class="active-filters">
                  </div>
              </div>
              <i
                  id="filter-arrow"
                  class="fa-solid fa-chevron-down">
              </i>
          </button>

          <div
              id="filter-content"
              class="filter-content collapsed">

              <div class="filter-group">

                <h3>Brand</h3>

                <div
                    class="product-filter"
                    id="brand-filter">

                    <button
                        class="filter-btn active"
                        data-brand="all">
                        Semua
                    </button>

                    <?php while($brand = mysqli_fetch_assoc($query_brands)): ?>

                        <button
                            class="filter-btn"
                            data-brand="<?= strtolower(htmlspecialchars($brand['name'])) ?>">

                            <?= htmlspecialchars($brand['name']) ?>
                        </button>
                    <?php endwhile; ?>
                </div>
            </div>

            <br>

              <div class="filter-group">

                <h3>Kategori</h3>

                <div
                    class="product-filter"
                    id="category-filter">

                    <button
                        class="filter-btn active"
                        data-category="all">
                        Semua
                    </button>

                    <?php while($category = mysqli_fetch_assoc($query_categories)): ?>

                        <button
                            class="filter-btn"
                            data-category="<?= strtolower(htmlspecialchars($category['name'])) ?>">

                            <?= htmlspecialchars($category['name']) ?>
                        </button>
                    <?php endwhile; ?>
                </div>
              </div>
          </div>
      </section>
      </div>

      <div class="product-grid hidden-left">

        <?php while($product = mysqli_fetch_assoc($query_products)): ?>

        <?php
        $brandFolder = strtolower(trim($product['brand_name']));
        $brandFolder = str_replace(' ', '_', $brandFolder);
        $imagePath = "assets/images/products/$brandFolder/" . $product['image'] . ".png";

        if(!file_exists($imagePath))
        {
            $imagePath = "assets/images/products/no_brand/" . $product['image'];
        }
        ?>

        <article
        class="product-card"
        data-brand="<?= strtolower(htmlspecialchars($product['brand_name'])) ?>"
        data-category="<?= strtolower(htmlspecialchars($product['category_name'])) ?>">

            <div class="product-image">
                <img
                src="<?= $imagePath ?>"
                alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

            <div class="product-body">

                <h3>
                    <?= htmlspecialchars($product['name']) ?>
                </h3>

                <p>
                    <?= htmlspecialchars($product['description']) ?>
                </p>

                <?php if($product['min_price']): ?>

                <div class="product-price">
                    Rp <?= number_format($product['min_price'],0,',','.') ?>
                </div>

                <?php endif; ?>

            </div>
        </article>
        <?php endwhile; ?>
      </div>

      <div id="empty-product" class="empty-product">
        <div class="empty-icon">
              <i class="fas fa-box-open"></i>
        </div>
          <h3>Produk tidak ditemukan</h3>
          <p>
              Coba pilih Brand atau Kategori yang berbeda.
          </p>
      </div>
      <div class="pagination-container">
        <button id="prev-page" class="pagination-btn">
            &laquo;
        </button>
        <div id="pagination-numbers"></div>
        <button id="next-page" class="pagination-btn">
            &raquo;
      </button>
      </div>
</div>
  </section>

  <section class="screen section-dark">
    <div class="container screen-inner stack">
      <div class="section-head section-head-dark hidden-left">
        <span class="section-tag section-tag-dark">Sasaran</span>
        <h2>Target Kerja</h2>
      </div>

      <div class="target-grid">
        <article class="card card-light hidden-left delay2">
          <i class="fa-solid fa-building target-icon"></i>
          <h3>Proyek</h3>
          <p>Bangunan, gudang, dan perumahan.</p>
        </article>

        <article class="card card-light hidden-left delay3">
          <i class="fa-solid fa-store target-icon"></i>
          <h3>Retail</h3>
          <p>Toko bahan bangunan, toko cat, dan retail sejenis.</p>
        </article>

        <article class="card card-light hidden-left delay2">
          <i class="fa-solid fa-screwdriver-wrench target-icon"></i>
          <h3>Workshop & Bengkel</h3>
          <p>
            Teralis besi, karoseri bak truk dan bus, serta bengkel
            authorized.
          </p>
        </article>

        <article class="card card-light hidden-left delay3">
          <i class="fa-solid fa-industry target-icon"></i>
          <h3>Industri Finishing</h3>
          <p>Otomotif, dekoratif, furnitur, dan konstruksi besi.</p>
        </article>
      </div>
    </div>
  </section>

  <section id="distribusi" class="screen section-light">
    <div class="container screen-inner stack hidden-left">
      <div class="section-head">
        <span class="section-tag">Distribusi</span>
        <h2>Jangkauan Kalimantan Barat</h2>
      </div>

      <div class="grid-2">
        <article class="card hidden-left">
          <h3>Wilayah Utama</h3>
          <p>
            Seluruh jalur distribusi diarahkan untuk menjangkau area
            strategis agar produk lebih mudah diakses oleh pelanggan di
            berbagai daerah.
          </p>
        </article>

        <article class="card hidden-left delay2">
          <h3>Target Ketersediaan</h3>
          <p>
            Produk CV Daya Satu ditargetkan tersedia di setiap kabupaten dan
            kota di Kalimantan Barat.
          </p>
        </article>
      </div>

      <div class="chip-bar">
        <span>Proyek</span>
        <span>Retail</span>
        <span>Workshop</span>
        <span>Bengkel Authorized</span>
        <span>Karoseri</span>
        <span>Teralis Besi</span>
      </div>
    </div>
  </section>

  <section id="kontak" class="screen section-dark contact-section">
    <div class="container screen-inner stack hidden-left">
      <div class="section-head section-head-dark">
        <span class="section-tag section-tag-dark">Kontak</span>
        <h2>Siap Menjadi Mitra</h2>
        <p>
          Hubungi kami untuk kebutuhan produk, distribusi, dan kerja sama
          suplai.
        </p>
      </div>

      <div class="cta-strip hidden-left delay2">
        <div class="cta-copy">
          <h3>
            Solusi yang melindungi, memperindah, dan mempermudah kerja.
          </h3>
          <p>
            CV Daya Satu siap mendukung kebutuhan proyek, retail, workshop,
            dan industri finishing dengan layanan yang responsif.
          </p>
        </div>
        <div class="cta-actions">
          <a class="btn btn-primary" href="#form-kontak">Kirim Pesan</a>
          <a class="btn btn-secondary" href="#lokasi">Lihat Lokasi</a>
        </div>
      </div>

      <div class="contact-layout">
        <article
          class="card card-light contact-info-card hidden-left delay2">
          <h3>Informasi Perusahaan</h3>
          <div class="contact-info">
            <div class="contact-item">
              <i class="fa-solid fa-location-dot contact-icon"></i>
              <div>
                <strong>Alamat</strong>
                <span>Kalimantan Barat, Indonesia</span>
              </div>
            </div>

            <div class="contact-item">
              <i class="fa-solid fa-envelope contact-icon"></i>
              <div>
                <strong>Email</strong>
                <span>cvdayasatu@gmail.com</span>
              </div>
            </div>

            <div class="contact-item">
              <i class="fa-solid fa-phone contact-icon"></i>
              <div>
                <strong>Telepon</strong>
                <span>+62 821-5830-0335</span>
              </div>
            </div>

            <div class="contact-item">
              <i class="fa-solid fa-clock contact-icon"></i>
              <div>
                <strong>Jam Operasional</strong>
                <span>Senin - Sabtu, 08.00 - 17.00</span>
              </div>
            </div>
          </div>
        </article>

        <article class="card card-light hidden-left delay3">
          <h3>Hubungi Kami</h3>
          <form
            class="contact-form"
            id="form-kontak"
            action="#"
            method="post">
            <div class="form-row">
              <label for="nama">Nama</label>
              <input
                type="text"
                id="nama"
                name="nama"
                placeholder="Nama Anda"
                required />
            </div>

            <div class="form-row">
              <label for="status">Jenis Pelanggan</label>
              <select id="status" name="status" required>
                <option value="">Pilih salah satu</option>
                <option value="Individu">Individu</option>
                <option value="Perusahaan">Perusahaan</option>
                <option value="Workshop">Workshop</option>
                <option value="Toko">Toko</option>
              </select>
            </div>

            <div class="form-row">
              <label for="kebutuhan">Kebutuhan</label>
              <select id="kebutuhan" name="kebutuhan" required>
                <option value="">Pilih kebutuhan</option>
                <option value="Cat Otomotif">Cat Otomotif</option>
                <option value="Bahan Pengecatan">Bahan Pengecatan</option>
                <option value="Tiner">Tiner</option>
                <option value="Lem">Lem</option>
                <option value="Produk Finishing">Produk Finishing</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>

            <div class="form-row">
              <label for="lokasi">Lokasi / Kota</label>
              <input
                type="text"
                id="lokasi"
                name="lokasi"
                placeholder="Contoh: Pontianak" />
            </div>

            <div class="form-row">
              <label for="pesan">Detail Kebutuhan</label>
              <textarea
                id="pesan"
                name="pesan"
                rows="5"
                placeholder="Jelaskan kebutuhan Anda..."
                required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
              Kirim Pesan
            </button>
          </form>
        </article>
      </div>

      <div class="map-layout hidden-left" id="lokasi">
        <div class="map-head">
          <h3>Lokasi Kami</h3>
          <p>
            Google Maps terhubung untuk memudahkan pelanggan menemukan kami.
          </p>
        </div>
        <div class="map-frame">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d997.4538304656777!2d109.3634113!3d-0.0702444!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e1d5be3b7b2f0b1%3A0xbb1119961a644639!2sCV.DAYA%20SATU!5e0!3m2!1sid!2sid!4v1776075348780!5m2!1sid!2sid"
            width="600"
            height="450"
            style="border: 0"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include 'partials/footer.php'; ?>