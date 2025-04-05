<aside class="left-sidebar" style="width: 25%;">
    <div class="brand-logo d-flex align-items-center justify-content-between">
        <a href="#" class="text-nowrap logo-img">
            <img src="../assets/images/logos/dark-logo.svg" width="180" alt="" />
        </a>
        <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
        </div>
    </div>

    <nav class="sidebar-nav scroll-sidebar mt-4">
        <form id="bannerForm" class="row">
            <!-- Textarea cho mô tả -->
            <div class="mb-4">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" rows="4" placeholder="Nhập mô tả" required></textarea>
            </div>

            <!-- Chọn chủ đề (có thể nhập) -->
            <!-- Ô xổ xuống chọn chủ đề -->
            <div class="mb-4">
                <label for="topic" class="form-label">Chủ đề</label>
                <input class="form-control" list="topicList" id="topic" required placeholder="Chọn hoặc nhập chủ đề" required>
                <datalist id="topicList">
                    <option value="Công nghệ">
                    <option value="Thời trang">
                    <option value="Ẩm thực">
                    <option value="Du lịch">
                </datalist>
                <div class="invalid-feedback">Vui lòng chọn hoặc nhập chủ đề.</div>
            </div>

            <!-- Chiều dài và chiều rộng (trên cùng 1 dòng) -->
            <div class="row mb-4">
                <div class="col-5">
                    <label for="width" class="form-label">Chiều rộng</label>
                    <input type="number" class="form-control" id="width" value="768" min="512" max="1536" placeholder="Chiều rộng" required>
                </div>
                <div class="col-5">
                    <label for="height" class="form-label">Chiều dài</label>
                    <input type="number" class="form-control" id="height" value="1376" min="512" max="1536" placeholder="Chiều dài" required>
                </div>
            </div>

            <!-- Số lượng -->
            <div class="mb-4">
                <label for="quantity" class="form-label">Số lượng</label>
                <input type="number" class="form-control" id="quantity" value="4" min="1" max="8" placeholder="Số lượng" required>
            </div>
            <!-- Nút tạo banner -->
            <div class="col-12">
                <button type="submit" class="btn w-100 text-white p-3 fs-3" id="createBannerBtn"
                    style="background: linear-gradient(122deg, rgb(250, 85, 96) 0.01%, rgb(177, 75, 244) 49.9%, rgb(77, 145, 255) 100%);"><i class="ti ti-star mx-2"></i> TẠO BANNER</button>
            </div>
        </form>
    </nav>
</aside>