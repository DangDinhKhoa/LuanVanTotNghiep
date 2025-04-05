<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h3>BANNER ĐÃ ĐƯỢC TẠO</h3>

            <div class="card mt-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" rows="1" readonly>Tôi muốn tạo một banner về công nghệ cho sự kiện giới thiệu sản phẩm mới</textarea>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label for="topic" class="form-label">Tên chủ đề</label>
                            <input type="text" class="form-control" id="topic" value="Technology" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="width" class="form-label">Chiều dài</label>
                            <input type="text" class="form-control" id="width" value="1200px" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="height" class="form-label">Chiều cao</label>
                            <input type="text" class="form-control" id="height" value="600px" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="quantity" class="form-label">Số lượng</label>
                            <input type="text" class="form-control" id="quantity" value="5" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="created_at" class="form-label">Ngày tạo</label>
                            <input type="text" class="form-control" id="created_at" value="04/04/2025" readonly>
                        </div>
                        <div class="col-md-2 d-flex">
                            <form class="align-self-end" action="#" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-dark"><i class="ti ti-trash"></i> Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Load image -->
                <div class="card-body">
                    <div class="row">
                        @foreach ($images as $image)
                        <div class="col-md-4 mb-3">
                            <div class="image-container">
                                <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" class="img-fluid">
                                <div class="icon-overlay d-flex justify-content-center align-items-center">
                                    <span class="icon mx-2">✏️</span>
                                    <span class="icon mx-2">⬇️</span>
                                    <span class="icon mx-2">🔗</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" rows="1" readonly>Tôi muốn tạo một banner về công nghệ cho sự kiện giới thiệu sản phẩm mới</textarea>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label for="topic" class="form-label">Tên chủ đề</label>
                            <input type="text" class="form-control" id="topic" value="Technology" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="width" class="form-label">Chiều dài</label>
                            <input type="text" class="form-control" id="width" value="1200px" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="height" class="form-label">Chiều cao</label>
                            <input type="text" class="form-control" id="height" value="600px" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="quantity" class="form-label">Số lượng</label>
                            <input type="text" class="form-control" id="quantity" value="5" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="created_at" class="form-label">Ngày tạo</label>
                            <input type="text" class="form-control" id="created_at" value="04/04/2025" readonly>
                        </div>
                        <div class="col-md-2 d-flex">
                            <form class="align-self-end" action="#" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-dark"><i class="ti ti-trash"></i> Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Load image -->
                <div class="card-body">
                    <div class="row">
                        @foreach ($images as $image)
                        <div class="col-md-4 mb-3">
                            <div class="image-container">
                                <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" class="img-fluid">
                                <div class="icon-overlay d-flex justify-content-center align-items-center">
                                    <span class="icon mx-2">✏️</span>
                                    <span class="icon mx-2">⬇️</span>
                                    <span class="icon mx-2">🔗</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Container bao quanh ảnh */
    .image-container {
        position: relative;
    }

    /* Khi hover vào container, hiển thị con trỏ click */
    .image-container:hover {
        cursor: pointer;
    }

    /* Lớp phủ icon */
    .icon-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    /* Hiển thị khi hover */
    .image-container:hover .icon-overlay {
        opacity: 1;
    }

    /* Style cho icon */
    .icon {
        font-size: 24px;
        color: white;
        background: rgba(255, 255, 255, 0.2);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon:hover {
        background: rgba(255, 255, 255, 0.4);
    }
</style>