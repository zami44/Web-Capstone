<?php

namespace App\Http\Controllers\student;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['students'] = Student::paginate(5);
        return view('students.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(),[

            'full_name' => 'required',
            'class_no'  => 'required|numeric',
            'photo'     => 'required|image|mimes:jpeg,png,jpg',
        ]);

        if( $validator->fails())
        {
            return response()->json([
                'status'=>400,
                'errors' => $validator->messages(),
            ]);
        }
        else
        {
            $student = new Student;
            $student->full_name = $request->full_name;
            $student->class_no = $request->class_no;

            if($request->hasfile('photo'))
            {
                $file = $request->file('photo');
                $fileExtention = $file->getClientOriginalExtension();
                $fileName = time().'.'.$fileExtention;
                $file->move('uploads/students_image/',$fileName);
                $student->photo = $fileName;
            }

            $student->save();

            return response()->json(['status'=>200,'message'=>'Student created successfully']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['student'] = Student::find($id);
        return view('students.view',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['student'] = Student::find($id);

        if ($data['student'])
        {
            return view('students.edit',$data);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[

            'full_name' => 'required',
            'class_no'  => 'required|numeric',
            'photo'     => 'image|mimes:jpeg,png,jpg',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status' =>400,
                'errors' =>$validator->messages(),
            ]);
        }
        else
        {

            $student = Student::find($id);

            if($student)
            {
                $student->full_name = $request->full_name;
                $student->class_no = $request->class_no;
                if($request->hasfile('photo'))
                {
                    $path = 'uploads/students_image/'.$student->photo;
                    if(File::exists($path))
                    {
                        File::delete($path);
                    }
                
                $file = $request->file('photo');
                $fileExtention = $file->getClientOriginalExtension();
                $fileName = time().'.'.$fileExtention;
                $file->move('uploads/students_image/',$fileName);
                $student->photo = $fileName;
                }
                $student->save();

                return response()->json([
                    'status' =>200,
                    'studentUpdated' => true,
                    'message' =>'Student Updated Successfully',
                ]);

            }

            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $student = Student::find($id);

       if($student)
       {
        $student->delete();
        return response()->json([
            'status' => 200,
            'deleted' => true,
            'message' =>'Student Delete Successfully'
        ]);
       }
       else
       {
        return response()->json(['status'=>404,'message'=>'Student Not Found']);
       }
    }



    // pagination

    public function pagination(Request $request)
    {
        $data['students'] = Student::paginate(5);
        return view('students.pagination',$data)->render();
    }
}
