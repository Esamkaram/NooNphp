<?php

namespace App\Http\Controllers\Api;

use Google\Type\DateTime;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Factory;
use MrShan0\PHPFirestore\FirestoreClient;
use MrShan0\PHPFirestore\FirestoreDocument;
use MrShan0\PHPFirestore\Fields\FirestoreArray;
use MrShan0\PHPFirestore\Fields\FirestoreBytes;
use MrShan0\PHPFirestore\Fields\FirestoreObject;
use MrShan0\PHPFirestore\Fields\FirestoreGeoPoint;
use MrShan0\PHPFirestore\Fields\FirestoreReference;
use MrShan0\PHPFirestore\Fields\FirestoreTimestamp;
use MrShan0\PHPFirestore\Attributes\FirestoreDeleteAttribute;

 use MrShan0\PHPFirestore\Collection;

class TestController extends Controller
{
    use ApiResponseHelper;
    public function order(Request $request)
    {
        // dd('ww');

        $firestoreClient = new FirestoreClient('noon-cc8ea', 'AIzaSyD1fFLxftZDf_-eOhraboycFkSQZEyR4UY', [
            'database' => '(default)',
        ]);

        $collection = 'orders';

         $isDelivery = $request->isDelivery === 'true' ? true : false;

        $document = new FirestoreDocument;
        $document->setGeoPoint('client', new FirestoreGeoPoint($request->clientN, $request->clientE));
        $document->setString('customerName', $request->customerName);
        $document->setGeoPoint('delivery', new FirestoreGeoPoint($request->deliveryN, $request->deliveryE));
        $document->setReference('driver_ref', new FirestoreReference("/drivers" . '/' . $request->driver_ref));
        $document->setBoolean('isDelivery', $isDelivery);
        $document->setString('location', $request->location);
        $document->setString('orderNum', $request->orderNum);
        $document->setString('payType', $request->payType);
        $document->setString('status', $request->status);
        $document->setString('time', $request->time);
        $document->setString('total', $request->total);


        $arr = [];
        foreach ($request->productsList as $key => $value) {
            // dd($value);
            array_push(
                $arr,
                new FirestoreObject(
                    [
                        'image' => $value['image'],
                        'name' => $value['name'],
                        'qty' => $value['qty'],
                    ]
                )
            );
        }

        $document->setArray('productsList', new FirestoreArray($arr));


        $firestoreClient->addDocument($collection, $document, $request->orderNum);

        return $this->setCode(200)->setData($firestoreClient)->setMessage('Order Created Successfully')->send();
    }

    public function driver(Request $request)
    {

        $firestoreClient = new FirestoreClient('noon-cc8ea', 'AIzaSyD1fFLxftZDf_-eOhraboycFkSQZEyR4UY', [
            'database' => '(default)',
        ]);;

        $collection = 'drivers';

        $document = new FirestoreDocument;

        $document->setString('car', $request->car);
        $document->setGeoPoint('delivery', new FirestoreGeoPoint($request->deliveryN, $request->deliveryE));
        $document->setString('id', $request->id);
        $document->setString('image', $request->image);
        $document->setString('name', $request->name);
        $document->setInteger('unReadNum', $request->unReadNum);


        $firestoreClient->addDocument($collection, $document, $request->id);

        return $this->setCode(200)->setData($firestoreClient)->setMessage('Driver Created Successfully')->send();
    }

    public function updateRef(Request $request)
    {

        $collection = 'orders';

        $firestoreClient = new FirestoreClient('noon-cc8ea', 'AIzaSyD1fFLxftZDf_-eOhraboycFkSQZEyR4UY', [
            'database' => '(default)',
        ]);;

        $documentPath = $collection . '/' . $request->order_id;

        try {
            $firestoreClient->updateDocument($documentPath, [
                'driver_ref' => new FirestoreReference("/drivers" . '/' . $request->driver_id),
            ], true);
            return $this->setCode(200)->setData([])->setMessage('Driver updated successfully.')->send();
        } catch (\Exception $e) {
            return $this->setCode(401)->setData([])->setMessage($e->getMessage())->send();
        }
    }



       public function updateOrInsertData(Request $request)
    {
  $client = new FirestoreClient('noon-cc8ea', 'AIzaSyD1fFLxftZDf_-eOhraboycFkSQZEyR4UY', [
            'database' => '(default)',
        ]);;


        // Create a new document in the parent collection
        $parentCollection = 'driver_orders';
        $parentDocumentId = '1';

        $client->collection($parentCollection)->document($parentDocumentId)->set([
            'field1' => 'value1',
            'field2' => 'value2',
        ]);

        // Create a new document in the subcollection
        $subCollection = 'parent_collection/' . $parentDocumentId . '/subcollection';
        $subDocumentId = 'orders';

        $client->collection($subCollection)->document($subDocumentId)->set([
            'sub_field1' => 'sub_value1',
            'sub_field2' => 'sub_value2',
        ]);


        return $this->setCode(200)->setData([])->setMessage('driver_orders Created Successfully')->send();
    }

 public function index()
    {
        return  $this->setCode(200)->setData([])->setMessage('Hello Form Noon')->send();
    }

    public function updateDriverOrderStatus(Request $request)
    {
        $data = $request->all();

        $factory = (new Factory())->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
        $firestore = $factory->createFirestore();
        $database = $firestore->database();

        $tripData = [[
            'path' => 'status',
            'value' => $data['status'],
        ]];

        if (!empty($data['driver_id'])) {
            if (in_array($data['status'], ['delivered', 'unDelivered', 'unRecived', 'unReceived', 'recived', 'received'])) {
                $database->collection('driver_orders')->document($data['driver_id'])
                    ->collection('orders')->document($data['order_id'])
                    ->delete();
            } else {
                $firebaseData = [
                    'status' => $data['status'],
                    'order_id' => $database->collection('orders')->document($data['order_id']),
                ];
                $database->collection('driver_orders')->document($data['driver_id'])
                    ->collection('orders')->document($data['order_id'])->set($firebaseData);


                $tripData[] = [
                    'path' => 'driver_ref',
                    'value' => $database->collection('drivers')->document($data['driver_id']),
                ];
            }
        }
        $collection = $database->collection('orders');
        $document = $collection->document($data['order_id']);

        $document->update($tripData);

        return $this->setCode(200)->setData([])->setMessage('Status Updated Successfully')->send();
    }
}


