<div class="border mb-4 p-2 mt-4 rounded-2">

    <div class="section-wrapper px-3 mt-2">
        <div class="top d-flex justify-content-between">
            <div class="fw-16">
                <p class="fs-16 "><b class="text-uppercase">{{translate('refund')}}</b></p>
            </div>
            <div>
                <i class="las la-info-circle fs-16 opacity-60"></i>
            </div>
        </div>
        <div class="free-shipping badge-cool-blue p-2 rounded ">
                <p class="m-0 p-0 text-white"><i class="las la-check fs-10 rounded-3 p-1 bg-white preorder-text-cool-blue" ></i></i> <span class="ml-2">{{translate('Refund Available
                for this product')}}</span></p>
        </div>
        <div class="mt-2">
            @if($product->preorder_refund?->note?->description != null && $product->preorder_refund->show_refund_note)
            <p id="text-{{ $product->preorder_refund?->note?->id }}" class="preorder-text-light-grey fs-14">
                <span id="short-text-{{ $product->preorder_refund?->note?->id }}">
                    {{ Str::limit($product->preorder_refund?->note?->description, 100) }} 
                </span>
                <span class="d-none preorder-text-light-grey fs-14" id="full-text-{{ $product->preorder_refund?->note?->id }}">{{ $product->preorder_refund?->note?->description }}</span>
                @if (strlen($product->preorder_refund?->note?->description) > 100)
                <a href="javascript:void(0);" onclick="toggleText({{ $product->preorder_refund?->note?->id }})" id="toggle-link-{{ $product->preorder_refund?->note?->id }}">{{translate('See More')}}</a>
                @endif
            </p>
            @endif
        </div>
    </div>

</div>


<script>
function toggleText(id) {
    const shortText = document.getElementById(`short-text-${id}`);
    const fullText = document.getElementById(`full-text-${id}`);
    const toggleLink = document.getElementById(`toggle-link-${id}`);

    if (fullText.classList.contains('d-none')) {
        shortText.classList.add('d-none'); 
        fullText.classList.remove('d-none'); 
        toggleLink.textContent = 'See Less'; 
    } else {
        shortText.classList.remove('d-none'); 
        fullText.classList.add('d-none'); 
        toggleLink.textContent = 'See More'; 
    }
}
    
</script>