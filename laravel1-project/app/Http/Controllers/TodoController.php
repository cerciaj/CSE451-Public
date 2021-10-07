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

class TodoController extends Controller
{

	public function getList() {
		$id = '2261008068';
		$token = env('TODO_API_KEY');
		$client = new Client([
			'base_uri' => 'https://api.todoist.com/rest/v1/'
		]);
		$response = $client->request('GET','tasks',
			['headers'=>['Authorization' => 'Bearer '.$token]]);
		$json = json_decode($response->getBody(),true);
		$j = 0;
		for($i = 0; $i < count($json); $i++) {
			if ($json[$i]['project_id'] == $id) {
				$todoist[$j] = $json[$i]['content'];
				$j++;
			}
		}
		json_encode($todoist);
		return $todoist;
	}

}
