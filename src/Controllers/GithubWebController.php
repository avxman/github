<?php

namespace Avxman\Github\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;


/**
 *
 * Общий контроллер по работе с помощью Web
 *
*/
abstract class GithubWebController extends Controller{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

}
