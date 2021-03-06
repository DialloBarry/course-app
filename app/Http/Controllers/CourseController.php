<?php

namespace App\Http\Controllers;

use App\Course;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Auth::user();
        $userId = Auth::user()->getAuthIdentifier();
        $user = User::where('id', $userId)->first();
        if (!$user->isAdmin())
            $courses = Course::orderBy('date', 'desc')->where('user_id', $userId)->get();
        else
            $courses = Course::orderBy('date', 'desc')->get();
        return view("courses.list_course", compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $montant = 0;
        $products = Product::all();
        return view("courses.add_course", compact('montant', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Auth::user();
        $course = new Course();
        $this->validate($request, [
            'products' => 'required',
            'g-recaptcha-response' => 'required'
        ]);

        $course->libelle = $request->title;
        $course->date = $request->date;
        $course->etat = $request->status;
        $course->amount = $request->amount;
        $course->user_id = Auth::user()->getAuthIdentifier();
        $products = json_decode($request->products, true);
        $course->save();
        //Update product with new course id
        if ($products != null) {
            foreach ($products as $p) {
                //ajouter les produits à la course
                $course->products()->attach(((object)$p)->id);
            }
        }
        return redirect('/courses')->with('success', 'Course ajoutée avec succès');

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::find($id);
        return view('courses.details_course', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course = Course::find($id);

        return view('courses.edit_course', compact('course', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'products' => 'required'
        ]);
        $course = Course::find($id);
        $course->libelle = $request->title;
        $course->date = $request->date;
        $course->etat = $request->status;
        $products = json_decode($request->products, true);
        $course->save();
        //Lier la sélection de produits à la course
        if ($products != null) {
            foreach ($products as $p) {
                //ajouter les produits à la course
                $course->products()->attach(((object)$p)->id);
            }
        }
        return redirect('/courses')->with('success', 'Course mise à jour avec succès');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('courses')
            ->where('id', $id)
            ->delete();
        return redirect('courses')->with('success', 'Course supprimée avec succès');;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addReceipt(Request $request)
    {
        $this->validate($request, [
            'receipt' => 'required'
        ]);
        $uploadedFile = $request->file('receipt');
        $filename = time() . $uploadedFile->getClientOriginalName();

        $uploadedFile->move(public_path() . '/receipts/', $filename);
        $courseId = $request->courseId;
        $course = Course::where('id', $courseId)->first();
        $course->receipt = $filename;
        $course->save();

        return redirect('courses');
    }
}
