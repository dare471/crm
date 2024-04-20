<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientVisit;
use Illuminate\Http\Request;

class ClientVisitController extends Controller
{
   
    public function index(Request $request)
    {
        $contacts = ClientVisit::where('clientId', $request->clientId)->get();
        return response()->json($contacts);
    }
    
    public function store(Request $request)
    {
        // Валидация входящих данных
        $validatedData = $request->validate([
            'clientId' => 'required',
            'createdBy' => 'required',
            'plannedTime' => 'required|date',
            'startTime' => 'required|date',
            'finishTime' => 'required|date',
            'contactPersonId' => 'required',
            'placeMeetingId' => 'required',
            'placeMeetingDescription' => 'required|string',
            'purposeOfMeeting' => 'required|string',
            'purposeOfMeetingDescription' => 'required|string',
        ]);

        // Создание нового ресурса с валидированными данными
        $contact = ClientVisit::create($validatedData);

        // Отправка ответа с данными нового ресурса и статус-кодом 201
        return response()->json($contact, 201);
    }

    public function show(Request $request)
    {
        $contact = ClientVisit::findOrFail($request->id);
        return response()->json($contact);
    }
   
    public function update(Request $request, $id)
    {
        $contact = ClientVisit::findOrFail($id);
        $contact->update($request->all());
        return response()->json($contact);
    }
   
    public function destroy(Request $request)
    {
        ClientVisit::findOrFail($request->id)->delete();
        return response()->json(null, 204); // 204 - код статуса для успешного выполнения без возвращения тела ответа
    }    
}
