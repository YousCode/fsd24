<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\NotificationHelper;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionConversationMessage;
use App\Models\ExplorerMissionProposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class NotificationRestController extends Controller
{
    /**
     * Helpers
     * @var [Array]
     */
    private $helpers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->helpers['api'] = new ApiHelper;
        $this->helpers['notification'] = new NotificationHelper();
    }

    //
    public function save(Request $request)
    {

        $notification = $request->notification ?? false;
        if ($notification) {
            $message = $notification['message'];
            $user_id = $notification['user_Id'];
            $element_type = $notification['element_type'];
            $element_id = $notification['element_id'];
        } else {
            $message = $request->input('message');
            $user_id = $request->input('user_id');
            $element_type = $request->input('element_type');
            $element_id = $request->input('element_id');
        }
        $datas = $this->helpers['notification']->save($message, $user_id, $element_type, $element_id);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    //
    public function get()
    {
        $user = \Auth::user();
        $propositions=  ExplorerMissionProposition::query()
            ->select([
                'explorer_mission_propositions.id as id',
                DB::raw('coalesce(updated_at, created_at) as datetime, coalesce( created_by) as user,coalesce( client_id) as cli,coalesce( freelance_id) as free')
            ])
            ->where('freelance_id',$user->id)
            ->orWhere('client_id',$user->id)
            ->orderBy('datetime','desc')->get();
        if($propositions)
        {
            $unreadMessages = 0;
            foreach ($propositions as $proposition) {
                if ($proposition) {
                    $propositionUnreadMessage = $this->helpers['notification']->getUnreadMessages($proposition->id, $user->id);
                    $unreadMessages += $propositionUnreadMessage;
                }
            }
            return $unreadMessages;
        }
        /*if(!empty($proposition))
        {
            $client = $proposition->client_id;
            $freelance = $proposition->freelance_id;
            if(!empty($client) && !empty($freelance))
            {
                $datas = $this->helpers['notification']->get($client,$freelance);
                $output = $this->helpers['api']->output($datas);
                return response()->json($output);
            }
        }*/
        return false;
    }

    public function getByElemId($element_id)
    {
        $datas = $this->helpers['notification']->getByElemId($element_id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}
