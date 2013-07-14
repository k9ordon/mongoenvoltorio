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

		// remove
		$user = new Model_MongoItem('users', ['_id' => 4]);
		$user->remove();
	}

	public function Action_Testusers() {
		$users = new Model_Users();
		foreach($users->find()->toModels() as $user) {
			echo "<hr>" . $user->name;
		}
	}
}
?>