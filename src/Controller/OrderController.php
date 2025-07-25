<?php

namespace App\Controller;

use App\Dto\RequestDto\Order\OrderChangeStatusRequestDto;
use App\Dto\RequestDto\Order\OrderCreateRequestDto;
use App\Dto\ResponseDto\Order\OrderResponseDto;
use App\Entity\Order;
use App\Exception\UnknownEnumTypeException;
use App\Inspectors\OrderInspector;
use App\Service\Basket\BasketServiceInterface;
use App\Service\Order\OrderService;
use App\Service\Payment\PaymentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class OrderController extends AbstractController
{
    public function __construct(
        private readonly BasketServiceInterface $basketService,
        private readonly OrderService $orderService,
        private readonly OrderInspector $orderInspector,
        private readonly PaymentServiceInterface $paymentService,
    )
    {
    }

    /**
     * @throws UnknownEnumTypeException
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/order', name: 'order.create', methods: ['POST'])]
    public function createOrder(
        #[MapRequestPayload]
        OrderCreateRequestDto $requestDto
    ): JsonResponse
    {
        $user = $this->getUser();

        $basket = $this->basketService->getBasket($user->getId());

        $order = $this->orderService->createOrder($requestDto, $basket);

        return new JsonResponse(
            data: new OrderResponseDto(
                id: $order->getId(),
                createdAt: $order->getCreatedAt(),
                payedAt: $order->getPayedAt(),
                totalPrice: $order->getTotalPrice(),
                orderStatus: $order->getOrderStatus(),
                deliveryType: $order->getDeliveryType(),
                userId: $order->getUserId(),
            ),
            status: Response::HTTP_CREATED,
        );
    }

    /**
     * @throws UnknownEnumTypeException
     */
    #[Route(path: '/api/order/{id}/change-status', name: 'order.update', methods: ['PUT'])]
    public function changeOrderStatus(
        Order $order,
        #[MapRequestPayload]
        OrderChangeStatusRequestDto $requestDto
    ): JsonResponse
    {
        if (!$this->orderInspector->canChangeAdminOrderStatus()) {
            return new JsonResponse(
                [
                    'message' => "You cannot change order status. You hasn't role admin",
                ],
                Response::HTTP_FORBIDDEN,
            );
        }

        $order = $this->orderService->changeOrderStatus($order, $requestDto->orderStatus);

        return new JsonResponse(
            data: new OrderResponseDto(
                id: $order->getId(),
                createdAt: $order->getCreatedAt(),
                payedAt: $order->getPayedAt(),
                totalPrice: $order->getTotalPrice(),
                orderStatus: $order->getOrderStatus(),
                deliveryType: $order->getDeliveryType(),
                userId: $order->getUserId(),
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(path: '/api/order/{id}/pay', name: 'order.pay', methods: ['PUT'])]
    public function payOrder(Order $order): RedirectResponse|JsonResponse
    {
        $user = $this->getUser();

        if (!$this->orderInspector->canPay($user, $order)) {
            return new JsonResponse(
                [
                    'message' => "You cannot pay this order. This isnt your order",
                ],
                Response::HTTP_FORBIDDEN,
            );
        }

        $paymentUrl = $this->paymentService->createPaymentUrlForOrder($user->getId(), $order);

        return $this->redirect($paymentUrl);
    }
}
