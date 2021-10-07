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
use Illuminate\Support\Facades\Redirect;
session_start();
class OAUTHController extends Controller
{
	//session_start();

	public function init(Request $req) {

		$_SESSION['code'] = $req ->code;

		if(isset($_SESSION['code'])) {
			if(isset($_SESSION['token'])) {
				return $this->getList($_SESSION['token']);
			}
			if(!isset($_SESSION['token'])) {
				//get the token
				$client = new Client([
					'base_uri' => 'https://todoist.com/oauth/access_token'
				]);
				$params = [
					'form_params' => [
						'client_id' => '8a46563becaf4d52b8fa0621cce63ac5',
						'client_secret' => '0afd754514394f1596198cd28515fd0d',
						'code' => $_SESSION['code'],
						'redirect_uri' => 'https://cerciaj.451.csi.miamioh.edu/cse451-cerciaj-web/laravel1-project/public/api/todoistoauth'
					]
				];
				$response = $client->request("POST","",$params);
				$token = json_decode($response->getBody());
				$_SESSION['token'] = $token->access_token;
				return $this->getList($_SESSION['token']);
			}
		}
		if(!isset($_SESSION['code'])) {
			$url ='https://todoist.com/oauth/authorize?client_id=8a46563becaf4d52b8fa0621cce63ac5&scope=data:read&state=thisisastring';
			return Redirect::to($url);
		}
		//return $_SESSION['token'];
	}

	public function auth(Request $req) {
		$code = $req -> code;

		return $code;
	}

	public function getList($token) {
		//$id = '2261008068';
		$token = $token;
		$client = new Client([
			'base_uri' => 'https://api.todoist.com/rest/v1/'
		]);
		$response = $client->request('GET','projects',
			['headers'=>['Authorization' => 'Bearer '.$token]]);
		$json = json_decode($response->getBody(),true);
		//$j = 0;
		//json_encode($todoist);
		return $json;
	}

	public function getProj(Request $req) {
		$id = $req->id;

		//$id = '2261008068';
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
				$todoist[$j] = $json[$i]['content']; $j++;
			}
		}
		json_encode($todoist);
		return $todoist;
	}
}
