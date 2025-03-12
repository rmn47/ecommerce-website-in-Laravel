@extends('seller.layouts.app')
@section('panel_content')

<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                #{{ $conversation->title.' ('.translate('Between').' '.$conversation->receiver?->name.' '.translate('and').' '.$conversation->sender?->name.')' }}
                <br>
                <a href="{{ route('preorder-product.details', $conversation->preorderProduct->product_slug) }}" class="fs-14 px-0 text-danger" target="_blank">
                    {{ $conversation->preorderProduct->getTranslation('product_name') }}
                </a>
            </h5>
        </div>

        <div class="card-body">
            <div id="messages">
                @include('preorder.common.messages', ['conversation', $conversation])
            </div>
            <form action="{{ route('seller.preorder-conversations.reply') }}" method="POST">
                @csrf
                <input type="hidden" name="conversation_thread_id" value="{{ $conversation->id }}">
                <div class="row">
                    <div class="col-md-12">
                        <textarea class="form-control" rows="4" name="message" placeholder="{{ translate('Type your reply') }}" required></textarea>
                    </div>
                </div>
                <br>
                <div class="text-right">
                    <button type="submit" class="btn btn-info">{{translate('Send')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
    function refresh_messages(){
        $.post('{{ route('preorder.conversations.refresh') }}', {_token:'{{ @csrf_token() }}', id:'{{ encrypt($conversation->id) }}'}, function(data){
            $('#messages').html(data);
        })
    }

    refresh_messages(); // This will run on page load
    setInterval(function(){
        refresh_messages() // this will run after every 4 seconds
    }, 5000);
    </script>
@endsection
