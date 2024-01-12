<?php

namespace App\Http\Helpers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

use App\Models\Job;

/**
 * Class JobHelper
 * @package App\Http\Helpers
 */
class JobHelper
{
    /**
     * Retrieve all jobs
     *
     * @param null $term
     * @return \Illuminate\Support\Collection
     */
    public function all($term = null)
    {
        if ($term) {
            return Job::where('name', 'LIKE', '%' . $term . '%')->orderBy('name', 'ASC')->get();
        } else {
            $jobs = Job::orderBy('name', 'ASC')->get();
            foreach ($jobs as $key => $job) {
                $jobs[$key]->name = __('job.' . $jobs[$key]->name);
            }
            return $jobs;
        }
    }

    /**
     * [add description]
     * @param [type] $name [description]
     */
    public function add($name)
    {
    	$job = new Job;
    	$job->name = $name;
    	$job->save();

    	return $job;
    }

    /**
     * [delete description]
     * @param [type] $id [description]
     */
    public function delete($id)
    {
    	$job = Job::find($id);

    	if($job){
    		$job->delete();
    	}

    	return 'Job deleted';
    }

}
