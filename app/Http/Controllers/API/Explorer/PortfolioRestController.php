<?php

namespace App\Http\Controllers\API\Explorer;

use App\Events\PortfolioAdded;
use App\Events\PortfolioDelete;
use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Http\Resources\Explorer\Portfolio as PortfolioResource;
use App\Models\PortfolioMedia;
use App\Rules\portfolioRules;
use App\Rules\VideoUrlValidation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Embed\Embed;

class PortfolioRestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Portfolio[]|Collection|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $isHome = $request->input('isHome');
        $portfolios = Portfolio::all();
        if(\Auth::user()->user_status === 'freelance' && \Auth::user()->name !== null && !$isHome)
        {
            $portfolios =  Portfolio::with('users')->where('created_by','=',\Auth::user()->id)->get();
        }

        return $portfolios;

        //$portfolio = Portfolio::with('medias')->join('portfolios_talents',"portfolios_talents.portfolio_id","=","portfolios.id")->leftJoin('users', 'user_id', '=', 'users.id')->get();
        //return Portfolio::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
        $params = $request->all();
        $link = $request->input('portfolio_video_url');
        $img = $request->input('portfolio_medias');
        $validation = Validator::make($params, [
            'portfolio_name' => ['required'],
            'portfolio_description'=>[''],
            'portfolio_medias'=>['sometimes','max:5',new portfolioRules]
        ]);
        if ($link !== null){
            $validation = Validator::make($params,[
                'portfolio_video_url'=>['sometimes',new VideoUrlValidation],
                'portfolio_name' => ['required'],
                'portfolio_description'=>[''],
                'portfolio_medias'=>['sometimes','max:5',new portfolioRules]]);
        }
        if ($validation->fails()) {
            return response()->json($validation->messages(), 400);
        }

        $portfolio = new Portfolio();
        $embed = new Embed();
        if(isset($link)){
            $uri = $embed->get("$link");
            $info = $uri->image;
            $portfolio->video_url_image = $info;
        }
        $portfolio->name = $params['portfolio_name'];
        $portfolio->description = $params['portfolio_description'];
        $portfolio->created_by = auth()->user()->id;
        if(!empty($params['portfolio_video_url']))
        {
            $portfolio->video_url = $params['portfolio_video_url'];
        }
        event(new PortfolioAdded());
        $portfolio->save();
        $user = \Auth::user();
        $portfolio->users()->attach($user);
        $isMediaExist = isset($params['portfolio_medias']);
        $mediaParams = $request->file('portfolio_medias');
        if($isMediaExist){
            foreach ($mediaParams as $portfolio_media) {
                $media = new PortfolioMedia();


                $filename = time() . '_' . $portfolio_media->getClientOriginalName();
                $reformatFilename = str_replace(' ','_',$filename);
                $media->name = $reformatFilename;


                // File extension
                $media->extension = $portfolio_media->getClientOriginalExtension();

                // File upload location
                $location = 'upload/portfolio-media';

                // Upload file
                $portfolio_media->move($location, $reformatFilename);

                // File path
                $media->path = '/' . $location . '/' . $reformatFilename;

                $media->media_type = 'image';
                $media->portfolio_id = $portfolio->id;
                $media->save();
            }
        }
        return $portfolio;
    }

    /**
     * Display the specified resource.
     *
     * @param Portfolio $portfolio
     * @return \Illuminate\Http\Response
     */
    public function show(Portfolio $portfolio, Request $request)
    {
        return new PortfolioResource($portfolio);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Portfolio $portfolio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        //
        $portfolio->update($request->all());
    }


    public function destroy($id)
    {
        $portfolio = Portfolio::findOrFail($id);
        //
        try {
            event(new PortfolioDelete());
            $portfolio->delete();
        } catch (Exception $e) {
            _info($e->getMessage());
        }
    }
}
