<?php

namespace App\Http\Controllers;

use App\Models\PostTag;
use Illuminate\Http\Request;
use Validator;

class PostTagController extends Controller
{
    public function update(Request $request, $id)
    {
        try {
            $valid = Validator::make($request->all(), [
                'color' => 'required|unique:post_tag,color,' . $id,
                'tagname' => 'required|unique:post_tag,tagName,' . $id
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $posttag = PostTag::find($id);
            $posttag->color = $request->color;
            $posttag->tagName = $request->tagname;
            if ($posttag->update    ()) {
                return response()->json($posttag, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'color' => 'required|unique:post_tag,color',
                'tagname' => 'required|unique:post_tag,tagName'
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $posttag = new PostTag();
            $posttag->color = $request->color;
            $posttag->tagName = $request->tagname;
            if ($posttag->save()) {
                return response()->json($posttag, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function delete($id)
    {
        try {
            if (PostTag::destroy($id)) {
                return response()->json("Success", 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function index()
    {
        try {
            return response()->json(PostTag::all(), 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
