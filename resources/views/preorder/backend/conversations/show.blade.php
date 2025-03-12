@extends('backend.layouts.app')

@section('content')

<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                #{{ $conversation->title.' ('.translate('Between').' '.$conversation->receiver?->name.' '.translate('and').' '.$conversation->sender?->name.')' }}
                <br>
                <a href="{{ route('preorder-product.details', $conversation->preorderProduct->product_slug) }}" class="btn btn-link btn-sm px-0 fs-14" target="_blank">
                    {{ $conversation->preorderProduct->getTranslation('product_name') }}
                </a>
            </h5>
        </div>

        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach($conversation->messages as $message)
                    <li class="list-group-item px-0">
                        <div class="media mb-2">
                          <img class="avatar avatar-xs mr-3" @if($message->sender != null) src="{{ uploaded_asset($message->sender->avatar_original) }}" @endif onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                          <div class="media-body">
                            <h6 class="mb-0 fw-600">
                                @if ($message->user != null)
                                    {{ $message->user?->name }}
                                @endif
                            </h6>
                            <p class="opacity-50">{{$message->created_at}}</p>
                          </div>
                        </div>
                        <p>
                            {{ $message->message }}
                        </p>
                    </li>
                @endforeach
            </ul>
            @if (($conversation->preorderProduct->user_id == get_admin()->id) && auth()->user()->can('reply_preorder_product_conversation'))
                <form action="{{ route('preorder-conversations.admin_reply') }}" method="POST">
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
            @endif
        </div>
    </div>
</div>

@endsection
