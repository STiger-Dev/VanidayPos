<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Modules\Connector\Transformers\CommonResource;
use Illuminate\Support\Facades\DB;
use App\Utils\ModuleUtil;

use Modules\Connector\Transformers\BusinessLocationResource;

use App\BusinessLocation;

/**
 * @group Business Location management
 * @authenticated
 *
 * APIs for managing business locations
 */
class BusinessLocationController extends ApiController
{
    /**
     * List business locations
     * @response {
            "data": [
                {
                    "id": 1,
                    "business_id": 1,
                    "location_id": null,
                    "name": "Awesome Shop",
                    "landmark": "Linking Street",
                    "country": "USA",
                    "state": "Arizona",
                    "city": "Phoenix",
                    "zip_code": "85001",
                    "invoice_scheme_id": 1,
                    "invoice_layout_id": 1,
                    "selling_price_group_id": null,
                    "print_receipt_on_invoice": 1,
                    "receipt_printer_type": "browser",
                    "printer_id": null,
                    "mobile": null,
                    "alternate_number": null,
                    "email": null,
                    "website": null,
                    "featured_products": [
                        "5",
                        "71"
                    ],
                    "is_active": 1,
                    "payment_methods": [
                        {
                            "name": "cash",
                            "label": "Cash",
                            "account_id": "1"
                        },
                        {
                            "name": "card",
                            "label": "Card",
                            "account_id": null
                        },
                        {
                            "name": "cheque",
                            "label": "Cheque",
                            "account_id": null
                        },
                        {
                            "name": "bank_transfer",
                            "label": "Bank Transfer",
                            "account_id": null
                        },
                        {
                            "name": "other",
                            "label": "Other",
                            "account_id": null
                        },
                        {
                            "name": "custom_pay_1",
                            "label": "Custom Payment 1",
                            "account_id": null
                        },
                        {
                            "name": "custom_pay_2",
                            "label": "Custom Payment 2",
                            "account_id": null
                        },
                        {
                            "name": "custom_pay_3",
                            "label": "Custom Payment 3",
                            "account_id": null
                        }
                    ],
                    "custom_field1": null,
                    "custom_field2": null,
                    "custom_field3": null,
                    "custom_field4": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:20",
                    "updated_at": "2020-06-05 00:56:54"
                }
            ]
        }
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $permitted_locations = $user->permitted_locations($business_id);
        
        $query = BusinessLocation::where('business_id', $business_id);

        if ($permitted_locations != 'all') {
            $query->whereIn('id', $permitted_locations);
        }
        $business_locations = $query->Active()->get();

        return BusinessLocationResource::collection($business_locations);
    }

    /**
     * Get the specified business location
     * 
     * @urlParam location required  comma separated ids of the business location Example: 1
     * @response {
            "data": [
                {
                    "id": 1,
                    "business_id": 1,
                    "location_id": null,
                    "name": "Awesome Shop",
                    "landmark": "Linking Street",
                    "country": "USA",
                    "state": "Arizona",
                    "city": "Phoenix",
                    "zip_code": "85001",
                    "invoice_scheme_id": 1,
                    "invoice_layout_id": 1,
                    "selling_price_group_id": null,
                    "print_receipt_on_invoice": 1,
                    "receipt_printer_type": "browser",
                    "printer_id": null,
                    "mobile": null,
                    "alternate_number": null,
                    "email": null,
                    "website": null,
                    "featured_products": [
                        "5",
                        "71"
                    ],
                    "is_active": 1,
                    "payment_methods": [
                        {
                            "name": "cash",
                            "label": "Cash",
                            "account_id": "1"
                        },
                        {
                            "name": "card",
                            "label": "Card",
                            "account_id": null
                        },
                        {
                            "name": "cheque",
                            "label": "Cheque",
                            "account_id": null
                        },
                        {
                            "name": "bank_transfer",
                            "label": "Bank Transfer",
                            "account_id": null
                        },
                        {
                            "name": "other",
                            "label": "Other",
                            "account_id": null
                        },
                        {
                            "name": "custom_pay_1",
                            "label": "Custom Payment 1",
                            "account_id": null
                        },
                        {
                            "name": "custom_pay_2",
                            "label": "Custom Payment 2",
                            "account_id": null
                        },
                        {
                            "name": "custom_pay_3",
                            "label": "Custom Payment 3",
                            "account_id": null
                        }
                    ],
                    "custom_field1": null,
                    "custom_field2": null,
                    "custom_field3": null,
                    "custom_field4": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:20",
                    "updated_at": "2020-06-05 00:56:54"
                }
            ]
        }
     */
    public function show($location_ids)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $location_ids = explode(',', $location_ids);

        $locations = BusinessLocation::where('business_id', $business_id)
                        ->whereIn('id', $location_ids)
                        ->get();

        return BusinessLocationResource::collection($locations);
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

            $input = $request->only(['name', 'landmark', 'city', 'state', 'country', 'zip_code', 'invoice_scheme_id',
                'invoice_layout_id', 'mobile', 'alternate_number', 'email', 'website', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'location_id', 'selling_price_group_id', 'default_payment_accounts', 'featured_products', 'sale_invoice_layout_id']);

            $input['business_id'] = $business_id;

            $input['default_payment_accounts'] = !empty($input['default_payment_accounts']) ? json_encode($input['default_payment_accounts']) : null;

            $moduleUtil = new ModuleUtil();
            //Update reference count
            $ref_count = $moduleUtil->setAndGetReferenceCount('business_location', $business_id);

            if (empty($input['location_id'])) {
                $input['location_id'] = $moduleUtil->generateReferenceNumber('business_location', $ref_count);
            }
            if (!isset($input['invoice_scheme_id'])) {
                $input['invoice_scheme_id'] = 1;
            }
            if (!isset($input['invoice_layout_id'])) {
                $input['invoice_layout_id'] = 1;
            }
            if (!isset($input['sale_invoice_layout_id'])) {
                $input['sale_invoice_layout_id'] = 1;
            }
            if (!isset($input['selling_price_group_id'])) {
                $input['selling_price_group_id'] = 1;
            }

            DB::beginTransaction();

            $location = BusinessLocation::create($input);

            //Create a new permission related to the created location
            Permission::create(['name' => 'location.' . $location->id ]);

            DB::commit();
            $output = ['success' => true,
                        'data' =>  $location,
                        'msg' => __("business.business_location_added_success")
                      ];
            
            return new CommonResource($output);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            return $this->otherExceptions($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->only(['name', 'landmark', 'city', 'state', 'country',
                'zip_code', 'invoice_scheme_id',
                'invoice_layout_id', 'mobile', 'alternate_number', 'email', 'website', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'location_id', 'selling_price_group_id', 'default_payment_accounts', 'featured_products', 'sale_invoice_layout_id']);
            
            $user = Auth::user();
            $business_id = $user->business_id;

            $input['default_payment_accounts'] = !empty($input['default_payment_accounts']) ? json_encode($input['default_payment_accounts']) : null;

            $input['featured_products'] = !empty($input['featured_products']) ? json_encode($input['featured_products']) : null;

            $location = BusinessLocation::where('business_id', $business_id)
                            ->where('id', $id)
                            ->update($input);

            $output = ['success' => true,
                       'data'   =>  $location,
                       'msg' => __('business.business_location_updated_success')
            ];

            new CommonResource($output);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            return $this->otherExceptions($e);
        }

        return $output;
    }

    /**
     * Delete Contact
     *
     * @urlParam required id of the contact to be deleted
     * 
     */
    public function destroy($id)
    {
        try {
            $business_id = Auth::user()->business_id;

            $business_location = BusinessLocation::where('business_id', $business_id)
                            ->findOrFail($id);

            $business_location->is_active = !$business_location->is_active;
            $business_location->save();

            $msg = $business_location->is_active ? __('lang_v1.business_location_activated_successfully') : __('lang_v1.business_location_deactivated_successfully');

            $output = ['success' => true,
                            'msg' => $msg
                        ];

            return new CommonResource($output);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return $this->otherExceptions($e);
        }
    }
}
