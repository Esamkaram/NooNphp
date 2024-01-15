<?php

namespace App\Http\Controllers\Api;

use Google\Type\DateTime;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use MrShan0\PHPFirestore\FirestoreClient;
use MrShan0\PHPFirestore\FirestoreDocument;
use MrShan0\PHPFirestore\Fields\FirestoreArray;
use MrShan0\PHPFirestore\Fields\FirestoreBytes;
use MrShan0\PHPFirestore\Fields\FirestoreObject;
use MrShan0\PHPFirestore\Fields\FirestoreGeoPoint;
use MrShan0\PHPFirestore\Fields\FirestoreReference;
use MrShan0\PHPFirestore\Fields\FirestoreTimestamp;
use MrShan0\PHPFirestore\Attributes\FirestoreDeleteAttribute;
// use MrShan0\PHPFirestore\Fields\FirestoreTimestamp;
// use MrShan0\PHPFirestore\Fields\FirestoreArray;
// use MrShan0\PHPFirestore\Fields\FirestoreBytes;
// use MrShan0\PHPFirestore\Fields\FirestoreGeoPoint;
// use MrShan0\PHPFirestore\Fields\FirestoreObject;
// use MrShan0\PHPFirestore\Fields\FirestoreReference;
// use MrShan0\PHPFirestore\Attributes\FirestoreDeleteAttribute;

class TestController extends Controller
{
    use ApiResponseHelper;
    public function order(Request $request)
    {

        $firestoreClient = new FirestoreClient('noon-cc8ea', '', [
            'database' => '(default)',
        ]);

        $collection = 'orders';

        $document = new FirestoreDocument;
        $document->setGeoPoint('client', new FirestoreGeoPoint($request->clientN, $request->clientE));
        $document->setString('customerName', $request->customerName);
        $document->setGeoPoint('delivery', new FirestoreGeoPoint($request->deliveryN, $request->deliveryE));
        $document->setReference('driver_ref', new FirestoreReference("/drivers" . '/' . $request->driver_ref));
        $document->setBoolean('isDelivery', $request->isDelivery);
        $document->setString('location', $request->location);
        $document->setString('orderNum', $request->orderNum);
        $document->setString('payType', $request->payType);
        $document->setString('status', $request->status);
        $document->setString('time', $request->time);
        $document->setString('total', $request->total);

        $firestoreClient->addDocument($collection, $document, $request->orderNum);

        return $this->setCode(200)->setData($firestoreClient)->setMessage('Order Created Successfully')->send();
    }

    public function driver(Request $request)
    {

        $firestoreClient = new FirestoreClient('noon-cc8ea', '', [
            'database' => '(default)',
        ]);

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
}
