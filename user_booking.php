<?php
// ... (PHP Bagian Atas SAMA PERCIS, copy dari jawaban sebelumnya) ...
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') { header("Location: login.php"); exit(); }
if (!isset($_GET['id_film'])) { header("Location: user_home.php"); exit(); }

$id_film = $_GET['id_film'];
$id_user = $_SESSION['id_pengguna'];

$q_film = mysqli_query($conn, "SELECT * FROM film WHERE id_film=$id_film");
if(mysqli_num_rows($q_film) == 0) { header("Location: user_home.php"); exit(); }
$film = mysqli_fetch_assoc($q_film);

$vouchers = mysqli_query($conn, "SELECT pr.*, r.nama_reward, r.nilai_diskon FROM penukaran_reward pr JOIN reward r ON pr.id_reward = r.id_reward WHERE pr.id_pengguna = $id_user AND pr.status_penukaran = 'active'");

if (isset($_POST['confirm_payment'])) {
    // ... (LOGIKA PHP SUBMIT SAMA PERCIS, COPY AJA DR SEBELUMNYA) ...
    $id_jadwal = $_POST['id_jadwal_final'];
    $seats_str = $_POST['selected_seats_final'];
    $id_penukaran = $_POST['id_penukaran_final'];
    
    if (empty($id_jadwal) || empty($seats_str)) {
        echo "<script>alert('Data booking tidak valid!'); window.location='user_booking.php?id_film=$id_film';</script>";
        exit;
    }
    $seats = explode(',', $seats_str);
    $qty = count($seats);
    $q_jadwal = mysqli_query($conn, "SELECT harga_reguler, kursi_tersedia FROM jadwal_tayang WHERE id_jadwal=$id_jadwal");
    $j = mysqli_fetch_assoc($q_jadwal);
    $subtotal = $j['harga_reguler'] * $qty;
    $potongan = 0;
    if (!empty($id_penukaran)) {
        $q_voc = mysqli_query($conn, "SELECT r.id_reward, r.nilai_diskon, r.nama_reward FROM penukaran_reward pr JOIN reward r ON pr.id_reward = r.id_reward WHERE pr.id_penukaran = $id_penukaran");
        if(mysqli_num_rows($q_voc) > 0) {
            $voc = mysqli_fetch_assoc($q_voc);
            if (stripos($voc['nama_reward'], 'Gratis') !== false || stripos($voc['nama_reward'], 'Free') !== false) {
                $potongan = $subtotal;
            } else if (strpos($voc['nama_reward'], '%') !== false) {
                $persen = (int) filter_var($voc['nama_reward'], FILTER_SANITIZE_NUMBER_INT);
                $potongan = ($subtotal * $persen) / 100;
            } else {
                $potongan = $voc['nilai_diskon'];
            }
        }
    }
    $total_bayar = $subtotal - $potongan;
    if ($total_bayar < 0) $total_bayar = 0;
    $tgl_trx = date('Y-m-d H:i:s');
    $kode = "B-" . time() . rand(100,999);
    $query = "INSERT INTO pemesanan (id_pengguna, id_jadwal, id_reward, tanggal_pemesanan, jumlah_tiket, total_bayar, status_pemesanan, kode_booking) VALUES ($id_user, $id_jadwal, " . ($id_penukaran ? $voc['id_reward'] : "NULL") . ", '$tgl_trx', $qty, $total_bayar, 'confirmed', '$kode')";
    if (mysqli_query($conn, $query)) {
        $last_id = mysqli_insert_id($conn);
        mysqli_query($conn, "UPDATE jadwal_tayang SET kursi_tersedia = kursi_tersedia - $qty WHERE id_jadwal=$id_jadwal");
        if (!empty($id_penukaran)) mysqli_query($conn, "UPDATE penukaran_reward SET status_penukaran = 'used' WHERE id_penukaran=$id_penukaran");
        $poin_dapat = $qty * 5;
        mysqli_query($conn, "UPDATE pengguna SET total_poin = total_poin + $poin_dapat WHERE id_pengguna=$id_user");
        echo "<script>window.location='booking_success.php?id=$last_id';</script>"; exit;
    }
}

// 6. Ambil Data Jadwal TERMASUK DIMENSI STUDIO (Rows & Cols)
$raw_schedules = mysqli_query($conn, "
    SELECT j.id_jadwal, j.tanggal_tayang, j.jam_mulai, j.harga_reguler, j.kursi_tersedia, 
           s.nama_studio, s.tipe_studio, s.jumlah_baris, s.kursi_per_baris, 
           b.id_bioskop, b.nama_bioskop, b.kota
    FROM jadwal_tayang j 
    JOIN studio s ON j.id_studio = s.id_studio 
    JOIN bioskop b ON s.id_bioskop = b.id_bioskop 
    WHERE j.id_film = $id_film AND j.tanggal_tayang >= CURDATE() AND j.status_jadwal='active'
    ORDER BY j.tanggal_tayang ASC, j.jam_mulai ASC
");

$data_jadwal = [];
$list_bioskop = [];
$list_tanggal = [];

while($row = mysqli_fetch_assoc($raw_schedules)) {
    $data_jadwal[] = $row;
    $list_bioskop[$row['id_bioskop']] = $row['nama_bioskop'] . " - " . $row['kota'];
    $list_tanggal[$row['tanggal_tayang']] = date('d F Y', strtotime($row['tanggal_tayang']));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking • CinemaPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .time-slot { cursor: pointer; transition: all 0.2s; border: 2px solid #e2e8f0; background: white; }
        .time-slot:hover { border-color: var(--primary); background: #eff6ff; }
        .time-slot.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .seat-checkbox:checked + label { background-color: var(--primary); color: white; border-color: var(--primary); }
    </style>
</head>
<body style="background-color: #f8f9fa;">
    <?php include 'user_header.php'; ?>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded shadow-sm">
            <div><h2 class="fw-bold mb-0">Book Ticket</h2><p class="text-muted mb-0">Select your preferred schedule</p></div>
            <a href="user_home.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold py-3"><i class="bi bi-sliders me-2"></i> 1. Choose Cinema & Date</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">CINEMA LOCATION</label>
                                <select id="filter_bioskop" class="form-select" onchange="renderTimes()"><option value="">-- Select Cinema --</option>
                                    <?php foreach($list_bioskop as $id => $nama): ?><option value="<?= $id ?>"><?= $nama ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">SHOW DATE</label>
                                <select id="filter_tanggal" class="form-select" onchange="renderTimes()"><option value="">-- Select Date --</option>
                                    <?php foreach($list_tanggal as $val => $disp): ?><option value="<?= $val ?>"><?= $disp ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="form-label small fw-bold text-muted">AVAILABLE TIME</label>
                            <div id="time_slots_container" class="d-flex flex-wrap gap-2"><div class="text-muted fst-italic small">Please select Cinema and Date first.</div></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm" id="seat_section" style="display:none;">
                    <div class="card-header bg-white fw-bold py-3"><i class="bi bi-grid-3x3-gap me-2"></i> 2. Pick Seats</div>
                    <div class="card-body">
                        <div class="screen bg-secondary text-white text-center rounded py-1 mb-4 shadow-sm" style="width: 100%;">SCREEN</div>
                        <div class="d-flex flex-column align-items-center gap-2" id="seat_map_container">
                            </div>
                        <div class="text-center mt-3 small text-muted">
                            <span class="me-3"><i class="bi bi-square text-secondary"></i> Available</span>
                            <span><i class="bi bi-square-fill text-primary"></i> Selected</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><?= $film['judul_film'] ?></h5>
                        <div id="summary_content" class="text-muted small mb-3">Select a schedule to see details.</div>
                        <hr>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">APPLY VOUCHER</label>
                            <div id="voucher_warning" class="alert alert-warning py-1 px-2 small mb-2" style="display:none;">
                                <i class="bi bi-exclamation-circle"></i> Vouchers only for Regular studios.
                            </div>
                            <select id="voucher_select" class="form-select form-select-sm" onchange="calculateTotal()">
                                <option value="" data-disc="0" data-type="flat">No Voucher</option>
                                <?php while($v = mysqli_fetch_assoc($vouchers)): 
                                    $is_free = stripos($v['nama_reward'], 'Gratis') !== false;
                                    $is_percent = strpos($v['nama_reward'], '%') !== false;
                                    $type = $is_free ? 'free' : ($is_percent ? 'percent' : 'flat');
                                    $val = $is_free ? 0 : ($is_percent ? (int)filter_var($v['nama_reward'], FILTER_SANITIZE_NUMBER_INT) : $v['nilai_diskon']);
                                ?>
                                <option value="<?= $v['id_penukaran'] ?>" data-type="<?= $type ?>" data-val="<?= $val ?>"><?= $v['nama_reward'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between mb-1 small"><span>Subtotal</span><span class="fw-bold">Rp <span id="txt_subtotal">0</span></span></div>
                        <div class="d-flex justify-content-between mb-3 small text-success"><span>Discount</span><span>- Rp <span id="txt_discount">0</span></span></div>
                        <div class="d-flex justify-content-between border-top pt-3"><span class="fw-bold fs-5">Total</span><span class="fw-bold fs-5 text-primary">Rp <span id="txt_total">0</span></span></div>
                        <button id="btn_checkout" onclick="openCheckoutModal()" class="btn btn-primary w-100 mt-4 py-2 fw-bold" disabled>Checkout Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0"><h5 class="modal-title fw-bold">Confirm Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="alert alert-light border">
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Movie</span><span class="fw-bold text-end"><?= $film['judul_film'] ?></span></div>
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Cinema</span><span class="fw-bold text-end" id="mod_cinema">-</span></div>
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Date/Time</span><span class="fw-bold text-end" id="mod_datetime">-</span></div>
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Seats</span><span class="fw-bold text-end" id="mod_seats">-</span></div>
                        <hr>
                        <div class="d-flex justify-content-between"><span class="fw-bold fs-5">Total Pay</span><span class="fw-bold fs-5 text-primary" id="mod_total">Rp 0</span></div>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="id_jadwal_final" id="inp_jadwal">
                        <input type="hidden" name="selected_seats_final" id="inp_seats">
                        <input type="hidden" name="id_penukaran_final" id="inp_voucher">
                        <div class="d-grid"><button type="submit" name="confirm_payment" class="btn btn-primary py-2 fw-bold">Pay & Book Ticket</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const rawData = <?php echo json_encode($data_jadwal); ?>;
        let selectedSchedule = null;

        function renderTimes() {
            const bioId = document.getElementById('filter_bioskop').value;
            const dateVal = document.getElementById('filter_tanggal').value;
            const container = document.getElementById('time_slots_container');
            const seatSec = document.getElementById('seat_section');

            selectedSchedule = null;
            seatSec.style.display = 'none';
            document.querySelectorAll('.seat-checkbox').forEach(cb => cb.checked = false);
            calculateTotal();

            if (!bioId || !dateVal) {
                container.innerHTML = '<div class="text-muted fst-italic small">Please select Cinema and Date first.</div>';
                return;
            }

            const filtered = rawData.filter(item => String(item.id_bioskop) === String(bioId) && item.tanggal_tayang === dateVal);

            if (filtered.length === 0) {
                container.innerHTML = '<div class="text-danger small fw-bold">No schedules found.</div>';
                return;
            }

            let html = '';
            filtered.forEach(item => {
                let priceFmt = parseInt(item.harga_reguler).toLocaleString('id-ID');
                let timeFmt = item.jam_mulai.substring(0, 5);
                html += `<div class="time-slot px-3 py-2 rounded text-center" onclick="selectTime(this, ${item.id_jadwal})"><div class="fw-bold fs-5">${timeFmt}</div><div class="small text-uppercase" style="font-size: 0.7rem;">${item.tipe_studio}</div><div class="small fw-bold text-primary">Rp ${priceFmt}</div></div>`;
            });
            container.innerHTML = html;
        }

        // 3. Render Kursi Dinamis (Baris x Kolom)
        function renderSeats(rows, cols) {
            const container = document.getElementById('seat_map_container');
            const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            let html = '';

            for(let i = 0; i < rows; i++) {
                let rowHtml = '<div class="d-flex gap-2 justify-content-center">';
                let rowChar = alphabet[i];
                
                for(let j = 1; j <= cols; j++) {
                    let seatCode = rowChar + j;
                    rowHtml += `
                        <input type='checkbox' class='btn-check seat-checkbox' id='seat${seatCode}' name='seats[]' value='${seatCode}' onclick='calculateTotal()'>
                        <label class='btn btn-outline-secondary p-0 d-flex align-items-center justify-content-center' for='seat${seatCode}' style='width:35px; height:35px; font-size:0.8rem; border-radius:6px;'>${seatCode}</label>
                    `;
                }
                rowHtml += '</div>';
                html += rowHtml;
            }
            container.innerHTML = html;
        }

        function selectTime(el, idJadwal) {
            document.querySelectorAll('.time-slot').forEach(div => div.classList.remove('active'));
            el.classList.add('active');
            selectedSchedule = rawData.find(item => item.id_jadwal == idJadwal);
            
            // Render Kursi sesuai Data Studio
            renderSeats(selectedSchedule.jumlah_baris, selectedSchedule.kursi_per_baris);
            
            document.getElementById('seat_section').style.display = 'block';
            document.getElementById('seat_section').scrollIntoView({ behavior: 'smooth', block: 'center' });
            calculateTotal();
        }

        function calculateTotal() {
            const summContent = document.getElementById('summary_content');
            const txtSub = document.getElementById('txt_subtotal');
            const txtDisc = document.getElementById('txt_discount');
            const txtTot = document.getElementById('txt_total');
            const btn = document.getElementById('btn_checkout');
            const vocSelect = document.getElementById('voucher_select');
            const vocWarn = document.getElementById('voucher_warning');

            if (!selectedSchedule) return;

            let tglIndo = new Date(selectedSchedule.tanggal_tayang).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'});
            summContent.innerHTML = `<div class="fw-bold text-dark">${selectedSchedule.nama_bioskop}</div><div class="small text-muted">${selectedSchedule.nama_studio} (${selectedSchedule.tipe_studio})</div><div class="small text-primary mt-1"><i class="bi bi-calendar-event"></i> ${tglIndo} - ${selectedSchedule.jam_mulai.substring(0,5)}</div>`;

            // LOGIKA VOUCHER HANYA REGULAR
            if (selectedSchedule.tipe_studio.toLowerCase() !== 'regular') {
                vocSelect.disabled = true;
                vocSelect.value = "";
                vocWarn.style.display = 'block';
            } else {
                vocSelect.disabled = false;
                vocWarn.style.display = 'none';
            }

            const seats = document.querySelectorAll('.seat-checkbox:checked');
            const qty = seats.length;
            const price = parseInt(selectedSchedule.harga_reguler);
            const subtotal = qty * price;

            let discount = 0;
            if (vocSelect.value && qty > 0) {
                const selectedOption = vocSelect.options[vocSelect.selectedIndex];
                const discType = selectedOption.getAttribute('data-type');
                const discVal = parseInt(selectedOption.getAttribute('data-val') || 0);
                
                if (discType === 'free') discount = subtotal;
                else if (discType === 'percent') discount = (subtotal * discVal) / 100;
                else discount = discVal;
                
                if (discount > subtotal) discount = subtotal;
            }

            let total = subtotal - discount;
            txtSub.innerText = subtotal.toLocaleString('id-ID');
            txtDisc.innerText = discount.toLocaleString('id-ID');
            txtTot.innerText = total.toLocaleString('id-ID');
            btn.disabled = (qty === 0);
        }

        function openCheckoutModal() {
            const seats = Array.from(document.querySelectorAll('.seat-checkbox:checked')).map(cb => cb.value);
            const totalStr = document.getElementById('txt_total').innerText;
            document.getElementById('mod_cinema').innerText = selectedSchedule.nama_bioskop;
            document.getElementById('mod_datetime').innerText = selectedSchedule.jam_mulai.substring(0,5);
            document.getElementById('mod_seats').innerText = seats.join(', ');
            document.getElementById('mod_total').innerText = "Rp " + totalStr;
            document.getElementById('inp_jadwal').value = selectedSchedule.id_jadwal;
            document.getElementById('inp_seats').value = seats.join(',');
            document.getElementById('inp_voucher').value = document.getElementById('voucher_select').value;
            new bootstrap.Modal(document.getElementById('checkoutModal')).show();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>