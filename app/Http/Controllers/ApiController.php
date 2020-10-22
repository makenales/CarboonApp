<?php

namespace App\Http\Controllers;

use App\Models\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function getCarbonFootprint(Request $request)
    {
        try {
            $client = new Client();
            $params = $request->all();
            //Validate required params
            $validator = Validator::make($request->all(), [
                            'activity' => ['required', 'numeric'],
                            'activityType' => ['required', 'string'],
                            'country' => ['required', 'string']
                        ]);

            //Check for optional parameters
            $validator->sometimes('fuelType', 'required', function ($input) {
                return $input->activityType === 'fuel';
            });

            //Check for optional parameters
            $validator->sometimes('mode', 'required', function ($input) {
                return $input->activityType === 'miles';
            });

            if ($validator->fails()) {
                throw new \Exception('Bad Request');
            }

            //After validation create params object with only required fields
            $params = $request->only([
                        'activity',
                        'activityType',
                        'country',
                        ($params['activityType'] === 'miles') ? 'mode' : 'fuelType'
                    ]);
            //Fetch record from DB for given params
            $carbon = Carbon::where($params)->whereDate('expires_at', '<', 'NOW()')->first();
            if ($carbon) {
                //We found previously saved value so sending it withour doing API call
                return array('carbonFootprint' => $carbon->carbonFootprint);
            } else {
                //Delete previsouly cached data as it is expired and no londer valid
                Carbon::where($params)->delete();
                //Call triptocarbon to get carbonFootprint
                $response = $client->request('GET',
                    'https://api.triptocarbon.xyz/v1/footprint',
                    [
                        'query' => $params
                    ]
                );
                $body = json_decode($response->getBody()->getContents(), true);
                //Save carbon value
                $params['carbonFootprint'] = $body['carbonFootprint'];
                //Set expire time in next 24 hours
                $params['expires_at'] = date("Y-m-d H:i:s", strtotime('+24 hours'));
                Carbon::create($params);
                return $body;
            }
        } catch(\Exception $ex) {
            return response()->json(array('message' => 'Bad Request'), 400);
        }
    }
}