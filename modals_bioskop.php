<!-- Modal Tambah Bioskop -->
<div class="modal fade" id="tambahBioskopModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Cinema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Cinema Name</label>
                        <input type="text" class="form-control" name="nama_bioskop" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Complete Address</label>
                        <textarea class="form-control" name="alamat_lengkap" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="kota" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="no_telepon" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Open Time</label>
                                <input type="time" class="form-control" name="jam_buka" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Close Time</label>
                                <input type="time" class="form-control" name="jam_tutup" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="tambah_bioskop" class="btn btn-primary">Save Cinema</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Studio -->
<div class="modal fade" id="tambahStudioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Studio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Cinema</label>
                        <select class="form-control" name="id_bioskop" required>
                            <option value="">-- Select Cinema --</option>
                            <?php 
                            $bioskops = mysqli_query($conn, "SELECT * FROM bioskop ORDER BY nama_bioskop");
                            while ($bioskop = mysqli_fetch_assoc($bioskops)): 
                            ?>
                                <option value="<?= $bioskop['id_bioskop'] ?>"><?= $bioskop['nama_bioskop'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Studio Name</label>
                        <input type="text" class="form-control" name="nama_studio" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Studio Type</label>
                                <select class="form-control" name="tipe_studio" required>
                                    <option value="regular">Regular</option>
                                    <option value="vip">VIP</option>
                                    <option value="premium">Premium</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Capacity</label>
                                <input type="number" class="form-control" name="kapasitas_total" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Number of Rows</label>
                                <input type="number" class="form-control" name="jumlah_baris" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Seats per Row</label>
                                <input type="number" class="form-control" name="kursi_per_baris" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="tambah_studio" class="btn btn-primary">Save Studio</button>
                </div>
            </form>
        </div>
    </div>
</div>