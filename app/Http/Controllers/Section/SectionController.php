<?php

namespace App\Http\Controllers\Section;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSectionRequest;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections=Section::all();
        return view('sections.sections',['sections'=>$sections]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSectionRequest $request)
    {
        try {
            Section::create([
                'section_name' => $request->section_name,
                'description' => $request->description,
                'Created_by' => (Auth::user()->name),
            ]);
            session()->flash('Add', 'تم اضافة القسم بنجاح ');
            return redirect('/sections');
        } catch (\Exception $ex) {
            session()->flash('error','حدث خطا ما يرجي المحاوله فيما بعد');
            return redirect('/sections');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function show(Section $sections)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function edit(Section $sections)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSectionRequest $request)
    {
        try{
            $id = $request->id;
            $sections = Section::find($id);
            $sections->update([
                'section_name' => $request->section_name,
                'description' => $request->description,
            ]);
            session()->flash('edit','تم تعديل القسم بنجاج');
            return redirect('/sections');
        }catch (\Exception $ex) {
            session()->flash('error','حدث خطا ما يرجي المحاوله فيما بعد');
            return redirect('/sections');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
            $id = $request->id;
            Section::find($id)->delete();
            session()->flash('delete','تم حذف القسم بنجاح');
            return redirect('/sections');
        }catch (\Exception $ex) {
            session()->flash('error','حدث خطا ما يرجي المحاوله فيما بعد');
            return redirect('/sections');
        }
    }
}
