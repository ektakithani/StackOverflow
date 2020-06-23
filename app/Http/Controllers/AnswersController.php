<?php

namespace App\Http\Controllers;
use App\Http\Requests\Answers\CreateAnswerRequest;
use App\Http\Requests\Answers\UpdateAnswerRequest;
use App\Answer;
use App\Question;
use Illuminate\Http\Request;
use App\Notifications\NewReplyAdded;
use App\Notifications\BestAnswerNotification;


class AnswersController extends Controller
{
    public function store(CreateAnswerRequest $request , Question $question)
    {
       // dd('ans');
        $question->answers()->create([
            'body'=>$request->body,
            'user_id'=>auth()->id()
        ]);
        $question->owner->notify(new NewReplyAdded($question));
        session()->flash('success', 'Your answer submitted succesfully!');
        return redirect($question->url);
    }

  
   
    public function edit(Question $question , Answer $answer)
    {
        
        $this->authorize('update', $answer);
       
        return view('answers.edit', compact([
            'question',
            'answer'
        ]));
    }

    
    public function update(UpdateAnswerRequest $request, Question $question ,  Answer $answer)
    {
      
        $answer->update([
            'body'=>$request->body
        ]);
        session()->flash('success', 'Answer Updated Successfully!');
        return redirect($question->url);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question, Answer $answer)
    {
        $this->authorize('delete', $answer);
        $answer->delete();
        session()->flash('success', 'Answer Deleted Successfully!');
        return redirect($question->url);
    }

    public function bestAnswer(Request $request, Answer $answer){
        // dd($answer->question);
        $this->authorize('markAsBest', $answer);
        $answer->question->markBestAnswer($answer);
        $answer->author->notify(new BestAnswerNotification ($answer->question));
        return redirect()->back();

    }
}
