<?php

namespace App\Http\Controllers\Api\Section;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Section;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    use ApiResponseTrait;
    public function index()
    {
        $sections = Section::all();
        return $this->apiResponse($sections, 'ok', 200);
    }

    public function show($id){
        try {
            $Section = Section::find($id);
            if ($Section) {
                return $this->apiResponse($Section, "Section Data", 200);
            }
            return $this->apiResponse(null, "the Section not found", 404);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'section_name' => 'required|max:255|unique:sections',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors(), 400);
            }
            $Section = Section::create([
                'section_name' => $request->section_name,
                'description' => $request->description,
                'Created_by' => (Auth::user()->name),
            ]);
            if ($Section) {
                return $this->apiResponse($Section, "Section saved", 201);
            }
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'section_name' => 'max:255|unique:sections,section_name,' . $request->id,
            ]);
            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors(), 400);
            }
            $section = Section::find($id);
            if (!$section) {
                return $this->apiResponse(null, "the section not found", 404);
            }
            $section->update($request->all());
            if ($section) {
                return $this->apiResponse($section, "section updated", 200);
            }
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    public function destroy($id)
    {
        try {
            $Section = Section::find($id);
            if (!$Section) {
                return $this->apiResponse(null, "the Section not found", 404);
            }
            $Section->delete($id);
            if ($Section) {
                return $this->apiResponse(null, "Section Deleted", 200);
            }
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }
}
