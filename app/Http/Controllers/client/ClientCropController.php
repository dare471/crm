<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientCrop;
use Illuminate\Http\Request;

class ClientCropController extends Controller
{
   
    public function index(Request $request)
    {
        $contacts = ClientCrop::where('id', $request->clientId)->get();
        return response()->json($contacts);
    }
    
    public function store(Request $request)
    {
        // Валидация входящих данных
        $validatedData = $request->validate([
            'clientId' => 'required|integer',
            'area' => 'required|numeric',
            'unit' => 'required|int',
            'culture' => 'required|string',
            'cultureId' => 'required|int',
            'activitySubstance' => 'required|int',
            // Добавьте другие поля и правила, если необходимо
        ]);

        // Создание нового ресурса с валидированными данными
        $contact = ClientCrop::create($validatedData);

        // Отправка ответа с данными нового ресурса и статус-кодом 201
        return response()->json($contact, 201);
    }

    public function show(Request $request)
    {
        $contact = ClientCrop::findOrFail($request->id);
        return response()->json($contact);
    }
   
    public function update(Request $request, $id)
    {
        $contact = ClientCrop::findOrFail($id);
        $contact->update($request->all());
        return response()->json($contact);
    }
   
    public function destroy(Request $request)
    {
        ClientCrop::findOrFail($request->id)->delete();
        return response()->json(null, 204); // 204 - код статуса для успешного выполнения без возвращения тела ответа
    }    
}
