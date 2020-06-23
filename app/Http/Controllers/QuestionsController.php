<?php

namespace App\Http\Controllers;
use App\Http\Requests\Questions\CreateQuestionRequest;
use App\Http\Requests\Questions\UpdateQuestionRequest;
use Illuminate\Support\Facades\Gate;

use App\Question;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    public function __construct(){
        $this->middleware(['auth'])->only(['create', 'store', 'edit', 'update']);
    }
    
    public function index()
    {
        $questions = Question::with('owner')->latest()->paginate(10);//eager loader with owner!
        return view('questions.index', compact([
            'questions'
        ]));
    }

   
    public function create()
    {
       // app('debugbar')-disable();
        return view('questions.create');
    }

    
    public function store(CreateQuestionRequest $request)
    {
        auth()->user()->questions()->create([
            'title'=>$request->title,
            'body'=>$request->body
        ]);
        session()->flash('success', 'Question has been added successfully !');
        return redirect(route('questions.index'));

    }

    
    public function show(Question $question)
    {
       
        $question->increment('views_count');
       
        return view('questions.show', compact([
            'question'
        ]));
    }

    
    public function edit(Question $question)
    {
        if($this->authorize('update', $question)){
            return view('questions.edit', compact([
                'question'
            ]));
        }
        abort(403);
      
        
    }

   
    public function update(UpdateQuestionRequest $request, Question $question)
    {
        if($this->authorize('update', $question)){
            $question->update([
                'title'=>$request->title,
                'body'=>$request->body
            ]);
            session()->flash('success', 'Question has been Updated Successfully!!');
            return redirect(route('questions.index'));
        }
        abort(403);
        // if(Gate::allows('update-question', $question)){
        //     $question->update([
        //         'title'=>$request->title,
        //         'body'=>$request->body
        //     ]);
        //     session()->flash('success', 'Question has been Updated Successfully!!');
        //     return redirect(route('questions.index'));
        // }
        // abort('403');
    }

   
    public function destroy(Question $question)
    {
        if($this->authorize('delete', $question)){
            $question->delete();
            session()->flash('success', 'Question has been deleted successfully !');
            return redirect(route('questions.index'));
        }
        abort(403);
        // if(auth()->user()->can('delete-question', $question)){
        // $question->delete();
        // session()->flash('success', 'Question has been deleted successfully !');
        // return redirect(route('questions.index'));
        // }
        // abort('403');
    }
}
