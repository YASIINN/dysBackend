<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\Files;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class FileController extends Controller
{
    // required|image|mimes:jpeg,png,jpg|max:2048
    public $imageType;

    public function __construct(Parser $parser)
    {
        $this->imageType = $parser;
    }


    public function update(Request $request, $id)
    {

        try {
            $valid = Validator::make($request->all(), [
                'file' => 'max:10000',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 401);
            }
            $isImage = $this->imageType->ImageTypeControl($request->file("file")->getClientOriginalExtension());
            if ($isImage) {
                $filepath = $request->file('file')->store('public/uploads/images');
                if ($filepath) {
                    $file = Files::find($id);
                    $fileDeleted = Storage::delete($file->name);
                    if ($fileDeleted) {
                        //"http://localhost/zysbackend/storage/app/"
                        $fileName = explode("/", $filepath)[3];
                        $file->path = env("HOST_URL") . "public/storage/uploads/excel/" . $fileName;
                        $file->size = $request->file("file")->getSize();
                        $file->type = $request->file("file")->getMimeType();
                        $file->name = $filepath;
                        $file->viewname = $request->file("file")->getClientOriginalName();
                        $file->viewtype = "img";
                        if ($file->update()) {
                            return response()->json($file, 200);
                        } else {
                            return response()->json([], 500);
                        }
                    } else {
                        return response()->json([], 500);
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function delete($id)
    {
        try {
            $file = Files::find($id);
            $fileDeleted = Storage::delete($file->name);
            if ($fileDeleted) {
                if (Files::destroy($id)) {
                    return response()->json('Ürün başarıyla silindi.', 200);
                } else {
                    return response()->json("Resim DB Silinme Hatası", 500);
                }
            } else {
                return response()->json("Resim Fiziksel Silinme Hatası", 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function addmultiplefile(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'multipleFiles' => 'max:100000',
        ]);
        if ($valid->fails()) {
            return response()->json(['error' => $valid->errors()], 401);
        }
        try {
            $result = array();
            $allsave = false;
            for ($i = 0; $i < count($request->multipleFiles); $i++) {
                $isImage = $this->imageType->ImageTypeControl($request->file("multipleFiles")[$i]->getClientOriginalExtension());
                $isExcel = $this->imageType->ExcelTypeControl($request->file("multipleFiles")[$i]->getClientOriginalExtension());
                $isText = $this->imageType->TextTypeControl($request->file("multipleFiles")[$i]->getClientOriginalExtension());
                $isPdf = $this->imageType->PdfTypeControl($request->file("multipleFiles")[$i]->getClientOriginalExtension());
                if ($isImage) {
                    $filepath = $request->multipleFiles[$i]->store('public/uploads/Images');
                }
                if ($isExcel) {
                    $filepath = $request->multipleFiles[$i]->store('public/uploads/Excel');
                }
                if ($isText) {
                    $filepath = $request->multipleFiles[$i]->store('public/uploads/Text');
                }
                if ($isPdf) {
                    $filepath = $request->multipleFiles[$i]->store('public/uploads/Pdf');
                }
                if ($filepath) {
                    $fileName = explode("/", $filepath)[3];
                    $file = new Files();
                    $lpath = "";
                    if ($isImage) {
                        $lpath = env("HOST_URL") . "public/storage/uploads/Images/" . $fileName;
                    }
                    if ($isExcel) {
                        $lpath = env("HOST_URL") . "public/storage/uploads/Excel/" . $fileName;
                    }
                    if ($isText) {
                        $lpath = env("HOST_URL") . "public/storage/uploads/Text/" . $fileName;
                    }
                    if ($isPdf) {
                        $lpath = env("HOST_URL") . "public/storage/uploads/Pdf/" . $fileName;
                    }
                    $file->path = $lpath;
                    $file->size = $request->file("multipleFiles")[$i]->getSize();
                    $file->type = $request->file("multipleFiles")[$i]->getMimeType();
                    $file->name = $filepath;
                    $file->viewname = $request->file("multipleFiles")[$i]->getClientOriginalName();
                    $file->viewtype = $isImage ? 'img' : 'download';
                    if ($file->save()) {
                        $allsave = true;
                        array_push($result, $file);
                    } else {
                        $allsave = false;
                        return response()->json([], 500);
                    }
                } else {
                    $allsave = false;
                    return response()->json(['error', 'Dosya Ekleme Hatası'], 500);
                }
            }
            if ($allsave) {
                return response()->json($result, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function test(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'fileToUpload' => 'max:10000',
        ]);
        if ($valid->fails()) {
            return response()->json(['error' => $valid->errors()], 401);
        }

        for ($i = 0; $i < count($request->fileToUpload); $i++) {

            $isImage = $this->imageType->ImageTypeControl($request->file("fileToUpload")[$i]->getClientOriginalExtension());
            $isExcel = $this->imageType->ExcelTypeControl($request->file("fileToUpload")[$i]->getClientOriginalExtension());
            $isText = $this->imageType->TextTypeControl($request->file("fileToUpload")[$i]->getClientOriginalExtension());
            if ($isImage) {
                $filepath = $request->fileToUpload[$i]->store('public/uploads/Image');
            }
            if ($isExcel) {
                $filepath = $request->fileToUpload[$i]->store('public/uploads/Excel');
            }
            if ($isText) {
                $filepath = $request->fileToUpload[$i]->store('public/uploads/Text');
            }
        }
    }

    public function store(Request $request)
    {


        $valid = Validator::make($request->all(), [
            'file' => 'max:10000',
        ]);
        if ($valid->fails()) {
            return response()->json(['error' => $valid->errors()], 401);
        }
        $isImage = $this->imageType->ImageTypeControl($request->file("file")->getClientOriginalExtension());
        $isExcel = $this->imageType->ExcelTypeControl($request->file("file")->getClientOriginalExtension());
        if ($isImage) {
            $filepath = $request->file('file')->store('public/uploads/images');
            if ($filepath) {
                //"http://localhost/zysBitBucket/zaferprojebackend/storage/app/"
                $fileName = explode("/", $filepath)[3];
                $file = new Files();
                $file->path = env("HOST_URL") . "public/storage/uploads/images/" . $fileName;
                $file->size = $request->file("file")->getSize();
                $file->type = $request->file("file")->getMimeType();
                $file->name = $filepath;
                $file->viewname = $request->file("file")->getClientOriginalName();
                $file->viewtype = "img";
                if ($file->save()) {
                    return response()->json($file, 200);
                } else {
                    return response()->json([], 500);
                }
            } else {
                return response()->json([], 500);
            }
        }
        if ($isExcel) {
            $filepath = $request->file('file')->store('public/uploads/excel');
            if ($filepath) {
                $file = new Files();
                $fileName = explode("/", $filepath)[3];
                $file->path = env("HOST_URL") . "public/storage/uploads/excel/" . $fileName;
                $file->size = $request->file("file")->getSize();
                $file->type = $request->file("file")->getMimeType();
                $file->name = $filepath;
                $file->viewname = $request->file("file")->getClientOriginalName();
                $file->viewtype = "excel";
                if ($file->save()) {
                    return response()->json($file, 200);
                } else {
                    return response()->json([], 500);
                }
            } else {
                return response()->json([], 500);
            }
        }
    }
}
