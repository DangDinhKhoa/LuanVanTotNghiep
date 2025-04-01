<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Banner đã được tạo</h3>
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
    </div>
</div>