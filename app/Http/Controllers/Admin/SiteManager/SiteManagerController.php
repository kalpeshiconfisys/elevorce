<?php

namespace App\Http\Controllers\admin\SiteManager;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\SiteManager;
use App\Models\Site;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SiteManagerController extends Controller
{
    // List all site managers
    public function index($employer_id,Request $request)
    {

        $employer_id = base64_decode($employer_id);
        $redirectType =  base64_decode($request->query('type')) ?? 1;

        if ($employer_id == 0) {
            $hideSite = base64_decode($request->query('hide_site'));
            $sites = Site::where('id', $hideSite)->first();
            $siteManagers = SiteManager::where('employer_id',$sites->employer_id)->with('site')->paginate(10);
            $allSites = Site::where('employer_id', $sites->employer_id)->get();
        } else {
            $sites = Site::where('id', $employer_id)->first();
            if (!$sites) {
                return redirect()->back()->with('error', 'Site not found');
            }
            $allSites = Site::where('employer_id', $sites->employer_id)->get();
            $siteManagers = SiteManager::where('site_id', $employer_id)->paginate(10);
        }

        return view('admin.view.siteManager.index', compact('siteManagers', 'employer_id', 'allSites','redirectType'));
    }

    // Show form to create
    public function create($site_id)
    {
        $site = Site::where('id', $site_id)->first();
        $countries = Country::get();

        return view('admin.view.siteManager.create', compact('site','countries'));
    }

    // Store site manager
    public function store(Request $request)
    {


        if (SiteManager::where('email', $request->email)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Email already exists!');
        }

        if (SiteManager::where('number', $request->number)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Number already exists!');
        }

        $countries = Country::where('id', $request->country_code)->first();


        $input = [
            'employer_id' =>  $request->employer_id,
            'site_id'     => $request->site_id,
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'number'      => $request->number,
            'address'     => $request->address,
            'country_code' => $countries ? $countries->phone_code : null,
            'country' => $countries ? $countries->name : null,
        ];

        if ($request->hasFile('image')) {
            $imageName = time() . rand(1000, 9999) . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('images/site_manager'), $imageName);
            File::copy(public_path('images/site_manager/' . $imageName), public_path('images/site_manager/selfie/' . $imageName));
            $input['image'] = $imageName;
            $input['selfie'] = $imageName;
        }
        SiteManager::create($input);
        return redirect(url('admin/site-manager-list/' . $request->site_id))->with('success', 'Site Manager created successfully');
    }

    // Show one site manager
    public function show($id)
    {
        $siteManager = SiteManager::findOrFail($id);
        return view('admin.view.siteManager.show', compact('siteManager'));
    }

    // Edit form
    public function edit($id, $employer_id)
    {
        $siteManager = SiteManager::findOrFail($id);

        return view('admin.view.siteManager.edit', compact('siteManager', 'employer_id'));
    }

    // Update
    public function update(Request $request, $id)
    {
        $siteManager = SiteManager::findOrFail($id);

        if (SiteManager::where('email', $request->email)->where('id', '!=', $id)->whereNull('deleted_at')->exists()) {
            return redirect()->back()->with('error', 'Email already exists!');
        }

        if (SiteManager::where('number', $request->number)->where('id', '!=', $id)->whereNull('deleted_at')->exists()) {
            return redirect()->back()->with('error', 'Number already exists!');
        }
        $input = [
            'employer_id' => $request->employer_id,
            'site_id'     => $request->site_id,
            'name'        => $request->name,
            'email'       => $request->email,
            'number'      => $request->number,
            'address'     => $request->address,
        ];

        if (!empty($request->password)) {
            $input['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            $imageName = time() . rand(1000, 9999) . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('images/site_manager'), $imageName);
            File::copy(
                public_path('images/site_manager/' . $imageName),
                public_path('images/site_manager/selfie/' . $imageName)
            );
            $input['image']  = $imageName;
            $input['selfie'] = $imageName;
        }
        $siteManager->update($input);

        return redirect(url('admin/site-manager-list/' . $request->site_id))->with('success', 'Site Manager updated successfully');
    }


    // Delete
    public function destroy($id, $site_id)
    {
        $siteManager = SiteManager::findOrFail($id);


        if (File::exists(public_path('images/site_manager/' . $siteManager->image))) {
            File::delete(public_path('images/site_manager/' . $siteManager->image));
        }
        if (File::exists(public_path('images/site_manager/selfie/' . $siteManager->selfie))) {
            File::delete(public_path('images/site_manager/selfie/' . $siteManager->selfie));
        }


        $siteManager->delete();

        return redirect(url('admin/site-manager-list/' . $site_id))->with('success', 'Site Manager deleted successfully');
    }
}
