<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends BaseApiController
{
    /**
     * @OA\Post(
     *     path="/api/v1/webhook/speedbots",
     *     tags={"Webhook"},
     *     summary="Receive WhatsApp delivery status callbacks from Speedbots",
     *     description="Speedbots calls this endpoint to report whether a WhatsApp message was delivered or failed. No auth required — secured by hospital token in payload.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone",        type="string",  example="919994780436"),
     *             @OA\Property(property="flow_id",      type="string",  example="1774503294935"),
     *             @OA\Property(property="status",       type="string",  enum={"delivered","failed","read"}, example="delivered"),
     *             @OA\Property(property="hospital_token", type="string", description="The hospital's Speedbots API token to identify which hospital this is for")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Webhook received"),
     *     @OA\Response(response=400, description="Invalid payload")
     * )
     */
    public function speedbots(Request $request)
    {
        $payload = $request->all();

        Log::channel('hospital_admin')->info('Speedbots webhook received', $payload);

        $phone         = $payload['phone']          ?? null;
        $status        = $payload['status']          ?? null;
        $flowId        = $payload['flow_id']         ?? null;
        $hospitalToken = $payload['hospital_token']  ?? null;

        if (!$phone || !$status) {
            return response()->json(['error' => 'Missing phone or status'], 400);
        }

        // Find hospital by token
        $hospital = null;
        if ($hospitalToken) {
            $hospital = DB::table('hospitals')->where('token', $hospitalToken)->first();
        }

        // Update wa_status on the most recent matching booking
        $query = DB::table('bookings')
            ->where('patient_phone', $phone)
            ->whereIn('status', ['pending', 'accepted', 'rescheduled']);

        if ($hospital) {
            $query->where('hospital_id', $hospital->id);
        }

        $updated = $query->latest('created_at')
            ->limit(1)
            ->update([
                'wa_status'    => $status,
                'wa_sent_at'   => now(),
                'updated_at'   => now(),
            ]);

        return response()->json([
            'received' => true,
            'updated'  => $updated,
        ]);
    }
}