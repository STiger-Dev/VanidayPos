<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\Restaurant\Booking;
use App\User;
use App\Utils\Util;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\RestaurantUtil;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;

class BookingController extends ApiController
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $restUtil;

    public function __construct(Util $commonUtil, RestaurantUtil $restUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $business_id = $user->business_id;
            $user_id = $user->id;

            $input = $request->input();
            $booking_start = $input['booking_start'];
            $booking_end = $input['booking_end'];
            $date_range = [$booking_start, $booking_end];

            //Check if booking is available for the required input
            $query = Booking::where('business_id', $business_id)
                            ->where('location_id', $input['location_id'])
                            ->where('contact_id', $input['contact_id'])
                            ->where(function ($q) use ($date_range) {
                                $q->whereBetween('booking_start', $date_range)
                                ->orWhereBetween('booking_end', $date_range);
                            });

            if (isset($input['res_table_id'])) {
                $query->where('table_id', $input['res_table_id']);
            }
            
            $existing_booking = $query->first();
            if (empty($existing_booking)) {
                $input['business_id'] = $business_id;
                $input['created_by'] = $user_id;
                $input['booking_start'] = $booking_start;
                $input['booking_end'] = $booking_end;
                $booking = Booking::createBooking($input);
                
                $output = ['success' => 1,
                    'msg' => trans("lang_v1.added_success"),
                    'data'  =>  $booking
                ];
            } else {
                $time_range = $this->commonUtil->format_date($existing_booking->booking_start, true) . ' ~ ' .
                                $this->commonUtil->format_date($existing_booking->booking_end, true);

                $output = ['success' => 0,
                        'msg' => trans(
                            "restaurant.booking_not_available",
                            ['customer_name' => $existing_booking->customer->name,
                            'booking_time_range' => $time_range]
                        )
                    ];
            }

            return new CommonResource($output);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];

            return $this->otherExceptions($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;
        $booking = Booking::where('business_id', $business_id)
                            ->where('id', $id)
                            ->with(['table', 'customer', 'correspondent', 'waiter', 'location'])
                            ->first();
        if (!empty($booking)) {
            $booking_start = $this->commonUtil->format_date($booking->booking_start, true);
            $booking_end = $this->commonUtil->format_date($booking->booking_end, true);

            $booking_statuses = [
                'waiting' => __('lang_v1.waiting'),
                'booked' => __('restaurant.booked'),
                'completed' => __('restaurant.completed'),
                'cancelled' => __('restaurant.cancelled'),
            ];
            return view('restaurant.booking.show', compact('booking', 'booking_start', 'booking_end', 'booking_statuses'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $business_id = $user->business_id;
            $booking = Booking::where('business_id', $business_id)
                                ->find($id);
            $input = $request->only(['location_id', 'contact_id', 'correspondent', 'res_waiter_id', 'booking_start', 'booking_end', 'booking_note']);
            
            if (!empty($booking)) {
                $booking = Booking::where('business_id', $business_id)
                            ->where('id', $id)
                            ->update($input);
            }

            $output = ['success' => 1,
                            'msg' => trans("lang_v1.updated_success"),
                            'data'  =>  $booking
                        ];
        
            return new CommonResource($output);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
            return $this->otherExceptions($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $business_id = $user->business_id;
            $booking = Booking::where('business_id', $business_id)
                                ->where('id', $id)
                                ->delete();
            $output = ['success' => 1,
                            'msg' => trans("lang_v1.deleted_success")
                        ];

            return new CommonResource($output);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
            return $this->otherExceptions($e);
        }
    }
}
