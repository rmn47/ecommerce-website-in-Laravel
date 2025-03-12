<div class="modal-header">
    <h5 class="modal-title fw-600 h5">
        {{ translate('Any query about this product') }}
        @if($conversation)
            <a href="#" class="btn btn-link btn-sm px-0 fs-12" target="_blank">
                {{ translate('View Previous Conversations') }}<i class="las la-arrow-right"></i>
            </a>
        @endif
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form class="" action="{{ route('preorder.conversations.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <div class="modal-body gry-bg px-3 pt-3">
        <div class="form-group">
            <input type="text" class="form-control mb-3 rounded-0" name="title"
                value="{{ $conversation ?  $conversation->title : $product->product_name }}" 
                placeholder="{{ translate('Product Name') }}" 
                @if($conversation) readonly @endif required>
        </div>
        <div class="form-group">
            <textarea class="form-control rounded-0" rows="8" name="message" required
                placeholder="{{ translate('Your Question') }}"></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-primary fw-600 rounded-0" data-dismiss="modal">{{translate('Cancel') }}</button>
        <button type="submit" class="btn btn-primary fw-600 rounded-0 w-100px">{{ translate('Send')}}</button>
    </div>
</form>