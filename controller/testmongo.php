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

	public function Action_Testuser() {
		$user = new Model_User(['_id' => 1]);
		$user->name = 'y1'.rand(0,99);
		var_dump($user);
		$user->save();
	}

	public function Action_Testusers() {
		$users = new Model_Users();
		$cursor = $users->find()->limit(2)->sort(['name' => 1]);

		echo "<hr> all users: " . $cursor->count();
		echo "<br> limited users: " . $cursor->count(true);

		foreach($cursor->load() as $user) {
			echo "<hr>" . $user->name;
		}
	}

	public function Action_Testuserids() {
		$users = new Model_Users();
		foreach($users->byIds([2,3,5]) as $user) {
			echo "<hr>" . $user->name;
		}
	}
}
?>