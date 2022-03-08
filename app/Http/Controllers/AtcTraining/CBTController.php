<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\CBT\CbtExam;
use App\Models\AtcTraining\CBT\CbtExamAnswer;
use App\Models\AtcTraining\CBT\CbtExamAssign;
use App\Models\AtcTraining\CBT\CbtExamQuestion;
use App\Models\AtcTraining\CBT\CbtExamResult;
use App\Models\AtcTraining\CBT\CbtModule;
use App\Models\AtcTraining\CBT\CbtModuleAssign;
use App\Models\AtcTraining\CBT\CbtModuleLesson;
use App\Models\AtcTraining\CBT\CbtNotification;
use App\Models\AtcTraining\Student;
use App\Models\Users\User;
use App\Notifications\ExamCompletion;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class CBTController extends Controller
{
    public function index()
    {
        $student = Student::all();

        return view('dashboard.training.CBT.index', compact('student'));
    }

    public function moduleindex()
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        if ($student == null) {
            return redirect()->back()->withError('You are not a student in the system! Please contact the Chief Instructor.');
        }
        //Student Assigned Modules
        if ($student != null) {
            $modules = CbtModuleAssign::where('student_id', $student->id)->get();
            if (count($modules) < 1) {
                return redirect()->back()->withError('You do not have any assigned modules! Contact your Instructor at '.$student->instructor->email.'');
            }
        }

        return view('dashboard.training.CBT.modules', compact('modules'));
    }

    public function moduleindexadmin()
    {
        $modules = CbtModule::all();
        $exam = CbtExam::all();

        return view('dashboard.training.CBT.modulesadmin', compact('modules', 'exam'));
    }

    public function addModule(Request $request)
    {
        if ($request->input('exam') == 0) {
            $exam = null;
        } else {
            $questioncount = CbtExamQuestion::where('cbt_exam_id', $request->input('exam'))->get();
            if (count($questioncount) < 10) {
                return redirect()->back()->withError('This exam does not have the minimum 10 questions, so it cannot be assigned!');
            }
            $exam = $request->input('exam');
        }
        $module = CbtModule::create([
            'name' => $request->input('name'),
            'user_id' => Auth::user()->id,
            'cbt_exam_id' => $exam,
        ]);
        CbtModuleLesson::create([
            'cbt_modules_id' => $module->id,
            'lesson' => 'intro',
            'name' => 'Introduction',
            'content_html' => 'Create some content!',
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtModuleLesson::create([
            'cbt_modules_id' => $module->id,
            'lesson' => 'conclusion',
            'name' => 'Conclusion',
            'content_html' => 'Create some content!',
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->route('cbt.module.edit', $module->id);
    }

    public function deleteModule($id)
    {
        $assign = CbtModuleAssign::where('cbt_module_id', $id)->get();
        foreach ($assign as $a) {
            $a->delete();
        }
        $lessons = CbtModuleLesson::where('cbt_modules_id', $id)->get();
        foreach ($lessons as $l) {
            $l->delete();
        }
        $module = CbtModule::whereId($id)->first();
        $module->delete();

        return redirect()->back()->withSuccess('Deleted the Module!');
    }

    public function editModuleDetails(Request $request, $id)
    {
        if ($request->input('exam') == 0) {
            $exam = null;
        } else {
            $questioncount = CbtExamQuestion::where('cbt_exam_id', $request->input('exam'))->get();
            if (count($questioncount) < 10) {
                return redirect()->back()->withError('This exam does not have the minimum 10 questions, so it cannot be assigned!');
            }
            $exam = $request->input('exam');
        }
        $module = CbtModule::whereId($id)->first();
        $module->name = $request->input('name');
        $module->cbt_exam_id = $exam;
        $module->save();

        return redirect()->back()->withSuccess('Changed module details!');
    }

    public function assignModuleAll($id)
    {
        $module = CbtModule::whereId($id)->first();
        $module->assignall = '1';
        $module->save();
        $students = Student::all();
        foreach ($students as $s) {
            if ($s->instructor != null) {
                $instructor = $s->instructor->id;
            }
            if ($s->instructor == null) {
                $instructor = null;
            }
            $check = CbtModuleAssign::where([
                ['cbt_module_id', $id],
                ['student_id', $s->id],
            ])->first();
            if ($check == null) {
                CbtModuleAssign::create([
                    'cbt_module_id' => $id,
                    'student_id' => $s->id,
                    'instructor_id' => $instructor,
                    'intro' => '1',
                    'created_at' => Carbon::now()->toDateTimeString(),
                ]);
                CbtNotification::create([
                    'student_id' => $s->id,
                    'message' => 'You have been assigned the '.$module->name.' Module!',
                    'dismissed' => '0',
                ]);
            }
        }

        return redirect()->back()->withSuccess('Assigned module to all students!');
    }

    public function moduleUnassignall($id)
    {
        $students = Student::all();
        $module = CbtModule::whereId($id)->first();
        $module->assignall = '0';
        $module->save();
        foreach ($students as $s) {
            $assign = CbtModuleAssign::where([
                ['cbt_module_id', $id],
                ['student_id', $s->id],
            ])->first();
            if ($assign != null) {
                $assign->delete();
            }
        }

        return redirect()->back()->withSuccess('Unassigned all students from this module!');
    }

    public function editModule($id)
    {
        $exam = CbtExam::all();
        $module = CbtModule::whereId($id)->first();
        $intro = CbtModuleLesson::where([
            ['cbt_modules_id', $id],
            ['lesson', 'intro'],
        ])->first();
        $lessons = CbtModuleLesson::where([
            ['cbt_modules_id', $id],
            ['lesson', 'LIKE', '%'.'lesson'.'%'],
        ])->get();
        $conclusion = CbtModuleLesson::where([
            ['cbt_modules_id', $id],
            ['lesson', 'conclusion'],
        ])->first();

        return view('dashboard.training.CBT.editmodule', compact('exam', 'module', 'lessons', 'intro', 'conclusion'));
    }

    public function addLesson(Request $req, $id)
    {
        $lesson = CbtModuleLesson::create([
            'cbt_modules_id' => $id,
            'lesson' => $req->input('lesson'),
            'name' => 'Name your lesson',
            'content_html' => 'Give your lesson some content! You can use HTML',
            'updated_by' => Auth::user()->id,
            'created_by' => Auth::user()->id,
        ]);

        return redirect()->route('cbt.lesson.edit', $lesson->id);
    }

    public function editLesson($id)
    {
        $lesson = CbtModuleLesson::whereId($id)->first();

        return view('dashboard.training.CBT.editmodule2', compact('lesson'));
    }

    public function processEditLesson(Request $req, $id)
    {
        $lesson = CbtModuleLesson::whereId($id)->first();
        $lesson->name = $req->input('name');
        $lesson->content_html = $req->input('content');
        $lesson->updated_by = Auth::user()->id;
        $lesson->save();

        return redirect()->route('cbt.module.edit', $lesson->cbt_modules_id)->withSuccess('Edited Lesson!');
    }

    public function deleteLesson($id)
    {
        $lesson = CbtModuleLesson::whereId($id);
        $lesson->delete();

        return redirect()->back()->withSuccess('Deleted the lesson!');
    }

    public function viewmodule($id, $progress)
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        $intro = CbtModuleLesson::where([
            ['cbt_modules_id', $id],
            ['lesson', 'intro'],
        ])->first();
        $lessons = CbtModuleLesson::where([
            ['cbt_modules_id', $id],
            ['lesson', 'LIKE', '%'.'lesson'.'%'],
        ])->get();
        $conclusion = CbtModuleLesson::where([
            ['cbt_modules_id', $id],
            ['lesson', 'conclusion'],
        ])->first();
        $currentlesson = CbtModuleLesson::where([
            ['cbt_modules_id', $id],
            ['lesson', $progress],
        ])->first();

        $update = CbtModuleAssign::where([
            ['student_id', $student->id],
            ['cbt_module_id', $id],
        ])->first();

        if ($update->started_at == null) {
            $update->started_at = Carbon::now()->toDateTimeString();
            $update->save();
        }
        if ($progress != 'intro') {
            $update->{$progress} = 1;
            $update->save();
        }

        return view('dashboard.training.CBT.viewmodule', compact('lessons', 'currentlesson', 'update', 'intro', 'conclusion'));
    }

    public function completeModule($id)
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        $module = CbtModuleAssign::where([
            'cbt_module_id' => $id,
            'student_id' => $student->id,
        ])->first();
        $module->completed_at = Carbon::now()->ToDateTimeString();
        $module->save();
        if ($module->cbtmodule->cbt_exam_id != null) {
            $check = CbtExamAssign::where([
                ['student_id', $student->id],
                ['cbt_exam_id', $module->cbtmodule->cbt_exam_id],
            ])->first();
            if ($check != null) {
                return redirect()->back()->withSuccess('The exam is available under the Exams Section!');
            }
            $check2 = CbtExamResult::where([
                ['cbt_exam_id', $module->cbtmodule->cbt_exam_id],
                ['student_id', $student->id],
            ])->first();
            if ($check2 != null) {
                return redirect()->back()->withSuccess('You have already completed the exam for this module!');
            }
            $exam = CbtExamAssign::create([
                'student_id' => $student->id,
                'instructor_id' => $student->instructor->id,
                'cbt_exam_id' => $module->cbtmodule->cbt_exam_id,
            ]);

            return redirect()->route('cbt.exam')->withSuccess('You have been assigned the exam for the module!');
        }

        return view('dashboard.training.CBT.index')->withSuccess('You have completed the Module!');
    }

    public function assignModule(Request $request, $id)
    {
        return redirect()->back()->withError('This feature has not been implemented yet!');
    }

    public function examindex()
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        if ($student == null) {
            $exams = CbtExamAssign::where('student_id', '0')->get();
            $completedexams = CbtExamResult::where('student_id', '0')->get();

            return view('dashboard.training.CBT.exams.index', compact('exams', 'completedexams'));
        }
        $exams = CbtExamAssign::where('student_id', $student->id)->get();
        $completedexams = CbtExamResult::where('student_id', $student->id)->get();

        return view('dashboard.training.CBT.exams.index', compact('exams', 'completedexams'));
    }

    public function examadminview()
    {
        $exams = CbtExam::all();

        return view('dashboard.training.CBT.exams.examadmin', compact('exams'));
    }

    public function startExam($id)
    {
        $subject = CbtExam::find($id);
        $student = Student::where('user_id', Auth::user()->id)->first();
        session()->forget('next_question_id');

        return view('dashboard.training.CBT.exams.startexam', compact('subject', 'student'));
    }

    public function exam($id)
    {
        $subject = CbtExam::find($id);
        $questions = CbtExamQuestion::where('cbt_exam_id', $id)->orderByRaw('RAND()')->take(10)->get();

        return view('dashboard.training.CBT.exams.exam', compact('subject', 'questions'));
    }

    public function gradeExam(Request $req, $id)
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        $examcheck = CbtExamResult::where([
            ['student_id', $student->id],
            ['cbt_exam_id', $id],
        ])->first();
        if ($examcheck != null) {
            return redirect()->route('cbt.exam')->withError('You have already completed this exam!');
        }
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_1'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('1'),
            'right_answer' => $req->input('a_1'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_2'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('2'),
            'right_answer' => $req->input('a_2'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_3'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('3'),
            'right_answer' => $req->input('a_3'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_4'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('4'),
            'right_answer' => $req->input('a_4'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_5'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('5'),
            'right_answer' => $req->input('a_5'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_6'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('6'),
            'right_answer' => $req->input('a_6'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_7'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('7'),
            'right_answer' => $req->input('a_7'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_8'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('8'),
            'right_answer' => $req->input('a_8'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_9'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('9'),
            'right_answer' => $req->input('a_9'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_10'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('10'),
            'right_answer' => $req->input('a_10'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        $score = '0';
        $answers = CbtExamAnswer::where([
            ['student_id', $student->id],
            ['cbt_exam_id', $id],
        ])->get();
        foreach ($answers as $a) {
            if ($a->user_answer == $a->right_answer) {
                $score++;
            }
        }
        $grade = $score / 10 * 100;
        CbtExamResult::create([
            'student_id' => $student->id,
            'cbt_exam_id' => $id,
            'instructor_id' => $student->instructor->id,
            'grade' => $grade,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        $removeexam = CbtExamAssign::where([
            'student_id' => $student->id,
            'cbt_exam_id' => $id,
        ])->first();
        $removeexam->delete();
        $exam = CbtExam::whereId($id)->first();
        $results = CbtExamAnswer::where([
            'student_id' => $student->id,
            'cbt_exam_id' => $id,
        ])->get();
        $student->instructor->user->notify(new ExamCompletion($grade, $student, $exam));

        return redirect()->route('cbt.exam.results', [$id, $student->id]);
    }

    public function examResults($id, $sid)
    {
        $student = Student::whereId($sid)->first();
        $exam = CbtExam::whereId($id)->first();
        $results = CbtExamAnswer::where([
            'student_id' => $sid,
            'cbt_exam_id' => $id,
        ])->get();
        $grade = CbtExamResult::where([
            'student_id' => $sid,
            'cbt_exam_id' => $id,
        ])->first();

        return view('dashboard.training.CBT.exams.results', compact('exam', 'results', 'grade', 'student'));
    }

    public function questionBank($id)
    {
        $exam = CbtExam::whereId($id)->first();
        $questions = CbtExamQuestion::where('cbt_exam_id', $id)->get();

        return view('dashboard.training.CBT.exams.qbank', compact('exam', 'questions'));
    }

    public function addQuestion(Request $request, $id)
    {
        $question = CbtExamQuestion::updateOrCreate([
            'cbt_exam_id' => $id,
            'question' => $request->input('question'),
            'option1' => $request->input('option1'),
            'option2' => $request->input('option2'),
            'option3' => $request->input('option3'),
            'option4' => $request->input('option4'),
            'answer' => $request->input('answer'),
        ]);

        return redirect()->back()->withSuccess('Added the question!');
    }

    public function updateQuestion(Request $request, $id)
    {
        $question = CbtExamQuestion::whereId($id)->first();
        if ($question != null) {
            $question->question = $request->input('question');
            $question->option1 = $request->input('option1');
            $question->option2 = $request->input('option2');
            $question->option3 = $request->input('option3');
            $question->option4 = $request->input('option4');
            $question->answer = $request->input('answer');
            $question->save();

            return redirect()->back()->withSuccess('Edited the question!');
        } else {
            return redirect()->back()->withError('A Server error has occured. Please contact Webmaster!');
        }
    }

    public function deleteQuestion($id)
    {
        $question = CbtExamQuestion::whereId($id)->first();
        $question->delete();

        return redirect()->back()->withSuccess('Question has been deleted!');
    }

    public function saveAnswer(Request $req, $id)
    {
        //save result
        $student = Student::where('user_id', Auth::user()->id)->first();
        $subject = CbtExam::find($id);
        $question = CbtExamQuestion::find($req->get('question_id'));
        if ($req->get('option') != null) {
            //save the answer into table
            //dd($time_taken);

            CbtExamAnswer::create([
                'student_id'=>$student->id,
                'cbt_exam_question_id'=>$req->get('question_id'),
                'cbt_exam_id' => $id,
                'user_answer'=>$req->get('option'),
                'question' => $question->question,
                'option1' => $question->option1,
                'option2' => $question->option2,
                'option3' => $question->option3,
                'option4' => $question->option4,
                'right_answer'=>$question->answer,
            ]);
        }

        $next_question_id = $subject->questions()->where('id', '>', $req->get('question_id'))->min('id');
        if ($next_question_id != null) {
            return Response()->json(['next_question_id' => $next_question_id]);
        }

        return redirect()->route('gradeExam', [$id]);
    }

    public function addExam(Request $req)
    {
        CbtExam::create([
            'name' => $req->input('name'),
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->back()->withSuccess('Added '.$req->input('name').' Exam!');
    }

    public function modifyExam(Request $request, $id)
    {
        //  User::updateOrCreate
        return redirect()->back()->withError('This feature has not been implemented yet!');
    }

    public function deleteExam($id)
    {
        CbtExamResult::where('cbt_exam_id', $id)->delete();
        CbtExamAnswer::where('cbt_exam_id', $id)->delete();
        CbtExamQuestion::where('cbt_exam_id', $id)->delete();
        CbtExamAssign::where('cbt_exam_id', $id)->delete();
        CbtExam::findOrFail($id)->delete();

        return redirect()->back()->withSuccess('Exam deleted!');
    }

    public function getQuestions($id)
    {
        $subject = Subject::findOrFail($id);

        $title = 'Manage questions';
        $answer = ['1'=>1, '2'=>2, '3'=> 3, '4'=> 4];
        $questions = $subject->questions;
        $title_button = 'Save question';
        //dd($questions);
        return view('subject.questions', compact('subject', 'title', 'answer', 'questions', 'title_button'));
    }

    public function viewQuestions($id)
    {
        $questions = CbtExamQuestion::where('cbt_exam_id', $id)->get();
        $exam = CbtExam::whereId($id)->FirstorFail();

        return view('dashboard.training.cbt.exams.viewexamadmin', compact('questions', 'exam'));
    }

    public function assignExam(Request $request, $id)
    {
        return redirect()->back()->withError('This feature has not been implemented yet!');
    }
}
