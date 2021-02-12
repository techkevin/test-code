<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Hobby;
use App\Models\User;
use DB;
use Str;
use Storage;

class UserController extends Controller
{
    //For List User Page
    public function index()
    {
        return view('user.index');
    }

    //For Get Data List User (Ajax)
    public function getList()
    {
        $result = User::with(['category', 'hobbies.hobby'])->latest()->paginate(5);

        return response()->json($result);
    }

    //For Get Data List Category (Ajax)
    public function getCategories()
    {
        $result = Category::get();

        return response()->json([
            'data' => $result,
        ]);
    }

    //For Get Data List Hobby (Ajax)
    public function getHobbies()
    {
        $result = Hobby::get();

        return response()->json([
            'data' => $result,
        ]);
    }

    //For Save Data User (Ajax)
    public function store()
    {
        $this->validation();
        DB::transaction(function () {
            $filename = Str::random(6) . time() . '.' . request()->file('photo')->extension();

            request()->file('photo')->storeAs(User::FOLDER_NAME_PHOTO, $filename, 'public_uploads');

            $user = User::create([
                'name' => request()->input('name'),
                'contact_number' => request()->input('contact_number'),
                'category_id' => request()->input('category_id'),
                'photo' => $filename,
            ]);

            $hobbies = collect(request()->input('hobbies'))->map(function ($hobby) {
                return [
                    'hobby_id' => $hobby,
                ];
            });

            $user->hobbies()->createMany($hobbies);
        });

        return response()->json([
            'message' => 'Insert User Successfully',
        ]);
    }

    //For Update Data User (Ajax)
    public function update($id)
    {
        $this->validation($id);
        DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);
            $user->name = request()->input('name');
            $user->contact_number = request()->input('contact_number');
            $user->category_id = request()->input('category_id');
            if (request()->hasFile('photo')) {
                //Delete Old Photo
                Storage::disk('public_uploads')->delete(User::FOLDER_NAME_PHOTO . '/' . $user->photo);

                $filename = Str::random(6) . time() . '.' . request()->file('photo')->extension();
                request()->file('photo')->storeAs(User::FOLDER_NAME_PHOTO, $filename, 'public_uploads');
                $user->photo = $filename;
            }
            $user->save();

            $hobbies = collect(request()->input('hobbies'))->map(function ($hobby) {
                return [
                    'hobby_id' => $hobby,
                ];
            });

            $user->hobbies()->delete();
            $user->hobbies()->createMany($hobbies);
        });

        return response()->json([
            'message' => 'Update User Successfully',
        ]);
    }

    //For Delete Data User (Ajax)
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        //Delete Photo
        Storage::disk('public_uploads')->delete(User::FOLDER_NAME_PHOTO . '/' . $user->photo);

        $user->delete();

        return response()->json([
            'message' => 'Delete User Successfully',
        ]);
    }

    //For Bulk Delete Data User (Ajax)
    public function bulkDestroy()
    {
        request()->validate([
           'ids' => 'required|array',
           'ids' => 'required|exists:users,id', 
        ]);

        //Delete Photos
        $users = User::whereIn('id', request()->input('ids'));
        foreach ($users->get() ?: [] as $user) {
            Storage::disk('public_uploads')->delete(User::FOLDER_NAME_PHOTO . '/' . $user->photo);
        }

        $users->delete();

        return response()->json([
            'message' => 'Bulk Delete User Successfully',
        ]);
    }

    //For Check validation Data User when insert and update
    private function validation($id = null)
    {
        request()->validate([
            'name' => 'required',
            'contact_number' => 'required',
            'hobbies' => 'required|array',
            'hobbies.*' => 'required|exists:hobbies,id',
            'category_id' => 'required|exists:categories,id',
            'photo' => is_null($id) ? 'required|mimes:jpg,png,jpeg' : 'nullable',
        ]);
    }
}
