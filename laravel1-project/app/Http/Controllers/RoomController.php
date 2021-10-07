<?php
/*
 * Scott Campbell
 * RoomController -> handle room requests
 * Edited for cse451 by Andrei Cerci 3/10/2021
 * */

namespace App\Http\Controllers;
use \GuzzleHttp\Client;
use \GuzzleHttp\Psr7;
use \GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
	/**
	 * Show a list of all of the Rooms
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$roomCollection = DB::select('select * from room');
		return view('room', ['roomCollection' => $roomCollection]);
	}

	public function getImage($building) {
		$client = new Client([
			'base_uri'=>'http://ws.miamioh.edu/api/building/v1/',
			'timeout'=>2.0
		]);
		try {
			$response = $client->request('GET',$building);
			$json = json_decode($response->getBody(),true);
			if( isset($json['data']['imageURL'])){
				return $json['data']['imageURL'];
			} else {
				return ' ';
			}
		} catch (RequestException $e) {
			return ' ';
		}
	}

	public function show()
	{
		return view('add');
	}
	public function add(Request $req) 
	{
		$isValid = Validator::make($req->all(), [
			'name'=>'required',
			'num' => 'required',
			'cap'=>'required',
			'desc'=>'required',
			'dept'=>'required'

		]);
		if ($isValid->fails()) {
			return view('add')->withErrors($isValid);
		}
		DB::table('room')-> insert([
			'buildingName' => $req->input('name'),
			'roomNum' => $req->input('num'),
			'capacity' => $req->input('cap'),
			'dept' => $req->input('dept'),
			'description' => $req->input('desc'),
			'image' => $this->getImage($req->input('name'))
		]);
		$message = 'Room added successfully!';
		$roomCollection = DB::select('select * from room');
		return view('room', ['roomCollection' => $roomCollection], ['message'=>$message]);

	}

	public function test(Request $req)
	{
		return view('test');
	}

	public function test2(Request $req) {
		$req->validate(['username'=>'required']); 

		return $req->input();
	}
}
