<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use MrShan0\PHPFirestore\FirestoreClient;
use MrShan0\PHPFirestore\FirestoreDocument;
use MrShan0\PHPFirestore\Fields\FirestoreArray;
use MrShan0\PHPFirestore\Fields\FirestoreObject;
use MrShan0\PHPFirestore\Fields\FirestoreGeoPoint;
use MrShan0\PHPFirestore\Fields\FirestoreReference;
use MrShan0\PHPFirestore\Fields\FirestoreTimestamp;
use DateTime;
use MrShan0\PHPFirestore\Attributes\FirestoreDeleteAttribute;

 






class FireStoreController extends Controller
{
    
//   use App\Helpers\ApiResponseHelper;


    public function updateOrInsertData(Request $request)
    {

         // Create a new instance of the Firestore client
        $firestoreClient = new FirestoreClient('noon-cc8ea', 'AIzaSyD1fFLxftZDf_-eOhraboycFkSQZEyR4UY', [
            'database' => '(default)',
        ]);;

         $collection = 'driver_orders/56/orders/1';

        $document = new FirestoreDocument;

        $document->setInteger('orders', '1');


        // $firestoreClient->updateDocument($collection, $document, true);
        $firestoreClient->setDocument($collection, '56', [
    'newFieldToAdd' => 'Jane Doe',
    'existingFieldToRemove' => new FirestoreDeleteAttribute
], [
    'exists' => true, // Indicate document must exist
]);

        return $this->setCode(200)->setData($firestoreClient)->setMessage('Driver Created Successfully')->send();
    }
}
