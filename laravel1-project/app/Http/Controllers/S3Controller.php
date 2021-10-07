<?php
#require "./vendor/autoload.php";
namespace App\Http\Controllers; 
use App\Http\Controllers\Controller;
use \Aws\S3\S3Client;
use \Aws\Exception\AwsException;
use \Aws\Credentials\CredentialProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class S3Controller extends Controller {
	public function getTxt () {
		$profile = 'class';
		$path = '/var/www/.aws/credentials';

		$provider = CredentialProvider::ini($profile, $path);
		$provider = CredentialProvider::memoize($provider);

		$s3Client = new S3Client([
			'region' => 'us-east-2',
			'version' => '2006-03-01',
			'credentials' => $provider
		]);

		header("content-type: text/plain");
		$bucket = "cse451-s21-web";
		$folder = "cerciaj";
		$key = "cerciaj/information";

		try {
			$result = $s3Client->getObject([
				'Bucket' => $bucket,
				//'Folder' => $folder,
				'Key' => $key
			]);
			$msg = $result['Body']->getContents();
			return $msg;

		} catch (S3Exception $e) {
			echo $e-getMessage().PHP_EOL;
		}
	}
	public function putTxt(Request $req) {

		$profile = 'class';
                $path = '/var/www/.aws/credentials';

                $provider = CredentialProvider::ini($profile, $path);
                $provider = CredentialProvider::memoize($provider);

		$s3Client = new S3Client([
                        'region' => 'us-east-2',
                        'version' => '2006-03-01',
                        'credentials' => $provider
                ]);

		$bucket = "cse451-s21-web";
		try {
			$result = $s3Client->putObject([
				'Bucket' => $bucket,
				'Key' => 'cerciaj/information',
				'Body' => $req->msg,
				'ContentType' => 'text/plain'
			]);
			$status = array('status' => 'OK');
			json_encode($status);
			return view('/s3', ['msg' => 'OK']);
		} catch (S3Exception $e) {
			var_dump($e);
			$status = array('status' => 'OK');
                        json_encode($status);
                        return $status;
		}
		var_dump($result);
	}
}

?>




