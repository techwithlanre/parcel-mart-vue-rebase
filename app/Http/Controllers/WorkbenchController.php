<?php
namespace App\Http\Controllers;

use App\Models\AramexCities;
use App\Models\Country;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Octw\Aramex\Aramex;

class WorkbenchController extends Controller
{
    public function index()
    {
        $date = new DateTime();
        $date->modify('+1 weekday');
        $shipment_date = $date->format('Y-m-d 10:00:00');
        echo $shipment_date = str_replace(' ', 'T', $shipment_date) . " GMT+01:00";
    }

    public function render()
    {
        $offset = 0;

        if (isset($_POST['user_input_box']) && isset($_POST['search']) && isset($_POST['replace'])) {

            $user_input_box = $_POST['user_input_box'];
            $search = $_POST['search'];
            $replace = $_POST['replace'];

            //To check whether above code work or not

            //  echo $user_input_box = $_POST['user_input_box'];
            //  echo $search = $_POST['search'];
            //  echo $replace = $_POST['replace'];

            $search_length = strlen($search);

            if (!empty($_POST['user_input_box']) && !empty($_POST['search']) && !empty($_POST['replace'])) {

                while ($string_position = strpos($user_input_box, $search, $offset)) {
                    // echo $string_position."<br>";
                    // echo $offset = $string_position + $search_length;

                    $offset = $string_position + $search_length;
                    $user_input_box = substr_replace($user_input_box, $replace, $string_position, $search_length);
                }
            }
            echo $user_input_box;
        }

        else{
            echo "Please fill the complete fields.";
        }

        echo '<form method="POST" action="#">
    
    <textarea cols="34" rows="12" name="user_input_box"></textarea>
    <br><br>
    <label>Search For: </label> <br>
    <input type="text" name="search"><br><br>
    <label>Replace With: </label><br>
    <input type="text" name="replace"><br><br>
    <input type="submit" value="Find and Replace">
 </form>';
    }

    public function aramexCountries()
    {
        set_time_limit(92000000000);
        $countries = Country::skip(40)->take(20)->get();
        foreach ($countries as $country) {
            $response = Aramex::fetchCities($country->iso2);
            if (!isset($response->error)) {
                $cities = $response->Cities->string;
                foreach ($cities as $city) {
                    AramexCities::updateOrCreate([
                        'name' => $city,'country_code' => $country->iso2],[
                        'country_id' => $country->id,
                    ]);
                }
            }
        }
    }
}
