<?php

namespace App\Http\Controllers\Seller;

use App\Enums\NoteType as EnumsNoteType;
use App\Models\Note;
use App\Models\NoteTranslation;
use Illuminate\Http\Request;
use Redirect;
use Validator;

class NoteController extends Controller
{
    public function __construct() {
        $this->note_rules = [
            'description' => ['required','max:900'],
        ];

        $this->note_messages = [
            'description.required' => translate('Note description is required'),
            'description.max'  => translate('Max 900 character'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort_search =null;
        $notes =  Note::where('user_id', auth()->id())
                        ->orWhere(function ($query){
                            $query->where('user_id', get_admin()->id)
                            ->where('seller_access', 1);
                        });
        if ($request->has('search')){
            $sort_search = $request->search;
            $notes = $notes->where('description', 'like', '%'.$sort_search.'%');
        }
        $notes =  $notes->orderBy('created_at','desc')->paginate(10);
        return view('seller.note.index', compact('notes', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!get_setting('seller_can_add_note')){
            flash(translate('The seller does not have permissions to add a note'))->error();
            return redirect()->route('seller.note.index');
        }
        $types = EnumsNoteType::cases();
        return view('seller.note.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules      = $this->note_rules;
        $messages   = $this->note_messages;
        $validator  = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            flash(translate('Sorry! Something went wrong'))->error();
            return Redirect::back()->withErrors($validator);
        }

        $note = new Note();
        $note->user_id = auth()->id();
        $note->note_type = $request->note_type;
        $note->description = $request->description;
        $note->save();

        $note_translation = NoteTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'note_id' => $note->id]);
        $note_translation->description = $request->description;
        $note_translation->save();

        flash(translate('Note has been created successfully!'))->success();
        return redirect()->route('seller.note.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $lang   = $request->lang;
        $types = EnumsNoteType::cases();
        $note  = Note::findOrFail($id);
        return view('seller.note.edit', compact('note', 'types', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules      = $this->note_rules;
        $messages   = $this->note_messages;
        $validator  = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            flash(translate('Sorry! Something went wrong'))->error();
            return Redirect::back()->withErrors($validator);
        }
        
        $note = Note::findOrFail($id);
        $note->note_type = $request->note_type;
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $note->description = $request->description;
        }
        $note->save();

        $note_translation = NoteTranslation::firstOrNew(['lang' => $request->lang, 'note_id' => $note->id]);
        $note_translation->description = $request->description;
        $note_translation->save();

        flash(translate('Note has been updated successfully!'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {   
        $note = Note::findOrFail($note->id);
        $note->note_translations()->delete();
        $note->delete();
        flash(translate('Note has been deleted successfully!'))->success();
        return back();
    }
}
