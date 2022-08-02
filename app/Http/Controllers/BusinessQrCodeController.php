<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use chillerlan\QRCode\{QRCode, QROptions};
use Modules\Essentials\Entities\EssentialsAttendance;
use App\BusinessLocation;
use App\User;
use App\Utils\ModuleUtil;

class BusinessQrCodeController extends Controller
{
    private $provider;

    protected $moduleUtil;

    public function __construct(
        ModuleUtil $moduleUtil
    )
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     *
     * @param  Request $request, $event
     * @return \Illuminate\Http\Response
     */
    protected function showQrCode(Request $request, $qrcode_info)
    {
        $qrcode_json = base64_decode($qrcode_info);
        $qrcode_info = json_decode($qrcode_json, true);
        $qrcode_url = route('business.employeeAttendance', $qrcode_info['location_id']);
        $qrcode_img = (new QRCode)->render($qrcode_url);
        
        return view('business.show_business_qrcode', compact('qrcode_info', 'qrcode_img'));
    }
    
    /**
     *
     * @param  Request $request, $event
     * @return \Illuminate\Http\Response
     */
    protected function employeeAttendance(Request $request, $location_id)
    {
        $employee_id = \Cookie::get('employee_id');
        $location_info = BusinessLocation::select('*')->where('id', $location_id)->first()->toArray();
        $business_id = $location_info['business_id'];
        $clock_in = array();
        $employee_info = array();

        if (!empty($employee_id)) {
            $clock_in = EssentialsAttendance::where('business_id', $business_id)
                                ->where('user_id', auth()->user()->id)
                                ->whereNull('clock_out_time')
                                ->first();
            $employee_info = User::where('id', $employee_id)->first();
        }
        
        return view('business.employee_attendance', compact('clock_in', 'location_id', 'employee_info', 'location_info'));
    }

     /**
     *
     * @param  Request $request, $event
     * @return \Illuminate\Http\Response
     */
    protected function submitAttendance(Request $request)
    {
        try {
            $input = $request->only(['latitude', 'longitude', 'employee_id', 'location_id', 'is_clock_in', 'attendance_id']);
            $locationInfo = BusinessLocation::select('business_id')->where('id', $input['location_id'])->first()->toArray();
            $business_id = $locationInfo['business_id'];
            
            if ($input['employee_id']) {
                \Cookie::queue('employee_id', $input['employee_id'], 525600);
            }

            $geoLocation = [
                $input['latitude'],
                $input['longitude']
            ];
            $address = $this->getUserLocation($geoLocation);

            if ($input['is_clock_in']) {
                $essentials_attendance_data = array(
                    'user_id'    =>  $input['employee_id'],
                    'location_id'    =>  $input['location_id'],
                    'clock_in_time' =>  date("Y-m-d H:i:s"),
                    'ip_address'    =>  $request->ip(),
                    'business_id'   =>  $business_id,
                    'clock_in_location'  =>  $address
                );

                $result = EssentialsAttendance::create($essentials_attendance_data);
                $message = __("essentials::lang.clock_out_success");
            } else {
                $id = $input['attendance_id'];

                $essentials_attendance_data = array(
                    'clock_out_location'  =>  $address,
                    'clock_out_time' =>  date("Y-m-d H:i:s")
                );

                $result = EssentialsAttendance::where('id', $id)->update($essentials_attendance_data);
                $message = __("essentials::lang.clock_in_success");
            }

            $output = [
                'success' => true,
                'data'    => $result,
                'msg' => $message
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    protected function registerEmployeeId(Request $request)
    {
        $input = $request->only(['employee_id']);
        $employee_id = $input['employee_id'];

        $employee_info = User::where('id', $employee_id)->first();

        if (isset($employee_info['id'])) {
            \Cookie::queue('employee_id', $employee_id, 525600);

            $output = [
                'success' => true,
                'data'    => $employee_info,
                'msg' => __("essentials::lang.register_employee_success")
            ];
        } else {
            $output = [
                'success' => false,
                'data'    => $employee_info,
                'msg' => __("essentials::lang.register_employee_not_member")
            ];
        }

        return $output;
    }

    public function getUserLocation($latlng)
    {
        $response = $this->moduleUtil->getLocationFromCoordinates($latlng[0], $latlng[1]);

        return $response;
    }
}
