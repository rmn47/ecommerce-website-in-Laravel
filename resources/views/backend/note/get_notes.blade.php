<div class="modal-header">
    <h5 class="modal-title h6">{{translate('Choose Note')}}</h5>
    <button type="button" class="close" data-dismiss="modal">
    </button>
</div>
<div class="modal-body">
    <div class="modal-body gry-bg px-3 pt-3 mx-auto c-scrollbar-light">
        <div class="align-items-center gutters-5 row">
            @foreach($notes as $key => $note)
                <div class="col-6 col-md-6">
                    <label class="aiz-megabox d-block mb-3">
                        <input type="radio" name="note" value="{{ $note->id }}" id="note_id" onchange="addNote(this.value, '{{ $note->note_type }}')">
                        <input type="hidden" value="{{ $note->getTranslation('description') }}" id="note_description_{{ $note->id }}">
                        <span class="d-block p-3 aiz-megabox-elem">
                            <p class="text-truncate-3 m-0">
                                {{ $note->getTranslation('description') }}
                            </p>
                        </span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
