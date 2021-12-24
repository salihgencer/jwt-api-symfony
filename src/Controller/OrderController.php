<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/orders", name="order_list", methods={"GET", "HEAD"})
     */
    public function allOrders(OrderRepository $orderRepository): Response
    {
        $response = [
            'status' => false,
            'message' => '',
            'data' => [],
            'count' => 0
        ];

        $userId = $this->getUser()->getId();
        $queryData = ['userId' => $userId];
        $orderList = $orderRepository->findBy($queryData);

        if(!empty($orderList)){
            $response['status'] = true;
            $response['message'] = 'All orders are listed.';
            $response['data'] = $orderList;
            $response['count'] = count($orderList);
        }
        else{
            $response['message'] = 'Order not found.';
        }

        return $this->json($response);
    }

    /**
     * @Route("/api/orders/{orderId}", name="get_order_detail", methods={"GET", "HEAD"})
     */
    public function getOrderDetail(int $orderId, OrderRepository $orderRepository): Response
    {
        $response = [
            'status' => false,
            'message' => '',
            'data' => [],
        ];

        $id = $orderId;
        $userId = $this->getUser()->getId();
        $queryData = ['id' => $id, 'userId' => $userId];

        $orderDetail = $orderRepository->findOneBy($queryData);

        if(!is_null($orderDetail)){
            $response['status'] = true;
            $response['message'] = 'Your order details have been brought.';
            $response['data'] = $orderDetail;
        }
        else {
            $response['message'] = 'Order not found.';
        }

        return $this->json($response);
    }

    /**
     * @Route("/api/orders", name="create_order", methods={"POST"})
     */
    public function createOrder(Request $request, OrderRepository $orderRepository): Response
    {
        $response = [
            'status' => false,
            'message' => '',
            'data' => [],
        ];

        $parameter = json_decode($request->getContent(), true);

        $orderCode = $parameter['orderCode'] ?? null;
        $productId = $parameter['productId'] ?? null;
        $quantity = $parameter['quantity'] ?? null;
        $address = $parameter['address'] ?? null;


        if (empty($orderCode) || empty($productId) || empty($quantity) || empty($address)){
            $response['message'] = "Invalid Order Code or Product Id or Quantity, or Address";
            return $this->json($response);
        }

        $orderData = [
            "orderCode" => $orderCode,
            "productId" => $productId,
            "quantity" => $quantity,
            "address" => $address,
            'userId' => $this->getUser()->getId(),
        ];

        $order = $orderRepository->create($orderData);

        if(!is_null($order)){
            $response['status'] = true;
            $response['message'] = "The new order has been successfully created.";
            $response['data'] = $order;
            $statusCode = 201;
        }
        else {
            $response['message'] = "Failed to create new order.";
            $statusCode = 200;
        }

        return $this->json($response, $statusCode);
    }

    /**
     * @Route("/api/orders/{orderId}", name="update_order", methods={"PUT"})
     */
    public function update(int $orderId, Request $request, OrderRepository $orderRepository): Response
    {
        $response = [
            'status' => false,
            'message' => '',
        ];

        $parameter = json_decode($request->getContent(), true);

        $orderCode = $parameter['orderCode'] ?? null;
        $productId = $parameter['productId'] ?? null;
        $quantity = $parameter['quantity'] ?? null;
        $address = $parameter['address'] ?? null;
        $shippingDate = $parameter['shippingDate'] ?? null;

        $id = $orderId;
        $userId = $this->getUser()->getId();
        $queryData = ['id' => $id, 'userId' => $userId];

        if (is_null($orderRepository->findOneBy($queryData))){
            $response['message'] = 'Order not found.';
            return $this->json($response, 404);
        }

        if (!is_null($orderRepository->find($orderId)->getShippingDate())) {
            $response['message'] = 'This order has been shipped. You cannot update.';
            return $this->json($response);
        }

        $orderData = [
            "orderCode" => trim($orderCode),
            "productId" => trim($productId),
            "quantity" => trim($quantity),
            "address" => trim($address),
            "shippingDate" => trim($shippingDate),
        ];

        $updateRes = $orderRepository->update($orderId, $orderData);


        if($updateRes){
            $response['status'] = true;
            $response['message'] = 'Your order has been successfully updated.';
        }
        else {
            $response['message'] = 'Order update could not be done.';
        }

        return $this->json($response);
    }

}
