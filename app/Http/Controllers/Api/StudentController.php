<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    public function index()
    {
        try {
            $perPage = 50; // Número de estudiantes por página
            $students = Student::paginate($perPage);
    
            $response = [
                'status' => 'success',
                'message' => 'Students found!',
                'data' => [
                    'students' => $students->items(),
                    'currentPage' => $students->currentPage(),
                    'perPage' => $students->perPage(),
                    'totalPages' => $students->lastPage(),
                    'totalCount' => $students->total(),
                ],
            ];
    
            return response()->json($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], 500);
        }
    }
    
    public function show($id){
        try {
            $students = Student::findOrFail($id);

            return response()->success($students , 'Student found!');

            } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'],500);
        }
    }

    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'DNI' => 'required|numeric|unique:students,DNI',
                'name' => 'required',
                'lastname' => 'required',
                'email' => 'required|email|unique:students,email',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $student = new Student();

            $student->DNI = $request->DNI;
            $student->name = $request->name;
            $student->lastname = $request->lastname;
            $student->email = $request->email;

            $student->save();

            return response()->success([ $student ], 'User successfully registered!');

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'],500);
        }
    }

    public function update(Request $request, $id) {
        try {
            $student = Student::findOrFail($id);
    
            $validator = Validator::make($request->all(), [
                'DNI' => ['required', 'numeric'],
                'name' => 'required',
                'lastname' => 'required',
                'email' => ['required', 'email'],
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }
    
            $existingStudentWithDNI = Student::where('DNI', $request->DNI)
                ->whereNotIn('_id', [$id])
                ->first();
    
            if ($existingStudentWithDNI) {
                return response()->json(['message' => 'The DNI is already in use by another student.'], Response::HTTP_CONFLICT);
            }
    
            $existingStudentWithEmail = Student::where('email', $request->email)
                ->whereNotIn('_id', [$id])
                ->first();
    
            if ($existingStudentWithEmail) {
                return response()->json(['message' => 'The email is already in use by another student.'], Response::HTTP_CONFLICT);
            }
    
            $student->DNI = $request->DNI;
            $student->name = $request->name;
            $student->lastname = $request->lastname;
            $student->email = $request->email;
            $student->save();
    
            return response()->success([$student], 'User updated successfully!');
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], 500);
        }
    }        

    public function destroy($id){
        try {
            $student = Student::find($id);
    
            if (!$student) {
                return response()->json(['message' => 'The student does not exist.'], Response::HTTP_NOT_FOUND);
            }
    
            Student::destroy($id);
            return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
    
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], 500);
        }
    }
    
    
}
