<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\Addon;
use Illuminate\Support\Str;
use ZipArchive;
use Storage;
use Cache;
use DB;
use Redirect;

class AddonController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:manage_addons'])->only('index', 'create');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addons = Addon::query()->orderBy('name', 'asc')->get();
        return view('backend.addons.index', compact('addons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.addons.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Cache::forget('addons');

        if (env('DEMO_MODE') == 'On') {
            flash(translate('This action is disabled in demo mode'))->error();
            return back();
        }

        if (class_exists('ZipArchive')) {
            if ($request->hasFile('addon_zip')) {
                
                if (! self::isLocalhostDomain()) {
                    $result = self::check_activation($request);

                    if(isset($result) && $result !== true){
                        flash($result == false ? 'Please use the same purchase key that you have registered' : $result )->warning();
                        return back();
                    }
                }

                // Create update directory.
                $dir = 'addons';
                if (!is_dir($dir))
                    mkdir($dir, 0777, true);

                $path = Storage::disk('local')->put('addons', $request->addon_zip);

                $zipped_file_name = $request->addon_zip->getClientOriginalName();

                //Unzip uploaded update file and remove zip file.
                $zip = new ZipArchive;
                $res = $zip->open(base_path('public/' . $path));

                $random_dir = Str::random(10);

                $dir = trim($zip->getNameIndex(0), '/');

                if ($res === true) {
                    $res = $zip->extractTo(base_path('temp/' . $random_dir . '/addons'));
                    $zip->close();
                } else {
                    dd('could not open');
                }

                $str = file_get_contents(base_path('temp/' . $random_dir . '/addons/' . $dir . '/config.json'));
                $json = json_decode($str, true);

                //dd($random_dir, $json);

                if (BusinessSetting::where('type', 'current_version')->first()->value >= $json['minimum_item_version']) {
                    if (count(Addon::where('unique_identifier', $json['unique_identifier'])->get()) == 0) {
                        $addon = new Addon;
                        $addon->name = $json['name'];
                        $addon->unique_identifier = $json['unique_identifier'];
                        $addon->version = $json['version'];
                        $addon->activated = 1;
                        $addon->image = $json['addon_banner'];
                        $addon->purchase_code = $request->purchase_code;
                        $addon->save();

                        // Create new directories.
                        if (!empty($json['directory'])) {
                            //dd($json['directory'][0]['name']);
                            foreach ($json['directory'][0]['name'] as $directory) {
                                if (is_dir(base_path($directory)) == false) {
                                    mkdir(base_path($directory), 0777, true);
                                } else {
                                    echo "error on creating directory";
                                }
                            }
                        }

                        // Create/Replace new files.
                        if (!empty($json['files'])) {
                            foreach ($json['files'] as $file) {
                                copy(base_path('temp/' . $random_dir . '/' . $file['root_directory']), base_path($file['update_directory']));
                            }
                        }
                        // Create/Replace new folders.
                        if (!empty($json['folders'])) {
                            foreach ($json['folders'] as $folder) {
                                $sourceFolder = base_path('temp/' . $random_dir . '/' . $folder['root_directory']);
                                $destinationFolder = base_path($folder['update_directory']);
                                
                                // Copy the folder recursively
                                $this->copyFolder($sourceFolder, $destinationFolder);
                            }
                        }
                        // Run sql modifications
                        $sql_path = base_path('temp/' . $random_dir . '/addons/' . $dir . '/sql/update.sql');
                        if (file_exists($sql_path)) {
                            DB::unprepared(file_get_contents($sql_path));
                        }

                        flash(translate('Addon installed successfully'))->success();
                        return redirect()->route('addons.index');
                    } else {
                        $addon = Addon::where('unique_identifier', $json['unique_identifier'])->first();

                        if ($json['unique_identifier'] == 'delivery_boy' && $addon->version < 3.3) {
                            $dir = base_path('resources/views/delivery_boys');
                            foreach (glob($dir . "/*.*") as $filename) {
                                if (is_file($filename)) {
                                    unlink($filename);
                                }
                            }
                        }

                        // Create new directories.
                        if (!empty($json['directory'])) {
                            //dd($json['directory'][0]['name']);
                            foreach ($json['directory'][0]['name'] as $directory) {
                                if (is_dir(base_path($directory)) == false) {
                                    mkdir(base_path($directory), 0777, true);
                                } else {
                                    echo "error on creating directory";
                                }
                            }
                        }

                        // Create/Replace new files.
                        if (!empty($json['files'])) {
                            foreach ($json['files'] as $file) {
                                copy(base_path('temp/' . $random_dir . '/' . $file['root_directory']), base_path($file['update_directory']));
                            }
                        }
                        // Create/Replace new folders.
                        if (!empty($json['folders'])) {
                            foreach ($json['folders'] as $folder) {
                                $sourceFolder = base_path('temp/' . $random_dir . '/' . $folder['root_directory']);
                                $destinationFolder = base_path($folder['update_directory']);
                                // Copy the folder recursively
                                $this->copyFolder($sourceFolder, $destinationFolder);
                            }
                        }

                        for ($i = $addon->version + 0.05; $i <= $json['version']; $i = $i + 0.1) {
                            // Run sql modifications
                            $sql_version = $i + 0.05;
                            $sql_path = base_path('temp/' . $random_dir . '/addons/' . $dir . '/sql/' . $sql_version . '.sql');
                            if (file_exists($sql_path)) {
                                DB::unprepared(file_get_contents($sql_path));
                            }
                        }

                        $addon->version = $json['version'];
                        $addon->name = $json['name'];
                        $addon->image = $json['addon_banner'];
                        $addon->purchase_code = $request->purchase_code;
                        $addon->save();

                        flash(translate('This addon is updated successfully'))->success();
                        return redirect()->route('addons.index');
                    }
                } else {
                    flash(translate('This version is not capable of installing Addons, Please update.'))->error();
                    return redirect()->route('addons.index');
                }
            }
        } else {
            flash(translate('Please enable ZipArchive extension.'))->error();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Addon $addon
     * @return \Illuminate\Http\Response
     */
    public function show(Addon $addon)
    {
        //
    }

    public function list()
    {
        //return view('backend.'.Auth::user()->role.'.addon.list')->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Addon $addon
     * @return \Illuminate\Http\Response
     */
    public function edit(Addon $addon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Addon $addon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Addon $addon
     * @return \Illuminate\Http\Response
     */
    public function activation(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('This action is disabled in demo mode'))->error();
            return 0;
        }
        $addon = Addon::find($request->id);
        $addon->activated = $request->status;
        $addon->save();

        Cache::forget('addons');

        return 1;
    }

    public function check_activation( $data){
		return true;
    }

    protected static function checkVerification( $type, $key){

        $res  = self::script_activation_check($key);
        return $res;
    }

    protected static function checkActivation( $type, $key){

        if($type == 'item'){
            $url = env('ACTIVATION_URL', 'https://activation.activeitzone.com/item_info/').$key;
        }else{
            $url = env('REGISTERED_ADDON_URL', 'https://activation.activeitzone.com/registered-addon-info/').$key;
        }
        $res = self::sendRequest( $url);
        return $res ? true : false;
    }


    protected static function sendRequest( $url) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    protected static function script_activation_check( $purchase_code) {
        $url = env('VERIFY_PURCHASE_URL', 'https://activeitzone.com/activation/verify-purchase-code/').$purchase_code;
        $request_data_json = json_encode(['code' => $purchase_code]);

        $header = array(
            'Content-Type:application/json'
        );
        $stream = curl_init();

        curl_setopt($stream, CURLOPT_URL, $url);
        curl_setopt($stream, CURLOPT_HTTPHEADER, $header);
        curl_setopt($stream, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($stream, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($stream, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($stream, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($stream, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $rn = curl_exec($stream);
        curl_close($stream);
        return $rn;
    }


    protected static function check_registered_addon($purchase_code) {
        $url = env('REGISTERED_ADDON_LIST_URL', 'https://activation.activeitzone.com/registered-addon-list/').$purchase_code;

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }


    protected static function normalizeDomain($domain){
            $domain = preg_replace('/^https?:\/\//', '', $domain);
            $domain = preg_replace('/^www\./', '', $domain);
            $parts = explode('.', $domain);
            $count = count($parts);
            if ($count > 2) {
                $domain = $parts[$count - 2] . '.' . $parts[$count - 1];
            }

        return $domain;
    }

    protected static function isLocalhostDomain() {
        if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
            return true;
        }
        return false;
    }

    public function copyFolder($source, $destination) {
        if (!is_dir($source)) {
            return false;
        }
    
        // Create the destination directory if it doesn't exist
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }
    
        $directory = opendir($source);
    
        while (($file = readdir($directory)) !== false) {
            if ($file === '.' || $file === '..') {
                continue; // Skip current and parent directory pointers
            }
    
            $srcPath = $source . DIRECTORY_SEPARATOR . $file;
            $destPath = $destination . DIRECTORY_SEPARATOR . $file;
    
            if (is_dir($srcPath)) {
                // Recursively copy subdirectory
                $this->copyFolder($srcPath, $destPath);
            } else {
                // Copy file
                copy($srcPath, $destPath);
            }
        }
    
        closedir($directory);
        return true;
    }

}
