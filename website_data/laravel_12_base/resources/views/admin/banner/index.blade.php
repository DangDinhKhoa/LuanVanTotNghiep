<div class="row">
    <div class="col-12">
        <h2 class="text-center">BANNER ĐÃ ĐƯỢC TẠO</h2>
        <div class="card mt-4">
            <div class="card-header">
                <div class="row">
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" rows="3" readonly>Tôi muốn tạo một banner về công nghệ cho sự kiện giới thiệu sản phẩm mới</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="topic" class="form-label">Tên chủ đề</label>
                        <input type="text" class="form-control" id="topic" value="Technology" readonly>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="width" class="form-label">Chiều dài</label>
                            <input type="text" class="form-control" id="width" value="1200px" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="height" class="form-label">Chiều cao</label>
                            <input type="text" class="form-control" id="height" value="600px" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Số lượng</label>
                            <input type="text" class="form-control" id="quantity" value="5" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="created_at" class="form-label">Ngày tạo</label>
                            <input type="text" class="form-control" id="created_at" value="04/04/2025" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($images as $image)
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card">
                            <img src="{{ $image['url'] }}" class="card-img-top" alt="{{ $image['alt'] }}">
                            <div class="card-body">
                                <p class="card-text text-center">{{ $image['alt'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>        
            </div>
        </div>
        <a href="{{ route('user_table_get') }}" class="btn btn-secondary">QUAY LẠI</a>
    </div>
</div>