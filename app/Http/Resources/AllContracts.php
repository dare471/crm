<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllContracts extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "type" => "managerContracts",
            "managerID" => $this->managerID, // ID менеджера
            "managerName" => $this->managerName, //ФИО менеджера
            "direction" => $this->direction, // Дирекция менеджера
            "managerTitle" => $this->managerTitle, // Должность Менеджера
            "clientId" => $this->clientID, // ID клиента
            "clientName" => $this->contracteClient, // Название клиента
            "contractId" => $this->contractsId, // ID договора
            "contractName" => $this->contractName, // Название Договора
            "clientIin" => $this->clientIin, // ИИН клиента
            "season" => $this->season, // Сезон договора
            "paymentTerms" => $this->termsOfPayment, // Первоначальный взнос
            "deliveryMethod" => $this->deliveryMethod, // Вид Доставки
            "deliveryAddress" => $this->deliveryAddress, // Адресс доставки
            "sumContracts" => $this->sumContracts, // Сумма Договора
        ];
    }
}
