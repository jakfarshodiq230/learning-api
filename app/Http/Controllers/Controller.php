<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="APIs Untuk E-Learning Kampus",
 *     version="1.0.0",
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="scantum",
 * ),
 */
abstract class Controller
{
    //
}
