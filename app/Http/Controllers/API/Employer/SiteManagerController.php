<?php

namespace App\Http\Controllers\API\Employer;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SiteManagerController extends Controller
{
    public function pySiteManagerList(){
        $employee = SiteManager::select('id','selfie')->get();
        return $this->success(true, 'SiteManager get successfully.',$employee);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:site_managers,email',
            'password' => 'required',
            'number'   => 'required|unique:site_managers,number',
            'image'    => 'required||mimes:jpg,jpeg,png,gif|max:2048',
            'site_id'  => 'required|exists:sites,id',
            'address'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $input = [
            'employer_id' => auth()->user()->id,
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'number' => $request->number,
            'site_id' => $request->site_id,
            'address' => $request->address,
            'country' => $request->country,
            'country_code' => $request->country_code,
        ];

        if ($request->hasFile('image')) {
            $imageName = time() . rand(1000, 9999) . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('images/site_manager'), $imageName);
            File::copy(public_path('images/site_manager/' . $imageName), public_path('images/site_manager/selfie/' . $imageName));
            $input['image'] = $imageName;
            $input['selfie'] = $imageName;
        }

        $user = SiteManager::create($input);

        $site = Site::with('siteManager')->where('id',$user->site_id)->first();
        $site['total_employee'] = 0;

        return $this->success(true, 'Site Manager created successfully.', $site);
    }

    public function deleteSiteManager($id){
        $siteManager = SiteManager::withTrashed()->find($id);

        if (!$siteManager) {
            return $this->error('Site Manager not found.');
        }
        $siteManager->forceDelete();
        return $this->success(true, 'Site Manager deleted successfully.');
    }
}
