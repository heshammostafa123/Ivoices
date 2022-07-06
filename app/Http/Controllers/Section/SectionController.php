<?php

namespace App\Http\Controllers\Section;

use App\Http\Controllers\Controller;
use App\Http\Requests\SectionRequest;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SectionRequest $request)
    {
        $newsection=$request->merge(['Created_by'=>Auth::user()->name])->ToArray();
        try {
            Section::create($newsection);
            session()->flash('Add', 'تم اضافة القسم بنجاح ');
            return redirect()->back();
        } catch (\Exception $ex) {
            session()->flash('error','حدث خطا ما يرجي المحاوله فيما بعد');
            return redirect()->back();
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function update(SectionRequest $request)
    {
        try{
            $id =$request->id;
            $sections = Section::find($id);
            $sections->update($request->all());
            session()->flash('edit','تم تعديل القسم بنجاج');
            return redirect()->back();
        }catch (\Exception $ex) {
            session()->flash('error','حدث خطا ما يرجي المحاوله فيما بعد');
            return redirect()->back();
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
            return redirect()->back();
        }catch (\Exception $ex) {
            session()->flash('error','حدث خطا ما يرجي المحاوله فيما بعد');
            return redirect()->back();
        }
    }
}
