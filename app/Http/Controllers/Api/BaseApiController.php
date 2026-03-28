<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="Medical Booking System API",
 *     version="1.0.0",
 *     description="REST API for the Medical Booking System.",
 *     @OA\Contact(email="admin@yourhospital.com")
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Local server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="API Token",
 *     description="Your api_code from the users table. Pass as: Authorization: Bearer {token}"
 * )
 *
 * @OA\Tag(name="Auth",     description="Authentication")
 * @OA\Tag(name="Bookings", description="Booking management")
 * @OA\Tag(name="Doctors",  description="Doctor management")
 * @OA\Tag(name="Patients", description="Patient records")
 * @OA\Tag(name="Webhook",  description="Speedbots webhooks")
 *
 * @OA\PathItem(path="/api/v1")
 */
class BaseApiController extends Controller
{
    //
}