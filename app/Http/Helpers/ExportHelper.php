<?php

namespace App\Http\Helpers;

use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Models\Export;
use Illuminate\Support\Facades\Request;

class ExportHelper
{

    /**
     * Get all exports
     * @param $request
     * @return Export[]|Collection [Object]
     */
    public function all($request = [])
    {
        $exports = Export::query();

        $exports = $exports->with('format')->with('language');

        if(!empty($request['project_id'])){
        	$exports = $exports->where('project_id', $request['project_id']);
        }

        $exports = $exports->orderBy('id')->get();

        foreach ($exports as $export) {
            $duration = (!empty($export->duration)) ? explode(':', $export->duration) : [];
            $resolution = (!empty($export->format_raw)) ? explode('x', $export->format_raw) : [];
            $export->hours = $duration[0] ?? '00';
            $export->minutes = $duration[1] ?? '00';
            $export->seconds = $duration[2] ?? '00';
            $export->width = $resolution[0] ?? '00';
            $export->height = $resolution[1] ?? '00';
        }

        return $exports;
    }

    /**
     * Get export details
     * @param  [Integer] $id Export id
     * @return Export|Builder|Model|object|null [Object]
     */
    public function get($id)
    {
        return Export::where('id', $id)->first();
    }

    /**
     * Delete multiple exports
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function delete($ids)
    {
    		foreach($ids as $id){
	        $export = Export::find($id);

	        try {
	          $export->delete();
	        } catch (Exception $e) {
	          _info($e->getMessage());
	        }
	      }

        return true;
    }

    /**
     * Add or update a export
     * @param
     * @return mixed [Object] Export
     */
    public function addOrUpdate($params)
    {
        $savedExports = [];

        $project = $params['project'];
        foreach ($params['exports'] as $exportParams) {
            $savedExports[] = $this->saveExport($project, $exportParams, (int)$params['user'][0]);
        }

        return $savedExports;
    }

    // -- Useful

    private function saveExport($project, $exportParams, $user = null)
    {
        $newExport = false;
        $mandatoryParameters = [];

        // Check if all parameters are provided
        try {
            _helper('api')->checkParameters($exportParams, $mandatoryParameters);
        } catch (Exception $e) {
            // Return the exception message if error
            _helper('api')->error($e->getMessage());
        }

        if (isset($exportParams['id']) && $exportParams['id']) {
            $export = Export::find($exportParams['id']);
            $export->updated_by = $user;
        } else {
            $export = new Export();
            $export->created_by = $user;
    		$export->updated_by = $user;
            $newExport = true;
        }

        $export->name = $exportParams['name'] ?? "";
        if (!empty($exportParams['hours']) && !empty($exportParams['minutes']) && !empty($exportParams['seconds'])) {
            $export->duration = $exportParams['hours'] . ':' . $exportParams['minutes'] . ':' . $exportParams['seconds'];
        } else {
            $export->duration = '';
        }
        $export->project_id = $project;
       $export->format_id = !empty($exportParams['format']) ? $exportParams['format']['id'] : null;
         if (!empty($exportParams['width']) && !empty($exportParams['height'])) {
            $export->format_raw = $exportParams['width'] . 'x' . $exportParams['height'];
        } else {
            $export->format_raw = "??:??:??";
        }
        $export->language_id = $exportParams['language']['id'] ?? "";
        $export->notes = $exportParams['notes'] ?? "";

        try {
            $export->save();
        } catch (Exception $e) {
            _helper('api')->error($e->getMessage());
        }

        if ($newExport) {
            return $export->id;
        }

        return false;
    }

    public function updateStatus($params)
    {
        $exportId = $params['exportId'] ?? false;
        $isClosed = $params['status'] ?? false;
        if ($exportId) {
            $export = Export::findOrFail($exportId);
            if ($export) {
                $export->is_closed = !$isClosed;

                try {
                    $export->save();
                    return true;
                } catch (Exception $e) {
                    _helper('api')->error($e->getMessage());
                }
            }
        }

        return false;
    }
}
