<?php
namespace App\Http\Controllers;
use \GuzzleHttp\Client;
use \GuzzleHttp\Psr7;
use \GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
class WeatherController extends Controller
{
	private function getLatLon($zip) {
		//if we have cache, use it
		if (Cache::has('geo'.$zip)) {
			return Cache::get('geo'.$zip);
		}

		$client = new Client([
			'base_uri'=>'http://api.openweathermap.org/geo/1.0/',
			'timeout'=>5.0
		]);
		$response = $client->request('GET', 'zip', [
			'query' => [
				'zip' => $zip,
				'appid' => env('W_API_KEY')
			],
		]);
		$json =json_decode($response->getBody(),true);
		Cache::put('geo'.$zip,$json,$SECONDS=600);
		return $json;
	}

	/*
	 * route calls this code
	 * */
	public function getTemp(Request $request) {
		$zip = $request->zip;

		if (Cache::has('geo'.$zip)) {
			$status = "CACHE";
		}
		else {
			$status = "LIVE";
		}
		$latLon = $this->getLatLon($zip);
		$temp = $this->toDeg($latLon['lat'], $latLon['lon']);
		return response()->json([
			'temp'=>$temp,
			'city'=>$latLon['name'],
			'status' =>$status
		]);
	}

	private function toDeg($lat, $lon) {
		$client = new Client([
			'base_uri'=>'http://api.openweathermap.org/data/2.5/',
			'timeout'=>5.0
		]);
		$response = $client->request('GET', 'onecall' , [
			'query'=> [
				'lat' => $lat,
				'lon' => $lon,
				'exclude' => 'hourly,daily',
				'units' => 'imperial',
				'appid' => env('W_API_KEY')
			],
		]);
		$json = json_decode($response->getBody(),true);
		//$roomCollection =DB::select('select * from room');
		return $json['current']['temp'];
	}
/*
	public function show() {
		return view('room');
	}
*/
}
?>
