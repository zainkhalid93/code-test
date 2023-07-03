<?php

namespace DTApi\Repository;
use Illuminate\Http\Request;

interface BookingInterface
{
    public function getUsersJobs($user_id);
    public function getUsersJobsHistory($user_id, Request $request);
    public function getPotentialJobIdsWithUserId($user_id);

}