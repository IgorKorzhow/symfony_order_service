<?php

namespace App\Controller;

use App\Dto\OrderDto;
use App\Entity\Order;
use App\Enum\OrderStatusEnum;
use App\Exception\DtoValidationException;
use App\Exception\UnknownEnumTypeException;
use App\Exception\ValidationException;
use App\Inspectors\OrderInspector;
use App\Service\Basket\BasketServiceInterface;
use App\Service\Order\OrderService;
use App\Service\Payment\PaymentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class OrderController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly BasketServiceInterface $basketService,
        private readonly OrderService $orderService,
        private readonly OrderInspector $orderInspector,
        private readonly PaymentServiceInterface $paymentService,
    )
    {
    }

    /**
     * @throws DtoValidationException
     * @throws UnknownEnumTypeException
     * @throws ExceptionInterface
     */
    #[Route(path: '/order', name: 'order.create', methods: ['POST'])]
    public function createOrder(OrderDto $orderDto, ValidatorInterface $validator): JsonResponse
    {
        $user = $this->getUser();

        $basket = $this->basketService->getBasket($user);

        $orderDto->setBasket($basket);
        $orderDto->setUserId($user->getUserIdentifier());
        $orderDto->validate($validator);

        $order = $this->orderService->createOrder($orderDto);

        return new JsonResponse(
            $this->serializer->serialize($order, 'json'),
            Response::HTTP_CREATED,
        );
    }

    /**
     * @throws UnknownEnumTypeException
     * @throws ValidationException
     * @throws ExceptionInterface
     */
    #[Route(path: '/order/{id}', name: 'order.update', methods: ['PUT'])]
    public function changeOrderStatus(Order $order, Request $request): JsonResponse
    {
        $orderStatus = $request->get('order_status');

        if (!isset($orderStatusRaw) || !OrderStatusEnum::hasValue($orderStatus)) {
            throw new ValidationException('Order status is missing or invalid');
        }

        if (!$this->orderInspector->canChangeAdminOrderStatus()) {
            return new JsonResponse(
                [
                    'message' => "You cannot change order status. You hasn't role admin",
                ],
                Response::HTTP_FORBIDDEN,
            );
        }

        $order = $this->orderService->changeOrderStatus($order, $orderStatus);

        return new JsonResponse(
            $this->serializer->serialize($order, 'json'),
            Response::HTTP_OK,
        );
    }

    #[Route(path: '/order/{id}/pay', name: 'order.pay', methods: ['PUT'])]
    public function payOrder(Order $order): RedirectResponse|JsonResponse
    {
        $user = $this->getUser();

        if (!$this->orderInspector->canPay($user, $order)) {
            return new JsonResponse(
                [
                    'message' => "You cannot change order status. You hasn't role admin",
                ],
                Response::HTTP_FORBIDDEN,
            );
        }

        $paymentUrl = $this->paymentService->createPaymentUrlForOrder($user->getUserIdentifier(), $order);

        return $this->redirect($paymentUrl);
    }
}
