<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedBackController extends Controller
{
    public function CommentToElement(Request $request) {
        if($request->type == "fieldsComment"){
            if($request->action == "viewsComment"){
                $query = DB::table("CRM_COMMENT_ELEMENT")
                ->where("ELEMENT_ID", $request->fieldsId);
                if($request->categoryChapterId){
                    $query->where("CATEGORY_CHAPTER_ID", $request->categoryChapterId);    
                }
                return response()->json([
                    "succees" => true,
                    "status" => 201,
                    "data" => $query->get()
                ]);
            }
            if($request->action == "addComment"){
                $query = DB::table("CRM_COMMENT_ELEMENT")
                ->insert([
                    'ELEMENT_ID' => $request->fieldsId,
                    'DESCRIPTION' => $request->description,
                    'CREATED_BY' => $request->createdBy,
                    'CATEGORY_CHAPTER_ID' => $request->categoryChapterId,
                    'TYPE' => $request->typeComment, // type = 1 it's just comment, if type=2 it's answer for commnet
                    'ANSWER_ID' => $request->answerUserId,
                ]);
            }
            if($request->action == "answerComment"){
                $query = DB::table("CRM_COMMENT_ELEMENT")
                    ->insert([
                        'ELEMENT_ID' => $request->fieldsId,
                        'DESCRIPTION' => $request->description,
                        'CREATED_BY' => $request->createdBy,
                        'CATEGORY_CHAPTER_ID' => $request->categoryChapterId,
                        'TYPE' => $request->typeComment, // type = 1 it's just comment, if type=2 it's answer for commnet
                        'ANSWER_ID' => $request->answerUserId,]
                    );
                return response()->json([
                    "succees" => true,
                    "status" => 201
                ]);
            }
            if($request->action == "deleteComment"){
                $query = DB::table()
                    ->where("ELEMENT_ID", $request->fieldsId)
                    ->delete();
                return response()->json([
                    "succees" => true,
                    "status" => 201,
                    "message" => "Comment deleted"
                ]);
            }
            if($request->action == "updateComment"){
                $query = DB::table()
                    ->where("ELEMENT_ID", $request->fieldsId)
                    ->insert([
                        "DESCRIPTION"=> $request->description,
                    ]);
                return response()->json([
                    "succees" => true,
                    "status" => 201,
                    "message" => "Comment Update"
                ]);
            }
        }
    }
}