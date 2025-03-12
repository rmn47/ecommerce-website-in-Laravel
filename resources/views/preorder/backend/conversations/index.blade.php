@extends('backend.layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Conversations')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th data-breakpoints="lg">{{ translate('Date') }}</th>
                    <th data-breakpoints="lg">{{translate('Title')}}</th>
                    <th>{{translate('Sender')}}</th>
                    <th>{{translate('Receiver')}}</th>
                    <th width="10%" class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($conversations as $key => $conversation)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{ $conversation->created_at }}</td>
                        <td>{{ $conversation->title }}</td>
                        <td>
                            @if($conversation->sender != null)
                                {{ $conversation->sender?->name }}
                                @php
                                    $customerUnreadConversations = $conversation->messages()->where('sender_id', '!=' , $conversation->sender_id)->whereReceiverViewed(0)->count();
                                @endphp 
                                @if ($customerUnreadConversations > 0)
                                    <span class="badge badge-danger ml-1">{{ $customerUnreadConversations }}</span>
                                @endif
                            @else
                                {{ translate('Customer Not Found') }}
                            @endif
                        </td>
                        <td>
                            @if($conversation->receiver != null)
                                {{ $conversation->receiver?->name }}
                                @php
                                    $customerUnreadConversations = $conversation->messages()->where('sender_id', '!=' , $conversation->receiver_id)->whereReceiverViewed(0)->count();
                                @endphp 
                                @if ($customerUnreadConversations > 0)
                                    <span class="badge badge-danger ml-1">{{ $customerUnreadConversations }}</span>
                                @endif
                            @else
                                {{ translate('Seller Not Found') }}
                            @endif

                        <td class="text-right">
                            @can('view_detail_preorder_product_conversation')
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('preorder-conversations.admin_show', encrypt($conversation->id))}}" title="{{ translate('View') }}">
                                    <i class="las la-eye"></i>
                                </a>
                            @endcan
                            @can('delete_preorder_product_conversation')
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('preorder-conversations.destroy', encrypt($conversation->id))}}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $conversations->links() }}
      </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
