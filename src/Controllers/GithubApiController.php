<?php

namespace Avxman\Github\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

/**
 *
 * Общий контроллер по работе с помощью API
 *
*/
abstract class GithubApiController extends Controller{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

}
