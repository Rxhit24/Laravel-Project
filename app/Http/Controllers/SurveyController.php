<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyAnswerRequest;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionAnswer;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\str;

use App\Http\Resources\SurveyResource;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $user = $request->user();
        error_log("index of surveycontr");
        return SurveyResource::collection(Survey::where('user_id', $user->id)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();
        if(isset($data['image'])){
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }

        $survey = Survey::create($data);
        foreach ($data['questions'] as $question) {
            $question['survey_id'] = $survey->id;
            $this->createQuestion($question);
        }
        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey, Request $request)
    {   
        $user = $request->user();
        if($user->id !== $survey->user_id){
            return abort(403, "Unauthorized Action");
        }
        return new SurveyResource($survey);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSurveyRequest  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $data = $request->validated();

        if(isset($data['image'])){
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            //to ddelete image from lcal storage
            if($survey ->image){
                $absolutePath = public_path($survey->image);
                File::delete($absolutePath);
            }
        }
        //update survey in the database
        $survey->update($data);

        //get ids as plain array of existing questions
        $existingids = $survey->questions->pluck('id')->toArray();

        //get id of plain array of new questions
        $newids = Arr::pluck($data['questions'], 'id');

        //find questions to delete
        $toDelete = array_diff($existingids , $newids);

        //fin question to add
        $toAdd = array_diff($newids, $existingids);

        //delete questions by $todelete array method
        SurveyQuestion::destroy($toDelete);

        //create new questions
        foreach ($data['questions'] as $question ) {
            if(in_array($question['id'] , $toAdd)){
                $question['survey_id'] = $survey->id;
                $this->createQuestion($question);
            }
        }
        //update existing questions
        $questionMap = collect($data['questions'])->keyBy('id');
        foreach ($survey->questions as $question) {
            if(isset($questionMap[$question->id])){
                $this->updateQuestion( $question, $questionMap[$question->id]);
            }
        }

        return new SurveyResource($survey);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey, Request $request)
    {
        $user = $request->user();
        if($user->id !== $survey->user_id){
            return abort(403, "not allowed to modify");
        }
        $survey->delete();
        
        //to delete file from local storage
        if($survey ->image){
            $absolutePath = public_path($survey->image);
            File::delete($absolutePath);
        }
        return response('', 204);
    }

    public function saveImage($image)
    {
        //check if images is valid base64 string
        if(preg_match('/^data:image\/(\w+);base64,/', $image, $type)){
            //takeout base64 encode
            $image = substr($image, strpos($image, ',') + 1);

            $type = strtolower($type[1]);

            if(!in_array($type , ['jpg','jpeg','png','gif','svg'])){
                throw new \Exception('invalid image type');
            }

            $image = str_replace(' ','+', $image );

            $image = base64_decode($image);

            if($image === false){
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match dataURI with image data');
        }

        $dir = 'images/';
        $file = str::random().'.'.$type;
        $absolutePath = public_path($dir);

        $relativePath = $dir.$file;

        if(!File::exists($absolutePath)){
            File::makeDirectory($absolutePath, 0755 , true);
        }

        file_put_contents($relativePath, $image);

        return $relativePath;
    }

    protected function createQuestion($data){
        if(is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }
        $validator = Validator::make($data, [
            'question' => 'required|string',
            'type' => ['required', Rule::in([
                Survey::TYPE_CHECKBOX,
                Survey::TYPE_RADIO,
                Survey::TYPE_SELECT,
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA
            ])],
            'description' => 'nullable|string',
            'data' =>'present',
            'survey_id' => 'exists:App\Models\Survey,id'
        ]);

        return SurveyQuestion::create($validator->validated());
    }

    public function updateQuestion( SurveyQuestion $question, $data){
        
        if(is_array($data['data'])){
            $data['data'] = json_encode($data['data']);

        }
        $validator = Validator::make($data, [
            'id' => 'exists:App\Models\SurveyQuestion,id',
            'type' => ['required', Rule::in([
                Survey::TYPE_CHECKBOX,
                Survey::TYPE_RADIO,
                Survey::TYPE_SELECT,
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA
            ])],
            'data' => 'present',
            'question' => 'required|String',
            'description' => 'nullable|string'
        ]);
        error_log("updateQuestion");

        return $question->update($validator->validated());
        
    }
    public function showPublicSurvey(Survey $survey)
    {
        return new SurveyResource($survey);
    }

    public function storeAnswer(StoreSurveyAnswerRequest $request,  Survey $survey)
    {
        $validated = $request->validated();

        $surveyAnswer = SurveyAnswer::create([
            'survey_id' => $survey->id,
            'start_date' => date('Y-m-d H-i-s'),
            'end_date' => date('Y-m-d H-i-s'),
        ]);

        foreach($validated['answers'] as $questionId => $answers){
            $question = SurveyQuestion::where(['id' => $questionId, 'survey_id' => $survey->id ])->get();

            if(!$question){
                return response("invalid Question ID: \"$questionId\"", 400);
            }

            $data = [
                'survey_question_id' => $questionId,
                'survey_answer_id' => $surveyAnswer->id,
                'answer' => is_array($answers) ? json_encode($answers) : $answers
            ];

            SurveyQuestionAnswer::create($data);
        } 
        return response('',201);
    }
}
