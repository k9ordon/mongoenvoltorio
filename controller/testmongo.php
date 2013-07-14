<?

class Controller_TestMongo extends Controller {

	public function __construct() {
	}

	public function Action_Testitem() {
		// create
		$user = new Model_MongoItem('users', ['name' => 'generic user ' . rand(0,1000)]);
		$user->save();
		print_r($user);

		// update
		$user = new Model_MongoItem('users', ['_id' => 1]);
		$user->name = 'New Username';
		$user->save();
		print_r($user);
	}
}
?>