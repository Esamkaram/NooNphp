<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use Illuminate\Support\Facades\Session;

class CrudController extends Controller
{

    use ApiResponseHelper;
    public function index()
    {
        //
        $students = app('firebase.firestore')->database()->collection('User')->documents();
        return view('Crud/index', compact('students'));
        // return dd($students);
    }


    public function order(Request $request)
    {
        $request->validate([
            'client' => 'required',
            'customerName' => 'required',
            'delivery' => 'required',
            'driver_ref' => 'required',
            'isDelivery' => 'required',
            'location' => 'required',
            'orderNum' => 'required',
            'payType' => 'required',
            'status' => 'required',
            'time' => 'required',
            'total' => 'required',
        ]);
        // dd($request->all());
        $stuRef = app('firebase.firestore')->database()->collection('orders_test')->Document($request->orderNum);
        $order = $stuRef->set($request->all());
        return $this->setCode(200)->setData($order)->setMessage('Order Created Successfully')->send();
    }

    public function driver(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'car' => 'required',
            'image' => 'required',
            'name' => 'required',
            'unReadNum' => 'required',
        ]);
        $stuRef = app('firebase.firestore')->database()->collection('drivers_test')->Document($request->id);
        $order = $stuRef->set($request->all());
        return $this->setCode(200)->setData($order)->setMessage('Driver Created Successfully')->send();
    }





    public function update(Request $request, $id)
    {
        //
        $student = app('firebase.firestore')->database()->collection('User')->document($id)
            ->update([
                ['path' => 'firstname', 'value' => $request->firstname],
                ['path' => 'lastname', 'value' => $request->lastname],
                ['path' => 'age', 'value' => $request->age],
            ]);
        return back();
    }


    public function destroy($id)
    {
        //
        app('firebase.firestore')->database()->collection('User')->document($id)->delete();
        return back();
    }
}
