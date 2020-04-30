<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\PostNotification;
use Illuminate\Http\Request;

class PostNotificationController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function all(Request $request)
    {
        try {
            //TODO USER ID
            $queryparse = $request->urlparse;
            $parser = $this->uriParser->queryparser($queryparse);
            $pnotification = PostNotification::with(['touser', 'post', 'fromuser'])
                ->where($parser)
                ->latest()
                ->paginate(10);
            return $pnotification;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function update(Request $request)
    {
        try {

                $pnotificationType = PostNotification::where(
                [
                    ['id', "=", $request->id],
                ]
            )->update(['isRead' => 1]);
            if ($pnotificationType) {
                return response()->json($pnotificationType, 200);
            }

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function index(Request $request)
    {
        try {
            //TODO USER ID
            $queryparse = $request->urlparse;
            $parser = $this->uriParser->queryparser($queryparse);
            $pnotification = PostNotification::with(['touser', 'post', 'fromuser'])
                ->where($parser)
                ->latest()
                ->take(5)
                ->get();
            return $pnotification;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
