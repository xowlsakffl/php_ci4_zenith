<div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">회원</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="postForm" method="post">
                
                <div class="d-grid text-center">
                    <img class="mb-3" id="preview" alt="Preview Image" src="/img/previewImage.png" />
                </div>

                <div class="mb-3">
                    <input type="file" name="file" id="fileInput" multiple="true" class="form-control form-control-lg" onChange="onFileUpload(this)">
                    <span id="error_file" class="text-danger ms-3"></span>
                </div>

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control title">
                    <span id="error_title" class="text-danger ms-3"></span>
                </div>

                <div class="form-group">
                    <label for="body">Body</label>
                    <textarea name="body" class="form-control body" id="" cols="30" rows="10"></textarea>
                    <span id="error_body" class="text-danger ms-3"></span>
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input type="text" name="slug" class="form-control slug">
                    <span id="error_slug" class="text-danger ms-3"></span>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-dark ajaxpost-save">Add Post</button>
        </div>
        </div>
    </div>
</div>