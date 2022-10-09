<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\RoleTranslation;

class RoleController extends Controller
{
    
    public function index()
    {
        $roles = Role::paginate(10);
        return view('backend.staff.staff_roles.index', compact('roles'));
    }

   
    public function create()
    {
        return view('backend.staff.staff_roles.create');
    }

    
    public function store(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('This action is disabled in demo mode'))->error();
            return back();
        }
        if($request->has('permissions')){
            $role = new Role;
            $role->name = $request->name;
            $role->permissions = json_encode($request->permissions);
            $role->save();

            $role_translation = RoleTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'role_id' => $role->id]);
            $role_translation->name = $request->name;
            $role_translation->save();

            flash(translate('Role has been inserted successfully'))->success();
            return redirect()->route('roles.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();

    }

    
    public function show($id)
    {
        
    }

  
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $role = Role::findOrFail($id);
        return view('backend.staff.staff_roles.edit', compact('role','lang'));
    }

   
    public function update(Request $request, $id)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('This action is disabled in demo mode'))->error();
            return back();
        }
        $role = Role::findOrFail($id);

        if($request->has('permissions')){
            if($request->lang == env("DEFAULT_LANGUAGE")){
                $role->name = $request->name;
            }
            $role->permissions = json_encode($request->permissions);
            $role->save();

            $role_translation = RoleTranslation::firstOrNew(['lang' => $request->lang, 'role_id' => $role->id]);
            $role_translation->name = $request->name;
            $role_translation->save();

            flash(translate('Role has been updated successfully'))->success();
            return redirect()->route('roles.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

  
    public function destroy($id)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('This action is disabled in demo mode'))->error();
            return back();
        }
        $role = Role::findOrFail($id);
        foreach ($role->role_translations as $key => $role_translation) {
            $role_translation->delete();
        }

        Role::destroy($id);
        flash(translate('Role has been deleted successfully'))->success();
        return redirect()->route('roles.index');
    }
}
