<!-- Modal Tambah Film -->
<div class="modal fade" id="tambahFilmModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Movie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Movie Title</label>
                                <input type="text" class="form-control" name="judul_film" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Genre</label>
                                <input type="text" class="form-control" name="genre" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Duration (minutes)</label>
                                <input type="number" class="form-control" name="durasi_menit" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Director</label>
                                <input type="text" class="form-control" name="sutradara" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Age Rating</label>
                                <select class="form-control" name="rating_usia" required>
                                    <option value="SU">All Ages</option>
                                    <option value="13+">13+</option>
                                    <option value="16+">16+</option>
                                    <option value="18+">18+</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Synopsis</label>
                        <textarea class="form-control" name="sinopsis" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="tambah_film" class="btn btn-primary">Save Movie</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Film -->
<div class="modal fade" id="editFilmModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="id_film" id="edit_id_film">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Movie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Movie Title</label>
                                <input type="text" class="form-control" name="judul_film" id="edit_judul" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Genre</label>
                                <input type="text" class="form-control" name="genre" id="edit_genre" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Duration (minutes)</label>
                                <input type="number" class="form-control" name="durasi_menit" id="edit_durasi" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Director</label>
                                <input type="text" class="form-control" name="sutradara" id="edit_sutradara" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Age Rating</label>
                                <select class="form-control" name="rating_usia" id="edit_rating" required>
                                    <option value="SU">All Ages</option>
                                    <option value="13+">13+</option>
                                    <option value="16+">16+</option>
                                    <option value="18+">18+</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Synopsis</label>
                        <textarea class="form-control" name="sinopsis" id="edit_sinopsis" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_film" class="btn btn-primary">Update Movie</button>
                </div>
            </form>
        </div>
    </div>
</div>